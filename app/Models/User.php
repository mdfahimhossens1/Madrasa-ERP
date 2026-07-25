<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'institution_id',
        'role_id',
        'institution_user_id',
        'username',
        'email',
        'password',
        'name',
        'name_bn',
        'photo',
        'cover_photo',
        'phone',
        'phone2',
        'phone_owner',
        'gender',
        'date_of_birth',
        'age',
        'blood_group',
        'religion',
        'nid',
        'birth_certificate',
        'custom_id',
        'present_division_id',
        'present_district_id',
        'present_upazila_id',
        'present_union',
        'present_post_office',
        'present_village_road',
        'present_postal_code',
        'present_address_full',
        'permanent_division_id',
        'permanent_district_id',
        'permanent_upazila_id',
        'permanent_union',
        'permanent_post_office',
        'permanent_village_road',
        'permanent_postal_code',
        'permanent_address_full',
        'father_name',
        'father_phone',
        'mother_name',
        'mother_phone',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',
        'status',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'last_login_at' => 'datetime',
        'status' => 'integer',
        'age' => 'integer',
    ];

    // Relationships
public function institution()
{
    return $this->belongsTo(Madrasa::class, 'institution_id');
}

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function presentDivision()
    {
        return $this->belongsTo(Division::class, 'present_division_id');
    }

    public function presentDistrict()
    {
        return $this->belongsTo(District::class, 'present_district_id');
    }

    public function presentUpazila()
    {
        return $this->belongsTo(Upazila::class, 'present_upazila_id');
    }

    public function permanentDivision()
    {
        return $this->belongsTo(Division::class, 'permanent_division_id');
    }

    public function permanentDistrict()
    {
        return $this->belongsTo(District::class, 'permanent_district_id');
    }

    public function permanentUpazila()
    {
        return $this->belongsTo(Upazila::class, 'permanent_upazila_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors
    public function getFullPresentAddressAttribute()
    {
        $address = [];
        if ($this->present_village_road) $address[] = $this->present_village_road;
        if ($this->present_union) $address[] = $this->present_union;
        if ($this->presentUpazila) $address[] = $this->presentUpazila->name_bn;
        if ($this->presentDistrict) $address[] = $this->presentDistrict->name_bn;
        if ($this->presentDivision) $address[] = $this->presentDivision->name_bn;
        if ($this->present_postal_code) $address[] = $this->present_postal_code;
        return implode(', ', $address);
    }

    public function getFullPermanentAddressAttribute()
    {
        $address = [];
        if ($this->permanent_village_road) $address[] = $this->permanent_village_road;
        if ($this->permanent_union) $address[] = $this->permanent_union;
        if ($this->permanentUpazila) $address[] = $this->permanentUpazila->name_bn;
        if ($this->permanentDistrict) $address[] = $this->permanentDistrict->name_bn;
        if ($this->permanentDivision) $address[] = $this->permanentDivision->name_bn;
        if ($this->permanent_postal_code) $address[] = $this->permanent_postal_code;
        return implode(', ', $address);
    }
public function hasRole(string $role): bool
{
    return optional($this->role)->slug === $role;
}
    // Role check methods
public function getIsSuperAdminAttribute()
{
    return $this->hasRole('super-admin');
}

public function getIsSoftAdminAttribute()
{
    return $this->hasRole('soft-admin');
}

public function getIsMadrasaAdminAttribute()
{
    return $this->hasRole('madrasa-admin');
}

public function getIsTeacherAttribute()
{
    return $this->hasRole('teacher');
}

public function getIsStudentAttribute()
{
    return $this->hasRole('student');
}

public function getIsGuardianAttribute()
{
    return $this->hasRole('guardian');
}
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('name_bn', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%");
        });
    }

public static function generateInstitutionUserId($institutionId, $userType)
{
    $role = Role::where('slug', $userType)->first();

    if (!$role) {
        return 100;
    }

    $lastUser = self::where('institution_id', $institutionId)
        ->where('role_id', $role->id)
        ->whereNotNull('institution_user_id')
        ->orderByRaw('CAST(institution_user_id AS UNSIGNED) DESC')
        ->first();

    if ($lastUser) {
        return (int)$lastUser->institution_user_id + 1;
    }

return match ($userType) {
    'student'       => 101,
    'teacher'       => 201,
    'guardian'      => 301,
    'madrasa-admin' => 401,
    'soft-admin'    => 501,
    'super-admin'   => 601,
    default         => 100,
};
}
public static function previewInstitutionUserId($institutionId, $roleSlug)  // institution_id -> madrasa_id
{
    $baseNumber = match($roleSlug) {
        'student' => 101,
        'teacher' => 201,
        'guardian' => 301,
        'madrasa-admin' => 401,
        'soft-admin' => 501,
        'super-admin' => 601,
        default => 100,
    };
    
    $lastUser = self::where('institution_id', $institutionId)  // institution_id -> madrasa_id
        ->where('role_id', Role::where('slug', $roleSlug)->first()?->id)
        ->orderBy('id', 'desc')
        ->first();
    
    if ($lastUser && $lastUser->institution_user_id) {
        $lastNumber = (int) $lastUser->institution_user_id;
        if ($lastNumber >= $baseNumber && $lastNumber < $baseNumber + 100) {
            return (string) ($lastNumber + 1);
        }
    }
    
    return (string) $baseNumber;
}
    public function getFormattedInstitutionIdAttribute()
    {
        return $this->institution_user_id.' - '.optional($this->role)->role_name;
    }
/*
|--------------------------------------------------------------------------
| Permissions
|--------------------------------------------------------------------------
*/

public function permissions()
{
    return $this->belongsToMany(
        Permission::class,
        'user_permissions'
    )
    ->withPivot('allow')
    ->withTimestamps();
}
public function hasPermission(string $permission): bool
{
    if ($this->is_super_admin) {
        return true;
    }

    if (!$this->role) {
        return false;
    }

    return $this->role
        ->permissions()
        ->where('slug', $permission)
        ->where('is_active', true)
        ->exists();
}
public function hasPanel(string $panel): bool
{
    if ($this->is_super_admin) {
        return true;
    }

    if (!$this->role) {
        return false;
    }

    return $this->role
        ->systemPanels()
        ->where('slug', $panel)
        ->where('is_active', true)
        ->exists();
}
}