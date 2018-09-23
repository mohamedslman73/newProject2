<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailReceiver extends Model
{
    protected $table = 'email_receiver';
    public $timestamps = true;

    protected $fillable = ['receivermodel_type','receivermodel_id','star','seen',];

    public function receivermodel(){
        return $this->morphTo();
    }

    public function receiver(){
        return $this->belongsTo('App\Models\SystemTicket','email_id');
    }

    public function starForStaff($staffID){
        return $this->hasOne('App\Models\EmailStar','email_id')
            ->where('model_type','App\\Models\\Staff')
            ->where('model_id',$staffID);
    }

}