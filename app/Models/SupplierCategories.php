<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class SupplierCategories extends Model
{
    protected $table = 'supplier_categories';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'description',
        'status',
        'staff_id',
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

}
