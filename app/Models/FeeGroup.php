<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeeGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'madrasa_id',
        'fund_id',
        'ledger_id',
        'sub_ledger_id',
        'fee_type_id',
        'type',
        'name',
        'amount',
        'description',
        'is_active',
        'number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function subLedger()
    {
        return $this->belongsTo(SubLedger::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function collections()
    {
        return $this->hasMany(FeeCollection::class);
    }

    public function feeSettings()
{
    return $this->hasMany(FeeSetting::class);
}

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
