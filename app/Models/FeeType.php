<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
  protected $fillable = ['name', 'is_active'];
 
    public function fees() 
    { return $this->hasMany(Fee::class); 
    }
 
    public function isMonthly(): bool { 
        return $this->months > 1; 
    }

        public function feeSettings()
    {
        return $this->hasMany(FeeSetting::class);
    }
}
