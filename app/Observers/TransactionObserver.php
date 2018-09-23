<?php
namespace App\Observers;
use App\Models\Wallet;
use App\Models\Transaction;

class TransactionObserver {

    public function creating(Transaction $transactions){
        //@TODO MUST Check if user have enough balance
        /*
        if(Wallet::where('id', $transactions->from_id)->balance < $transactions->amount)
            return false;
        */
    }

    public function created(Transaction $transactions){
        if($transactions->status == 'paid' && $transactions->type == 'wallet') {
            Wallet::where('id', $transactions->from_id)->decrement('balance', $transactions->amount);
            Wallet::where('id', $transactions->to_id)->increment('balance', $transactions->amount);
        }

        $ItemContainer = $transactions->order;
        $Transactions = $ItemContainer->first()->trans();
        $transtotall = $Transactions->sum('amount');
        //Making sure all transactions has been aded
        if($transtotall == $ItemContainer->first()->total){
            if($Transactions->where('status','unpaid')->count()){
                $ItemContainer->update(['is_paid'=>'no']);
            } else {
                $ItemContainer->update(['is_paid'=>'yes']);
            }
        }

    }


    public function deleting(Transaction $transactions){
        //@TODO MUST Check if merchant have enough balance
        /*
        if(Wallet::where('id', $transactions->to_id)->balance < $transactions->amount)
            return false;
        */
    }

    public function deleted(Transaction $transactions){
        if($transactions->status == 'paid' && $transactions->type == 'wallet') {
            Wallet::where('id', $transactions->from_id)->increment('balance', $transactions->amount);
            Wallet::where('id', $transactions->to_id)->decrement('balance', $transactions->amount);
        }

        $ItemContainer = $transactions->order;
        $Transactions = $ItemContainer->trans();
        $transtotall = $Transactions->sum('amount');
        //Making sure all transactions has been aded
        if($transtotall == $ItemContainer->first()->total){
            if($Transactions->where('status','unpaid')->count()){
                $ItemContainer->update(['is_paid'=>'no']);
            } else {
                $ItemContainer->update(['is_paid'=>'yes']);
            }
        }
    }

}
