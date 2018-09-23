<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PwdReset extends Model
{
    protected $table = 'password_resets';


    protected $dates = ['created_at'];

}