<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    protected $table = 'phone_number';

    protected $fillable = [
        'name', 'phone', 'email'
    ];
    
}
