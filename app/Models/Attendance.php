<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Attendance extends Model
{
    protected $table = 'attendance';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'cleaner_id',
        'date',
        'project_id',
        'type',
        'notes',
        'group_id',
        'staff_id',
        ];


    public function cleaner()
    {
        return $this->belongsTo('App\Models\Staff','cleaner_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }

    public function project(){
        return $this->belongsTo('App\Models\Project');
    }

}
