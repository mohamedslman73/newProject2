<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SystemTicket;
use Spatie\Activitylog\Traits\LogsActivity;

class Staff extends Authenticatable
{

    protected $table = 'staff';
    public $timestamps = true;

    use SoftDeletes;
    use Notifiable,LogsActivity;

    public $modelPath = 'App\Models\Staff';
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'firstname',
        'lastname',
        'visa_status',
        'visa_number',
        'passport_number',
        'avatar',
        'gender',
        'birthdate',
        'date_of_visa_issue' ,
        'salary_card_id_no' ,
        'salary'  ,
        'job_title',
        'nationality',
        'medical',
        'blood',
        'finger_print',
        'government_id',
        'certificate',
        'staff_id',
        'cancel_date',
        'joining_date',
        'age',
        'permission_group_id',
        'weekly_vacations',

        'contact_in_home_country',
        'weekly_vacations',
        'bank_account',
        'length',
        'weight',
        'termination_date',
        'api_token',
        'status'
    ];
    protected $hidden = array('password', 'remember_token');


    public static function StaffPerms($staffID){
        return Staff::find($staffID)->permission->pluck('route_name');
    }

    public function getFullnameAttribute($key)
    {
        if(isset($this->firstname) && strlen($this->firstname))
            $name = $this->firstname;
        if(isset($this->middlename) && strlen($this->middlename))
            $name .= ' ' .$this->middlename;

        if(isset($this->lastname) && strlen($this->lastname))
            $name .= ' ' .$this->lastname;

        return $name;
    }

    public function permission_group(){
        return $this->belongsTo('App\Models\PermissionGroup','permission_group_id','id');
    }

    public function permission(){
        return $this->hasManyThrough('App\Models\Permission','App\Models\PermissionGroup','id','permission_group_id','permission_group_id');
    }

    public function attendance(){
        return $this->hasMany('App\Models\Attendance','cleaner_id');
    }

    public function staff_clothes()
    {
        return $this->hasMany('App\Models\StaffClothes','cleaner_id');
    }

    public function activity_log()
    {
        return $this->morphMany('Spatie\Activitylog\Models\Activity', 'causer');
    }
}