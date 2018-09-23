<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Bus extends Model
{
    protected $table = 'buses';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'bus_brand_id',
        'bus_number',
//        'daily_traffic_rate',
//        'amount_of_oil_change',
//        'oil_change_rate',
        'driver',
        'gas',
        'fixed_distance',
        'variable_distance',
        'available',
        'staff_id',
        ];


    public function brand()
    {
        return $this->belongsTo('App\Models\Brand','bus_brand_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function busDriver(){
        return $this->belongsTo('App\Models\Staff','driver');
    }

//    public function tracking(){
//        return $this->hasManyThrough('App\Models\BusTracking','App\Models\Bus','id','bus_id','bus_id');
//    }
    public function bus_traking(){
            return $this->hasMany('App\Models\BusTraking','bus_id');
    }
    public function maintenance(){
        return $this->hasMany('App\Models\Maintenance','bus_id');
    }

}
