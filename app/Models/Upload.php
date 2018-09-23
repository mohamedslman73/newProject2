<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model 
{

    protected $table = 'uploads';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('title', 'path', 'model_id', 'model_type');

    public function model()
    {
        return $this->morphTo();
    }

}