<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToInstitution;
class Ledger extends Model
{
    use BelongsToInstitution;

    protected $fillable = ['fund_id','institution_id','user_id', 'name', 'type'];

    public function subLedgers(){
        return $this->hasMany(SubLedger::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}