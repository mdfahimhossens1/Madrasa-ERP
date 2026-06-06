<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubLedger extends Model
{
    protected $fillable = ['ledger_id', 'madrasa_id', 'fee_type', 'name'];

    public function ledger(){
        return $this->belongsTo(Ledger::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}