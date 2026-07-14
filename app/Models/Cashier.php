<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToInstitution;

class Cashier extends Model
{
    use BelongsToInstitution;
    
    protected $fillable = ['name', 'institution_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
