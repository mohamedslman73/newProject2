<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SupplierOrderItems extends Model
{
    protected $table = 'supplier_order_items';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'supplier_order_id',
        'item_id',
        'count',
        'price',
        ];



    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier','supplier_id');
    }
    public function supplier_order()
    {
        return $this->belongsTo('App\Models\SupplierOrders','supplier_order_id');
    }


    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function item(){
        return $this->belongsTo('App\Models\Item','item_id');
    }


}
