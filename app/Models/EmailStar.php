<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailStar extends Model
{

    protected $table = 'email_star';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'model_type',
        'model_id',
        'email_id'
    ];


    public function email(){
        return $this->belongsTo('App\Models\EmailReceiver','email_id');
    }

    public function staff(){
        return $this->morphTo();
    }

}