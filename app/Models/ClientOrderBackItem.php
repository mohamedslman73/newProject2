<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ClientOrderBackItem extends Model
{
    protected $table = 'client_order_back_items';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'client_order_back_id',
        'item_id',
        'count',
        'price',
        ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function item()
    {
        return $this->belongsTo('App\Models\Item');

    }
}
