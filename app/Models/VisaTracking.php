<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaTracking extends Model
{

    protected $table = 'visa_tracking';
    public $timestamps = true;

    use SoftDeletes;
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'staff_name',
        'visa_status',
        'visa_no',
        'passport_no',
        'gender',
        'date_of_visa_issue' ,
        'nationality',
        'staff_id',
        'id_no',
        'cancel_date',
        'joining_date',
    ];
}