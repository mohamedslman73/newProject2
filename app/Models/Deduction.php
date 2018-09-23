<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Deduction extends Model
{
    protected $table = 'deductions';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'date',
        'amount',
        'deduction_from',
        'reason',
        'staff_id',
        ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function deductionFrom()
    {
        return $this->belongsTo('App\Models\Staff','deduction_from');
    }
}
