<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Role;
use App\Models\Madrasa;
use App\Models\Classes;
use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\Division;
use App\Models\District;
use App\Models\Upazila;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = User::with([
            'role',
            'madrasa',
            'student'
        ]);

        if (!$authUser->is_super_admin) {

            // Same madrasa users only
            $query->where('madrasa_id', $authUser->madrasa_id);

            /*
            |--------------------------------------------------------------------------
            | Madrasa Admin Restrictions
            |--------------------------------------------------------------------------
            */

            if ($authUser->is_madrasa_admin) {

                $query->whereHas('role', function ($q) {

                    $q->whereNotIn('slug', [
                        'super-admin',
                        'soft-admin',
                    ]);
                });
            }
        }

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_bn', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('institution_user_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('user_type')) {

            if ($request->user_type == 'student') {
                $query->whereHas('student');
            } else {

                $query->whereHas('role', function ($q) use ($request) {
                    $q->where('slug', $request->user_type);
                });
            }
        }

        $users = $query
            ->latest()
            ->paginate(10);

        $divisions = Division::all();

        return view('admin.users.index', compact(
            'users',
            'divisions'
        ));
    }

    public function create()
    {
        $authUser = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | কে কোন ইউজার তৈরি করতে পারবে
        |--------------------------------------------------------------------------
        */

        $userTypes = [

            'student'  => 'শিক্ষার্থী',
            'teacher'  => 'শিক্ষক',
            'guardian' => 'অভিভাবক',
        ];

        /*
        |--------------------------------------------------------------------------
        | Soft Admin -> Madrasa Admin তৈরি করতে পারবে
        |--------------------------------------------------------------------------
        */

        if ($authUser->is_soft_admin) {

            $userTypes['madrasa-admin'] = 'মাদ্রাসা অ্যাডমিন';
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin -> Soft Admin তৈরি করতে পারবে
        |--------------------------------------------------------------------------
        */

        if ($authUser->is_super_admin) {

            $userTypes['soft-admin'] = 'সফট অ্যাডমিন';
        }

        /*
        |--------------------------------------------------------------------------
        | Madrasa List
        |--------------------------------------------------------------------------
        */

        if ($authUser->is_super_admin) {

            $madrasas = Madrasa::where('status', 1)->get();

        } else {

            $madrasas = Madrasa::where('id', $authUser->madrasa_id)->get();
        }

        $roles = Role::all();

        $divisions = Division::with([
            'districts.upazilas'
        ])->get();

        /*
        |--------------------------------------------------------------------------
        | Institution ID Preview
        |--------------------------------------------------------------------------
        */

        $institutionId = $authUser->madrasa_id ?? 1;

        $previewIds = [];

        foreach (array_keys($userTypes) as $type) {

            $previewIds[$type] =
                User::previewInstitutionUserId(
                    $institutionId,
                    $type
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Student Form Data
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::where('status', 1)
            ->orderBy('name', 'desc')
            ->get();

        $classes = Classes::where('status', 1)->get();

        return view('admin.users.create', compact(
            'userTypes',
            'roles',
            'madrasas',
            'divisions',
            'previewIds',
            'academicYears',
            'classes'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $authUser = auth()->user();

            /*
            |--------------------------------------------------------------------------
            | Role Find
            |--------------------------------------------------------------------------
            */

            $role = Role::where(
                'slug',
                $request->user_type
            )->first();

            if (!$role) {

                return back()->with(
                    'error',
                    'Role পাওয়া যায়নি'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Permission Check
            |--------------------------------------------------------------------------
            */

            if (
                $role->slug == 'soft-admin'
                && !$authUser->is_super_admin
            ) {

                return back()->with(
                    'error',
                    'শুধু Super Admin Soft Admin তৈরি করতে পারবে'
                );
            }

            if (
                $role->slug == 'madrasa-admin'
                && !$authUser->is_soft_admin
                && !$authUser->is_super_admin
            ) {

                return back()->with(
                    'error',
                    'আপনার অনুমতি নেই'
                );
            }

            $isStudent = $role->slug === 'student';

            /*
            |--------------------------------------------------------------------------
            | Madrasa ID
            |--------------------------------------------------------------------------
            */

            if ($authUser->is_super_admin) {

                $madrasaId = $request->madrasa_id;

            } else {

                $madrasaId = $authUser->madrasa_id;
            }

            /*
            |--------------------------------------------------------------------------
            | Validation
            |--------------------------------------------------------------------------
            */

            $rules = [
                'user_type' => 'required',
                'name_bn' => 'required|string|max:255',
                'phone' => 'required|unique:users,phone',
                'gender' => 'required',
                'email' => 'nullable|email|unique:users,email',
                'password' => 'nullable|min:6',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ];

            if ($authUser->is_super_admin) {

                $rules['madrasa_id'] ='required|exists:madrasas,id';
            }

            if ($isStudent) {

                $rules['class_id'] ='nullable|exists:classes,id';
                $rules['section_id'] ='nullable|exists:sections,id';
            }

            $request->validate($rules);

            /*
            |--------------------------------------------------------------------------
            | Username Generate
            |--------------------------------------------------------------------------
            */

            $username = $this->generateUsername(
                $request->name,
                $request->name_bn
            );

            /*
            |--------------------------------------------------------------------------
            | Photo Upload
            |--------------------------------------------------------------------------
            */

            $photoPath = null;

            if ($request->hasFile('photo')) {

                $photoPath = $request->file('photo')
                    ->store('users/photos', 'public');
            }

            /*
            |--------------------------------------------------------------------------
            | Institution User ID
            |--------------------------------------------------------------------------
            */

            $institutionUserId =
                User::generateInstitutionUserId(
                    $madrasaId,
                    $role->slug
                );

            /*
            |--------------------------------------------------------------------------
            | User Create
            |--------------------------------------------------------------------------
            */

            $user = User::create([
                'institution_user_id' => $institutionUserId,
                'madrasa_id' => $madrasaId,
                'role_id' => $role->id,
                'username' => $username,
                'email' => $request->email,
                'password' => Hash::make($request->password ?? 'password123'),
                'name' => $request->name,
                'name_bn' => $request->name_bn,
                'photo' => $photoPath,
                'phone' => $request->phone,
                'phone2' => $request->phone2,
                'phone_owner' => $request->phone_owner,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'age' => $request->date_of_birth
                    ? Carbon::parse(
                        $request->date_of_birth
                    )->age
                    : null,
                'blood_group' => $request->blood_group,
                'religion' => $request->religion,
                'nid' => $request->nid,
                'birth_certificate' => $request->birth_certificate,
                'father_name' => $request->father_name,
                'father_phone' => $request->father_phone,
                'mother_name' => $request->mother_name,
                'mother_phone' => $request->mother_phone,
                'guardian_name' =>
                    $request->guardian_name,
                'guardian_phone' =>
                    $request->guardian_phone,
                'guardian_relation' =>
                    $request->guardian_relation,
                'present_division_id' =>
                    $request->present_division_id,
                'present_district_id' =>
                    $request->present_district_id,
                'present_upazila_id' =>
                    $request->present_upazila_id,
                'present_union' =>
                    $request->present_union,
                'present_post_office' =>
                    $request->present_post_office,
                'present_village_road' =>
                    $request->present_village_road,
                'present_postal_code' =>
                    $request->present_postal_code,
                'present_address_full' =>
                    $this->generateAddressString(
                        $request,
                        'present'
                    ),
                'permanent_division_id' =>
                    $request->permanent_division_id,
                'permanent_district_id' =>
                    $request->permanent_district_id,
                'permanent_upazila_id' =>
                    $request->permanent_upazila_id,
                'permanent_union' =>
                    $request->permanent_union,
                'permanent_post_office' =>
                    $request->permanent_post_office,
                'permanent_village_road' =>
                    $request->permanent_village_road,
                'permanent_postal_code' =>
                    $request->permanent_postal_code,
                'permanent_address_full' =>
                    $this->generateAddressString(
                        $request,
                        'permanent'
                    ),
                'status' => 1,
                'created_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Student Create
            |--------------------------------------------------------------------------
            */

            if ($isStudent) {

                $academicYear =
                    AcademicYear::first();

                if (!$academicYear) {

                    throw new \Exception(
                        'Academic Year পাওয়া যায়নি'
                    );
                }

                $studentId =
                    $this->generateStudentId(
                        $madrasaId,
                        $academicYear->id
                    );

                Student::create([
                    'user_id' => $user->id,
                    'madrasa_id' => $madrasaId,
                    'academic_year_id' =>
                        $academicYear->id,
                    'class_id' =>
                        $request->class_id,
                    'section_id' =>
                        $request->section_id,
                    'guardian_user_id' =>
                        $request->guardian_user_id,
                    'student_id' =>
                        $studentId,
                    'is_hostel' =>
                        $request->boolean(
                            'is_hostel'
                        ),
                    'is_transport' =>
                        $request->boolean(
                            'is_transport'
                        ),
                    'status' => 1,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('dashboard.user.index')
                ->with(
                    'success',
                    'ইউজার সফলভাবে তৈরি হয়েছে'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with(
                    'error',
                    $e->getMessage()
                )
                ->withInput();
        }
    }

    private function generateStudentId(
        $madrasaId,
        $academicYearId
    ) {

        try {

            $lastStudent = Student::where(
                    'madrasa_id',
                    $madrasaId
                )
                ->where(
                    'academic_year_id',
                    $academicYearId
                )
                ->latest()
                ->first();

            if (
                $lastStudent &&
                $lastStudent->student_id
            ) {

                preg_match(
                    '/STU-\d+-\d+-(\d+)/',
                    $lastStudent->student_id,
                    $matches
                );

                if (isset($matches[1])) {

                    $lastNumber =
                        (int) $matches[1];

                    $newNumber =
                        str_pad(
                            $lastNumber + 1,
                            4,
                            '0',
                            STR_PAD_LEFT
                        );

                } else {

                    $newNumber = '0001';
                }

            } else {

                $newNumber = '0001';
            }

            $studentId =
                'STU-' . $madrasaId . '-' . $academicYearId . '-' . $newNumber;

            if (
                Student::where(
                    'student_id',
                    $studentId
                )->exists()
            ) {

                return $this->generateStudentId(
                    $madrasaId,
                    $academicYearId
                );
            }

            return $studentId;

        } catch (\Exception $e) {

            return
                'STU-' .
                $madrasaId .
                '-' .
                $academicYearId .
                '-' .
                time();
        }
    }

    public function destroy($id)
    {
        try {

            $user = User::findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | Super Admin Delete Protection
            |--------------------------------------------------------------------------
            */

            if (
                optional($user->role)->slug
                == 'super-admin'
            ) {

                return back()->with(
                    'error',
                    'Super Admin ডিলিট করা যাবে না'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Student Delete
            |--------------------------------------------------------------------------
            */

            if ($user->student) {

                $user->student->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Photo Delete
            |--------------------------------------------------------------------------
            */

            if (
                $user->photo &&
                Storage::disk('public')->exists(
                    $user->photo
                )
            ) {

                Storage::disk('public')
                    ->delete($user->photo);
            }

            $user->delete();

            return redirect()
                ->route('dashboard.user.index')
                ->with(
                    'success',
                    'ডিলিট সফল হয়েছে'
                );

        } catch (\Exception $e) {

            return back()->with(
                'error',
                'ডিলিট করতে সমস্যা: ' .
                $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Username Generate
    |--------------------------------------------------------------------------
    */

    private function generateUsername(
        $nameEn = null,
        $nameBn = null
    ) {

        $base = preg_replace(
            '/[^a-zA-Z0-9]/',
            '',
            strtolower($nameEn ?? '')
        );

        if (empty($base)) {

            $base =
                'user' .
                substr(
                    md5(
                        ($nameBn ?? '') .
                        microtime()
                    ),
                    0,
                    6
                );
        }

        $username = $base;

        $counter = 1;

        while (
            User::where(
                'username',
                $username
            )->exists()
        ) {

            $username =
                $base . $counter;

            $counter++;
        }

        return $username;
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Methods
    |--------------------------------------------------------------------------
    */

    public function getDistricts($divisionId)
    {
        return response()->json(
            District::where(
                'division_id',
                $divisionId
            )->get([
                'id',
                'name_bn'
            ])
        );
    }

    public function getUpazilas($districtId)
    {
        return response()->json(
            Upazila::where(
                'district_id',
                $districtId
            )->get([
                'id',
                'name_bn'
            ])
        );
    }

    public function getSections($classId)
    {
        return response()->json(
            Section::where(
                'class_id',
                $classId
            )
            ->where('status', 1)
            ->get([
                'id',
                'section_name'
            ])
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Address Generate
    |--------------------------------------------------------------------------
    */

    private function generateAddressString(
        $request,
        $type
    ) {

        $parts = [];

        $prefix = $type;

        if (
            $request->{"{$prefix}_village_road"}
        ) {

            $parts[] =
                $request->{"{$prefix}_village_road"};
        }

        if (
            $request->{"{$prefix}_union"}
        ) {

            $parts[] =
                $request->{"{$prefix}_union"};
        }

        if (
            $request->{"{$prefix}_upazila_id"}
        ) {

            $upazila = Upazila::find(
                $request->{"{$prefix}_upazila_id"}
            );

            if ($upazila) {

                $parts[] = $upazila->name_bn;
            }
        }

        if (
            $request->{"{$prefix}_district_id"}
        ) {

            $district = District::find(
                $request->{"{$prefix}_district_id"}
            );

            if ($district) {

                $parts[] = $district->name_bn;
            }
        }

        if (
            $request->{"{$prefix}_division_id"}
        ) {

            $division = Division::find(
                $request->{"{$prefix}_division_id"}
            );

            if ($division) {

                $parts[] = $division->name_bn;
            }
        }

        if (
            $request->{"{$prefix}_postal_code"}
        ) {

            $parts[] =
                $request->{"{$prefix}_postal_code"};
        }

        return implode(', ', $parts);
    }

    /*
    |--------------------------------------------------------------------------
    | Preview ID
    |--------------------------------------------------------------------------
    */

    public function previewId(Request $request)
    {
        $userType = $request->user_type;

        $madrasaId =
            $request->madrasa_id ?? 1;

        $role = Role::where(
            'slug',
            $userType
        )->first();

        if (!$role) {

            return response()->json([

                'success' => false,

                'preview_id' => '100'
            ]);
        }

        $lastUser = User::where(
                'madrasa_id',
                $madrasaId
            )
            ->where(
                'role_id',
                $role->id
            )
            ->whereNotNull(
                'institution_user_id'
            )
            ->orderByRaw(
                'CAST(institution_user_id AS UNSIGNED) DESC'
            )
            ->first();

        if ($lastUser) {

            $previewId =
                (int) $lastUser
                    ->institution_user_id + 1;

        } else {

            $previewId = match ($userType) {
                'student'       => 101,
                'teacher'       => 201,
                'guardian'      => 301,
                'madrasa-admin' => 401,
                'soft-admin'    => 501,

                default         => 100,
            };
        }

        return response()->json([

            'success' => true,

            'preview_id' => (string) $previewId
        ]);
    }
}