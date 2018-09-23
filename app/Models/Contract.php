<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Contract extends Model
{
    protected $table = 'contract';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'project_id',
        'description',
        'file',
        'date_from',
        'date_to',
        'staff_id',
        ];


    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }


}
