<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ClientOrderBack extends Model
{
    protected $table = 'client_order_back';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_order_id',
        'client_id',
        'total_price',
        'date',
        'notes',
        'staff_id',
        ];



    public function client()
    {
        return $this->belongsTo('App\Models\Client','client_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
//    public function supplier_order_items(){
//        return $this->hasMany('App\Models\SupplierOrderItems','supplier_order_id');
//    }


}
