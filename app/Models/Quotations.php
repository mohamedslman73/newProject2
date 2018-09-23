<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Quotations extends Model
{
    protected $table = 'quotations';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_id',
        'type',
        'name',
        'address',
        'phone',
        'notes',
        'description',
        'cleaners',
        'price_per_cleaner',
        'total_price',
        'file',
        'items',
        'staff_id',
        ];


    public function setCleanersAttribute($val){
        $this->attributes['cleaners'] = serialize($val);
    }
    public function setItemsAttribute($val){
        $this->attributes['items'] = serialize($val);
    }
//    public function getCleanersAttribute($val){
//        return $this->attributes['cleaners'] = unserialize($val);
//    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function item(){
        return $this->hasMany('App\Models\Item');
    }


}
