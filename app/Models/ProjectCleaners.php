<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectCleaners extends Model
{
    protected $table = 'project_cleaners';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'project_id',
        'department_id',
        'cleaner_id',

        ];

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function cleaner()
    {
        return $this->belongsTo('App\Models\Staff','cleaner_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Projects');
    }


}
