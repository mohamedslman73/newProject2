<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Item extends Model
{
    protected $table = 'items';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'description',
        'status',
        'code',
        'item_category_id',
        'unite',
        'image',
        'price',
        'count',
        'cost',
        'min_count',
        'staff_id',
        ];

    /*
     * Log Activity
     */
    protected static $logAttributes = [
        'name',
        'description',
    ];

    public function item_categories()
    {
        return $this->belongsTo('App\Models\ItemCategories','item_category_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
//    public function supplier_order_items(){
//        return $this->hasMany('App\Models\SupplierOrderItems');
//    }
//    public function salman(){
//        return $this->hasManyThrough('App\Models\Supplier','App\Models\SupplierOrderItems');
//    }
}
