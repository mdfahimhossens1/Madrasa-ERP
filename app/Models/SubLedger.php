<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToInstitution;
class SubLedger extends Model
{

    use BelongsToInstitution;

    protected $fillable = ['ledger_id', 'institution_id', 'fee_type', 'name'];

    public function ledger(){
        return $this->belongsTo(Ledger::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}