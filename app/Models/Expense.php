<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Expense extends Model
{
    protected $table = 'expenses';
    public $timestamps = true;

    use SoftDeletes,LogsActivity;

    protected $dates = ['created_at','updated_at','deleted_at'];
    protected $fillable = [
        'expense_causes_id',
        'date',
        'amount',
        'description',
        'supplier_id',
        'staff_id',
        ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff');
    }
    public function expense_causes()
    {
        return $this->belongsTo('App\Models\ExpenseCauses','expense_causes_id');
    }
    public function supplier(){
        return $this->belongsTo('App\Models\Supplier','supplier_id');
    }
}
