<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class MonthlyReport extends Model
{
    protected $table = 'monthly_report';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'cleaner_id',
        'date',
        'total_days_presence',
        'total_money_presence',
        'total_days_weekly_vacation',
        'total_money_weekly_vacation',
        'total_days_absence',
        'total_money_absence',
        'total_days_paid_vacations',
        'total_money_paid_vacations',
        'total_days_unpaid_vacations',
        'total_money_unpaid_vacations',
        'total_days_overtime',
        'total_money_overtime',
        'total_days_deduction',
        'total_money_deduction',
        'total_money_staff',
        'staff_id',
        ];


    public function cleaner()
    {
        return $this->belongsTo('App\Models\Staff');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }

}
