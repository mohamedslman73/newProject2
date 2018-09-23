<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class PermissionGroup extends Model 
{

    protected $table = 'permission_groups';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'is_supervisor',
        'whitelist_ip'
    ];

    /*
     * Log Activity
     */
    protected static $logAttributes = [
        'name',
        'is_supervisor',
        'whitelist_ip',
    ];

    public function permission()
    {
        return $this->hasMany('App\Models\Permission');
    }

    public function staff()
    {
        return $this->hasMany('App\Models\Staff');
    }

}