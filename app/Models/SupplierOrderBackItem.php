<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SupplierOrderBackItem extends Model
{
    protected $table = 'supplier_order_back_items';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'supplier_order_back_id',
        'item_id',
        'count',
        'price',
        ];



    public function supplier_order_back()
    {
        return $this->belongsTo('App\Models\SupplierOrderBack','supplier_order_back_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function supplier_order_items(){
        return $this->hasMany('App\Models\SupplierOrderItems','supplier_order_id');
    }
    public function item()
    {
        return $this->belongsTo('App\Models\Item');

    }
}
