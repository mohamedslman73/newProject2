<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Maintenance extends Model
{
    protected $table = 'maintenances';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'bus_id',
        'maintenance_date',
        'no_km_moving',
        'no_of_km_oil',
        'note',
        'price',
        'staff_id',
        ];


    public function bus()
    {
        return $this->belongsTo('App\Models\Bus','bus_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }

}
