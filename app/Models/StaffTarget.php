<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class StaffTarget extends Model{

    use LogsActivity;
    public $timestamps = true;
    protected $table = 'staff_target';
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'staff_id',
        'is_supervisor',
        'managed_staff_ids',
        'year',
        'month',
        'amount',
        'description',
        'sales_commission'
    ];

    /*
     * Log Activity
     */
    protected static $logAttributes = [
        'staff_id',
        'is_supervisor',
        'managed_staff_ids',
        'year',
        'month',
        'amount',
        'description',
        'sales_commission',
    ];

    public function staff(){
        return $this->belongsTo('App\Models\Staff','staff_id');
    }


    public function paymentInvoices(){
        if($this->is_supervisor == 'no'){
            return PaymentInvoice::join('wallet_settlement','wallet_settlement.id','=','payment_invoice.wallet_settlement_id')
                // Payment Invoice Where
                ->where('payment_invoice.status','paid')
                ->whereNotNull('payment_invoice.wallet_settlement_id')

                // Wallet Settlement Where
                ->where('wallet_settlement.staff_id',$this->staff_id)
                ->where('wallet_settlement.status','done')
                ->whereRaw("CONCAT(MONTH(`wallet_settlement`.`from_date_time`),'-',YEAR(`wallet_settlement`.`from_date_time`)) = ?",[$this->month.'-'.$this->year])
                ->whereRaw("CONCAT(MONTH(`wallet_settlement`.`to_date_time`),'-',YEAR(`wallet_settlement`.`to_date_time`)) = ?",[$this->month.'-'.$this->year])

                // Get Data
                ->selectRaw('SUM(`payment_invoice`.`total_amount`) as `total`')
                ->selectRaw('COUNT(`payment_invoice`.`id`) as `count`')
                ->selectRaw('GROUP_CONCAT(`payment_invoice`.`id`) as `payment_invoices_id`')
                ->get()
                ->first();
        }

        return PaymentInvoice::join('wallet_settlement','wallet_settlement.id','=','payment_invoice.wallet_settlement_id')
            // Payment Invoice Where
            ->where('payment_invoice.status','paid')
            ->whereNotNull('payment_invoice.wallet_settlement_id')

            // Wallet Settlement Where
            ->whereIn('wallet_settlement.staff_id',explode(',',$this->managed_staff_ids))
            ->where('wallet_settlement.status','done')
            ->whereRaw("CONCAT(MONTH(`wallet_settlement`.`from_date_time`),'-',YEAR(`wallet_settlement`.`from_date_time`)) = ?",[$this->month.'-'.$this->year])
            ->whereRaw("CONCAT(MONTH(`wallet_settlement`.`to_date_time`),'-',YEAR(`wallet_settlement`.`to_date_time`)) = ?",[$this->month.'-'.$this->year])

            // Get Data
            ->select('wallet_settlement.staff_id')
            ->selectRaw('SUM(`payment_invoice`.`total_amount`) as `total`')
            ->selectRaw('COUNT(`payment_invoice`.`id`) as `count`')
            ->selectRaw('GROUP_CONCAT(`payment_invoice`.`id`) as `payment_invoices_id`')
            ->groupBy('wallet_settlement.staff_id')
            ->get();
    }



    public function managedStaffTarget(){

        return self::whereIn('staff_id',explode(',',$this->managed_staff_ids))
            ->where('is_supervisor','no')
            ->where('year',$this->year)
            ->where('month',$this->month)
            ->with('staff')
            ->get();
    }


}