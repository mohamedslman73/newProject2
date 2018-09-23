<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class StaffClothes extends Model
{
    protected $table = 'staff_clothes';
    public $timestamps = true;


    protected $dates = ['created_at','updated_at'];
    protected $fillable = [
        'cleaner_id',
        'clothe_id',
        'size',
        'staff_id',
        ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function cleaner()
    {
        return $this->belongsTo('App\Models\Staff','cleaner_id');
    }
    public function clothe()
    {
        return $this->belongsTo('App\Models\Clothe','clothe_id');
    }
}
