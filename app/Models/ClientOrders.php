<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ClientOrders extends Model
{
    protected $table = 'client_orders';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_id',
        'project_id',
        'date',
        'plus',
        'minus',
        'total_price',
        'notes',
        'description',
        'staff_id',
        ];



    public function client()
    {
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Project','project_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function client_order_items(){
        return $this->hasMany('App\Models\ClientOrderItems','client_order_id');
    }


}
