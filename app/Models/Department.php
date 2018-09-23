<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Department extends Model
{
    protected $table = 'department';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'staff_id',
    ];

    /*
     * Log Activity
     */
    protected static $logAttributes = [
        'name',
        'staff_id',
    ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }

}
