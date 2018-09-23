<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Vacation extends Model
{
    protected $table = 'vacations';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'num_of_days',
        'vacation_type_id',
        'created_by_staff_id',
        'staff_id',
        'comment',
        'status',
        'from_date',
        'to_date',
        ];

    public function staff(){
        return $this->belongsTo('App\Models\Staff','staff_id');
    }

    public function created_by(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }
}
