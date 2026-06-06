<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherSequence extends Model
{
    protected $fillable = ['type', 'last_number'];
    
    public $timestamps = false;
    
    public static function nextVoucher($type)
    {
        $seq = self::firstOrCreate(
            ['type' => $type],
            ['last_number' => 0]
        );
        
        $seq->increment('last_number');
        
        $prefix = $type == 'income' ? 'I' : 'E';
        return $prefix . str_pad($seq->last_number, 4, '0', STR_PAD_LEFT);
    }

}