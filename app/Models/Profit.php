<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Profit extends Model
{
    protected $table = 'revenues';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'revenue_causes_id',
        'date',
        'amount',
        'description',
        'client_id',
        'staff_id',
        ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function revenue_causes()
    {
        return $this->belongsTo('App\Models\ProfitCauses','revenue_causes_id');
    }
    public function client(){
        return $this->belongsTo('App\Models\Client','client_id');
    }
}
