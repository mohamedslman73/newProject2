<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class BusTracking extends Model
{
    protected $table = 'bus_traking';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'bus_id',
        'project_id',
        'driver_id',
        'number_km',
        'date_from',
        'date_to',
        'destination_from',
        'destination_to',
        'cost_per_km',
        'staff_id',
        ];


    public function project()
    {
        return $this->belongsTo('App\Models\Project','project_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function busDriver(){
        return $this->belongsTo('App\Models\Staff','driver_id');
    }
    public function bus()
    {
        return $this->belongsTo('App\Models\Bus','bus_id');
    }

}
