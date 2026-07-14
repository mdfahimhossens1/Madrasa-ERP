<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstitution;
class FeeSetting extends Model
{
    use HasFactory, BelongsToInstitution;

    protected $fillable = [
        'academic_year_id',
        'class_id',
        'fee_group_id',   
        'institution_id',
        'chattra_abashik_new',
        'chattra_abashik_old',
        'chattra_onabashik_new',
        'chattra_onabashik_old',
        'chattra_dekeyr_new',
        'chattra_dekeyr_old',
        'chattra_nightcare_new',
        'chattra_nightcare_old',
        'chattra_checked',
        'chhatri_abashik_new',
        'chhatri_abashik_old',
        'chhatri_onabashik_new',
        'chhatri_onabashik_old',
        'chhatri_dekeyr_new',
        'chhatri_dekeyr_old',
        'chhatri_nightcare_new',
        'chhatri_nightcare_old',
        'chhatri_checked',
    ];

    protected $casts = [
        'chattra_checked' => 'boolean',
        'chhatri_checked' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function feeGroup()
    {
        return $this->belongsTo(FeeGroup::class);
    }
}