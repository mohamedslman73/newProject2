<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SupplierOrders extends Model
{
    protected $table = 'supplier_order';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'supplier_id',
        'date',
        'plus',
        'minus',
        'total_price',
        'note',
        'staff_id',
        ];



    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier','supplier_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function supplier_order_items(){
        return $this->hasMany('App\Models\SupplierOrderItems','supplier_order_id');
    }


}
