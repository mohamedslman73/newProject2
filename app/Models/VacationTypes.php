<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class VacationTypes extends Model
{
    protected $table = 'vacation_types';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'after_months',
        'staff_id',
        ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
}
