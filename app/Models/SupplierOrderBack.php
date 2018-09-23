<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SupplierOrderBack extends Model
{
    protected $table = 'supplier_order_back';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'supplier_order_id',
        'supplier_id',
        'total_price',
        'date',
        'notes',
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
