<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToInstitution;
class Fund extends Model
{
    use BelongsToInstitution;
    
    protected $fillable = [ 'institution_id','user_id', 'name', 'balance'];

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }
}