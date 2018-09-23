<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'name',
        'value'
    ];

    public function getOptionListAttribute($value){
        $value = @unserialize($value);
        if(!is_array($value)){
            return [];
        }
        return $value;
    }

}