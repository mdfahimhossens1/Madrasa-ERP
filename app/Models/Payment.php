<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'student_name',
        'month',
        'amount',
        'discount',
        'method',
        'pay_type',
        'cashier_id',
        'cashier_name',
        'payment_date',
        'voucher_no',
        'note',
    ];
}
