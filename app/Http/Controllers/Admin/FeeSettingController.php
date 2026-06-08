<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\FeeSetting;
use App\Models\FeeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeeSettingController extends Controller
{
    public function index()
    {
        $madrasaId = auth()->user()->madrasa_id;

        $academicYears = AcademicYear::where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();

        $classes = Classes::where('status', 'active')
            ->orderBy('name')
            ->get();

        // ✅ SubLedger এর বদলে FeeGroup
    $feeGroups = FeeGroup::with('subLedger') 
        ->where('madrasa_id', $madrasaId)
        ->where('is_active', 1)
        ->orderBy('name')
        ->get();

        // ✅ subLedger এর বদলে feeGroup relation
        $feeSettings = FeeSetting::with(['academicYear', 'class', 'feeGroup.subLedger'])
            ->where('madrasa_id', $madrasaId)
            ->latest()
            ->get();

        return view('admin.fee-settings.index', compact(
            'academicYears',
            'classes',
            'feeGroups',
            'feeSettings'
        ));
    }

    public function get(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id'         => 'nullable|exists:classes,id',
            'fee_group_id'     => 'required|exists:fee_groups,id', // ✅
        ]);

        try {
            $madrasaId = auth()->user()->madrasa_id;

            $query = FeeSetting::where('madrasa_id', $madrasaId)
                ->where('academic_year_id', $request->academic_year_id)
                ->where('fee_group_id', $request->fee_group_id); // ✅

            if ($request->class_id) {
                $query->where(function ($q) use ($request) {
                    $q->where('class_id', $request->class_id)
                      ->orWhereNull('class_id');
                });
            } else {
                $query->whereNull('class_id');
            }

            $data = $query->orderByRaw('class_id IS NULL')->first();

            return response()->json([
                'success' => true,
                'data'    => $data
            ]);

        } catch (\Exception $e) {
            Log::error('FeeSetting GET error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    public function save(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id'         => 'nullable|exists:classes,id',
            'fee_group_id'     => 'required|exists:fee_groups,id', // ✅
        ]);

        try {
            $madrasaId = auth()->user()->madrasa_id;

            $data = $request->only([
                'chattra_abashik_new', 'chattra_abashik_old',
                'chattra_onabashik_new', 'chattra_onabashik_old',
                'chattra_dekeyr_new', 'chattra_dekeyr_old',
                'chattra_nightcare_new', 'chattra_nightcare_old',
                'chhatri_abashik_new', 'chhatri_abashik_old',
                'chhatri_onabashik_new', 'chhatri_onabashik_old',
                'chhatri_dekeyr_new', 'chhatri_dekeyr_old',
                'chhatri_nightcare_new', 'chhatri_nightcare_old',
            ]);

            $data['madrasa_id']       = $madrasaId;
            $data['academic_year_id'] = $request->academic_year_id;
            $data['class_id']         = $request->class_id ?: null;
            $data['fee_group_id']     = $request->fee_group_id;

            FeeSetting::updateOrCreate(
                [
                    'madrasa_id'       => $madrasaId,
                    'academic_year_id' => $data['academic_year_id'],
                    'class_id'         => $data['class_id'],
                    'fee_group_id'     => $data['fee_group_id'],
                ],
                $data
            );

            return response()->json(['success' => true, 'message' => 'ফি সেটিংস সফলভাবে সেভ হয়েছে!']);

        } catch (\Exception $e) {
            Log::error('FeeSetting SAVE error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Save failed'], 500);
        }
    }

    public function reset(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        try {
            $madrasaId = auth()->user()->madrasa_id;

            FeeSetting::where('madrasa_id', $madrasaId)
                ->where('academic_year_id', $request->academic_year_id)
                ->delete();

            return response()->json(['success' => true, 'message' => 'রিসেট সম্পন্ন হয়েছে!']);

        } catch (\Exception $e) {
            Log::error('FeeSetting RESET error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Reset failed'], 500);
        }
    }
}