<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ClientOrderItems extends Model
{
    protected $table = 'client_order_items';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_order_id',
        'item_id',
        'count',
        'price',
        ];



    public function client()
    {
        return $this->belongsTo('App\Models\Client','client_id');
    }
    public function client_order()
    {
        return $this->belongsTo('App\Models\ClientOrders','client_order_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }

    public function item(){
        return $this->belongsTo('App\Models\Item','item_id');
    }


}
