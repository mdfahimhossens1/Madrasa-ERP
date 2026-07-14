<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstitution;
class FeeCollection extends Model
{
    use HasFactory, BelongsToInstitution;

    protected $table = 'fee_collections';

    protected $fillable = [
        'institution_id',
        'student_id',
        'fee_setting_id',
        'sub_ledger_id',
        'receipt_no',
        'collection_date',
        'total_amount',
        'discount',
        'paid_amount',
        'due_amount',
        'payment_method_id',
        'month',
        'pay_type',
        'transaction_ref',
        'status',
        'note',
        'collected_by',
    ];

    protected $casts = [
        'collection_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isPaid()
    {
        return $this->due_amount <= 0;
    }

    public function isPartial()
    {
        return $this->paid_amount > 0 && $this->due_amount > 0;
    }

    public function isDue()
    {
        return $this->paid_amount <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute()
    {
        if ($this->due_amount <= 0) {
            return 'paid';
        }

        if ($this->paid_amount > 0) {
            return 'partial';
        }

        return 'due';
    }
}
