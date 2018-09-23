<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{

    protected $table = 'notifications';
    public $timestamps = true;

    protected $dates = ['updated_at','created_at'];

}