<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Clothe extends Model
{
    protected $table = 'clothes';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'staff_id',
        ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
}
