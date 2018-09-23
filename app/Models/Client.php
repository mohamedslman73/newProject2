<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Client extends Model
{
    protected $table = 'clients';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'name',
        'status',
        'client_type_id',
        'organization_name',
        'email',
        'address',
        'phone',
        'mobile',
        'id_number',
        'init_credit',
        'staff_id',
        ];


    public function client_types()
    {
        return $this->belongsTo('App\Models\ClientTypes','client_type_id');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }

    public function client_order(){
        return $this->hasMany('App\Models\ClientOrders','client_id');

    }
    public function client_order_back(){
        return $this->hasMany('App\Models\ClientOrderBack','client_id');

    }
    public function client_revenue(){
        return $this->hasMany('App\Models\Profit','client_id');
    }
    public function project(){
        return $this->hasMany('App\Models\Project','client_id');
    }

}
