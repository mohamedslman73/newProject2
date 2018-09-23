<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\User;

class Call extends Model
{

    protected $table = 'calls';
    public $timestamps = true;

    use LogsActivity;

    protected $dates = ['call_time','created_at','updated_at','deleted_at'];
    protected $fillable = [
        'call_time',
        'client_name',
        'client_id',
        'call_propose',
        'type',
        'call_details',
        'status',
        'phone_number',
        'reminder',
        'staff_id',
    ];

    /*
     * Log Activity
     */
    protected static $logAttributes = [
        'call_time',
        'client_name',
        'type',
        'call_propose',
        'call_details',
        'status',
        'phone_number',
        'reminder',
        'staff_id',
    ];


    public function staff(){
        return $this->belongsTo('App\Models\Staff');
    }

    public static function viewData(array $additionColumn = []){
        $columns = [
            'calls.id',
            'calls.call_time',
            'calls.phone_number',
            "calls.client_name",
            "calls.call_propose",
            "calls.type",
            "calls.call_details",
            "calls.status",
            "calls.reminder",
            "calls.staff_id",
            "calls.created_at",
            "calls.updated_at",
        ];
        $columns = array_merge($columns,$additionColumn);
        return self::select($columns);
    }
}