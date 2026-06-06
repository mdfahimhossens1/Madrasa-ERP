<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $fillable = [ 'madrasa_id','user_id', 'name', 'balance'];

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}