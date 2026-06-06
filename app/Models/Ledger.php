<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected $fillable = ['fund_id','madrasa_id','user_id', 'name', 'type'];

    public function subLedgers(){
        return $this->hasMany(SubLedger::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}