<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{

    protected $table = 'error_log';
    public $timestamps = true;


    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'model_type',
        'model_id',
        'type',
        'data',
        'msg'
    ];


    public function setDataAttribute($value){
        if(!is_array($value)){
            $this->attributes['data'] = [];
        }else{
            $this->attributes['data'] = @serialize($value);
        }
    }

    public function getDataAttribute($value){
        return @unserialize($value);
    }



}