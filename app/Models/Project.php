<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    protected $table = 'projects';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name','status','client_id','staff_id'
        ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function cleaners()
    {
        return $this->hasMany('App\Models\ProjectCleaners','project_id');
    }

    public function contract()
    {
        return $this->hasMany('App\Models\Contract');
    }

    public function lastContract()
    {
        return  self::contract()->where('project_id',$this->id)->orderBy('id','DESC')->first();
    }
}
