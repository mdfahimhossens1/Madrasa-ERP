<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'ledger_id',
        'sub_ledger_id',
        'amount',
        'description'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class);
    }

    public function subLedger()
    {
        return $this->belongsTo(SubLedger::class, 'sub_ledger_id');
    }
}