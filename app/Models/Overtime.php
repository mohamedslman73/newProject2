<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Overtime extends Model
{
    protected $table = 'overtimes';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'added_to',
        'project_id',
        'hours',
        'date',
        'total_added_money',
        'staff_id',
        ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function addedTo()
    {
        return $this->belongsTo('App\Models\Staff','added_to');
    }
    public function project()
    {
        return $this->belongsTo('App\Models\Project','project_id');
    }
}
