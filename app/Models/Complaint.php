<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Complaint extends Model
{
    protected $table = 'complaints';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'staff_id',
        'project_id',
        'client_id',
        'details',
        'status',
        'created_by_staff_id'
    ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }

    public function created_by_staff()
    {
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }

    public function project(){
        return $this->belongsTo('App\Models\Project','project_id');
    }

    public function client()
    {
        return  $this->belongsTo('App\Models\Client','client_id');
    }
}