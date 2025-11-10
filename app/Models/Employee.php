<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Employee extends Model
{
    use HasApitokens;


    protected $fillable = [
        'user_id',
        'salary',
        'department',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
