<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPayment extends Model
{
    protected $fillable = [
        'madrasa_id',
        'student_id',
        'user_id',
        'fee_id',
        'month',
        'pay_type',
        'amount',
        'discount',
        'method',
        'pay_method_label',
        'pay_account_number',
        'cashier_id',
        'payment_date',
        'voucher_no',
        'note',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashier()
    {
        return $this->belongsTo(Cashier::class, 'cashier_id');
    }
}