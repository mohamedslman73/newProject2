<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class StaffSupervisor extends Model
{
    use LogsActivity;
    public $timestamps = true;

    protected $table = 'staff_supervisor';
    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'id',
        'staff_supervisor_id',
        'staff_managed_id'
    ];

    /*
     * Log Activity
     */
    protected static $logAttributes = [
        'id',
        'staff_supervisor_id',
        'staff_managed_id',
    ];

    public function staff_managed(){
        return $this->belongsTo('App\Models\Staff','staff_managed_id');
    }


    public function staff_supervisor_data(){
        return $this->belongsTo('App\Models\Staff','staff_supervisor_id');
    }

    public function staff_managed_data(){
        return $this->belongsTo('App\Models\Staff','staff_managed_id');
    }




}