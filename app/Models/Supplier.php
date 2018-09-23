<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Supplier extends Model
{
    protected $table = 'suppliers';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'description',
        'status',
        'supplier_category_id',
        'staff_id',
        'company_name',
        'email',
        'init_credit',
        'address',
        'phone1',
        'phone2',
        'phone3',
        'mobile1',
        'mobile2',
        'mobile3',
        'init_credit',
        ];
    /*
     * Log Activity
     */
    protected static $logAttributes = [
        'name',
        'description',
    ];

    public function supplier_categories()
    {
        return $this->belongsTo('App\Models\SupplierCategories','supplier_category_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function supplier_order(){
        return $this->hasMany('App\Models\SupplierOrders','supplier_id');

    }
    public function supplier_order_back(){
        return $this->hasMany('App\Models\SupplierOrderBack','supplier_id');
    }
    public function supplier_expence(){
        return $this->hasMany('App\Models\Expense','supplier_id');
    }

//    public function setPhoneAttribute($value){
//        $this->attributes['phone'] = json_encode($value);
//    }
//    public function getPhoneAttribute($value)
//    {
//        return   json_decode($value);
//    }
//    public function setMobileAttribute($value){
//        $this->attributes['mobile'] = json_encode($value);
//    }
//    public function getMobileAttribute($value)
//    {
//        return   json_decode($value);
//    }
}
