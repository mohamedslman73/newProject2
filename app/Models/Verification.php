<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Verification extends Model
{

    protected $table = 'verification';
    public $timestamps = true;

    protected $fillable = ['user_id','code'];

    public function user(){
        return $this->belongsTo('App\Models\User','user_id');
    }

}