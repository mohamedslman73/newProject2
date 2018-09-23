<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ItemCategories extends Model
{
    protected $table = 'item_categories';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'description',
        'status',
        'staff_id',
        'parent_id',
    ];

    /*
     * Log Activity
     */
    protected static $logAttributes = [
        'name',
        'description',
    ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\ItemCategories','parent_id','id');
    }

    public function child()
    {
        return $this->hasMany('App\Models\ItemCategories','id','parent_id');
    }

    public function items(){
        return $this->hasMany('App\Models\Item','item_category_id');
    }

}
