<?php

namespace App\Libs;

use App\Models\LoyaltyWallet;
use App\Models\MerchantStaff;
use App\Models\TransactionsStatus;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Events\WalletEvent;
use DB;

class LoyaltyWalletData extends WalletAdapters\Adapter{

    /*
     * Make Transaction without Model ( For Transfer Money )
     */

    private static $makeTransactionWithoutModel   = false;

    private static $makeTransactionWithoutBalance = false;


    /**
     * @param $model = 'App\Models\Order'
     * return 'order'
     */

    public static function getModelTypeByModel($model){
        $getKey = array_search($model,self::$modelType);
        if($getKey){
            return $getKey;
        }

        return false;
    }

    public static function diffBetweenStatusType($walletID){
        $return = [];
        $wallet = LoyaltyWallet::find($walletID);
        if(!$wallet){
            return $return;
        }

        foreach (self::$statusType as $key => $value){
            $count = $wallet->allTransaction()->where('status',$value)->count();
            $return[$value] = [
                'count'=> $count,
                'percentage'=> 0,
            ];

        }

        $totalCount = array_sum(array_column($return,'count'));
        if($totalCount){
            foreach ($return as $key => $value){
                $return[$key]['percentage'] = round(($return[$key]['count']/$totalCount)*100);
            }
        }
        return $return;
    }

    public static function diffBetweenModelsType($walletID){
        $return = [];
        $wallet = LoyaltyWallet::find($walletID);
        if(!$wallet){
            return $return;
        }

        $modelType   = self::$modelType;
        $modelType[] = null;

        foreach ($modelType as $key => $value){
            if(is_null($value)){
                $count = $wallet->allTransaction()->whereNull('model_type')->count();
            }else{
                $count = $wallet->allTransaction()->where('model_type',$value)->count();
            }

            $return[$key] = [
                'count'=> $count,
                'percentage'=> 0,
            ];

        }

        $totalCount = array_sum(array_column($return,'count'));
        if($totalCount){
            foreach ($return as $key => $value){
                $return[$key]['percentage'] = round(($return[$key]['count']/$totalCount)*100);
            }
        }

        return $return;

    }

    public static function getWalletUserTypeModel($type){
        if(array_key_exists($type,self::$userType)){
            return self::$userType[$type];
        }
        return false;
    }

    public static function getWalletUserType($model){
        if(in_array($model,self::$userType)){
            return array_search($model,self::$userType);
        }

        return false;
    }

    /**
     * @param $userType
     * @param int $ID
     * @return mixed
     */
    public static function balance($userType, int $ID,$dateTo = 'now'){

        $getUserData = self::getUserData($userType,$ID);
        $getWallet = $getUserData->loyaltyWallet;
        if(!$getWallet){
            $getUserData->loyaltyWallet()->create([]);
            return self::balance($userType,$ID,$dateTo);
        }

        if($dateTo == 'now'){
            return $getWallet->balance;
        }else{
            return self::getBalanceTransaction($userType,$ID,$dateTo);
        }

    }


    /**
     * @param $userType
     * @param int $ID
     * @param string $dateTo
     */

    public static function getBalanceTransaction($userType, int $ID, $dateTo = 'now'){
        $getUserData = self::getUserData($userType,$ID);
        $getWallet = $getUserData->loyaltyWallet;

        // FROM
        $transactionFrom = $getWallet->transactionFrom()
            ->whereIn('status',['pending','paid'])
            ->select(DB::raw('SUM(`amount`) as `total`'));

        if($dateTo != 'now'){
            $transactionFrom->whereRaw('DATE(`created_at`) <= "'.$dateTo.'"');
        }

        $fromAmount = $transactionFrom->first();
        // FROM

        // TO
        $transactionto   = $getWallet->transactionTo()
            ->whereIn('status',['pending','paid'])
            ->select(DB::raw('SUM(`amount`) as `total`'));

        if($dateTo != 'now'){
            $transactionto->whereRaw('DATE(`created_at`) <= "'.$dateTo.'"');
        }

        $toAmount = $transactionto->first();
        // TO
        return $toAmount->total - $fromAmount->total;
    }

    /**
     * @param $fromUserType
     * @param int $fromID
     * @param $toUserType
     * @param int $toID
     */

    public static function makeTransaction($amount,$fromUserType, int $fromID, $toUserType, int $toID,$modelType,$modelID,$status = 'pending'){

        // Start moneyTransfer Event
        event(new WalletEvent([
            'event'=> 'start_moneyTransfer',
            'amount'=> $amount,
            'fromUserType'=> $fromUserType,
            'fromID'=> $fromID,
            'toUserType'=> $toUserType,
            'toID'=> $toID,
            'modelType'=> $modelType,
            'modelID'=> $modelID,
            'status'=> $status
        ]));

        $getFromBalance = self::balance($fromUserType,$fromID);

        // C Numeric
        if(!is_numeric($amount)){
            return ['status'=>false,'error_code'=> 1];
        }

        if(!in_array($status,self::$statusType)){
            return ['status'=>false,'error_code'=> 4];
        }

        if(!self::$makeTransactionWithoutModel && !array_key_exists($modelType,self::$modelType)){
            return ['status'=>false,'error_code'=> 5];
        }

        if($fromID == $toID && $fromUserType == $toUserType){
            return ['status'=>false,'error_code'=> 6];
        }

        // C if wallet bigger than or equele amount
        if($transactionsType == 'wallet' && $amount > $getFromBalance && self::$makeTransactionWithoutBalance === false){
            return ['status'=>false,'error_code'=> 2,'balance'=>$getFromBalance];
        }


        $walletTransaction = new \stdClass();
        DB::transaction(function () use($amount,$transactionsType,$fromUserType,$fromID,$toUserType,$toID,$modelType,$modelID,$status,&$walletTransaction) {
            // Start moneyTransfer Transaction Event
            event(new WalletEvent(['event'=> 'startTransaction_moneyTransfer']));

            $from = self::getUserData($fromUserType,$fromID);
            $to   = self::getUserData($toUserType,$toID);

            // Start Transfare Amount

            // Transactions Type
            switch ($transactionsType){
                case 'wallet':
                    if($status == 'pending'){
                        $from->wallet()->decrement('balance',$amount);
                    } elseif ($status == 'paid'){
                        $from->wallet()->decrement('balance',$amount);
                        $to->wallet()->increment('balance',$amount);
                    }
                    $walletTransaction = WalletTransaction::create([
                        'model_id'  => $modelID ?? null,
                        'model_type'=> self::$modelType[$modelType] ?? null,
                        'amount'    => $amount,
                        'from_id'   => $from->wallet->id,
                        'to_id'     => $to->wallet->id,
                        'type'      => $transactionsType,
                        'status'    => $status
                    ]);

                    break;


                case 'cash':
                    $walletTransaction = WalletTransaction::create([
                        'model_id'  => $modelID ?? null,
                        'model_type'=> self::$modelType[$modelType] ?? null,
                        'amount'    => $amount,
                        'from_id'   => $from->wallet->id,
                        'to_id'     => $to->wallet->id,
                        'type'      => $transactionsType,
                        'status'    => $status
                    ]);
                    break;

                default:
                    return false;
                    break;
            }

            // Transactions Status
            TransactionsStatus::create([
                'transaction_id'=> $walletTransaction->id,
                'status'=> $status
            ]);

        });

        // End moneyTransfer Transaction Event
        event(new WalletEvent(['event'=> 'endTransaction_moneyTransfer']));

        return $walletTransaction;

    }


    /**
     * Make Transaction without Model ( For Transfer Money )
     * @param $status
     */
    public static function makeTransactionWithoutModel($status){
        self::$makeTransactionWithoutModel = $status;
    }


    /**
     * Make Transaction without Balance ( For Transfer Money )
     * @param $status
     */
    public static function makeTransactionWithoutBalance($status){
        self::$makeTransactionWithoutBalance = $status;
    }

    /**
     * @param $transactionID
     * @param $status
     * @return array|bool
     */
    public static function changeTransactionStatus($transactionID, $status){
        // End moneyTransfer Transaction Event
        event(new WalletEvent(['event'=> 'start_changeTransactionStatus']));

        if(!in_array($status,self::$statusType)){
            return ['status'=>false,'error_code'=> 4];
        }

        $data = WalletTransaction::find($transactionID);

        if(!$data){
            return ['status'=>false,'error_code'=> 6];
        }

        if($data->status == $status){
            return ['status'=>false,'error_code'=> 7];
        }

        switch ($status){
            case 'paid':
                DB::transaction(function () use($data,$status) {
                    $data->toWallet()->increment('balance',$data->amount);
                    $data->update(['status'=> $status]);
                });

                break;
            case 'reverse':
                DB::transaction(function () use($data,$status) {
                    if($data->status == 'pending'){
                        $data->fromWallet()->increment('balance',$data->amount);
                    }else{
                        $data->fromWallet()->increment('balance',$data->amount);
                        $data->toWallet()->decrement('balance',$data->amount);
                    }

                    $data->update(['status'=> $status]);
                });
                break;
        }

        // Transactions Status
        TransactionsStatus::create([
            'transaction_id'=> $data->id,
            'status'=> $status
        ]);

        return true;

    }

    /**
     * @param $ID
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public static function transactionInfo($ID){
        return WalletTransaction::find($ID);
    }

    /**
     * @param $ID
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public static function walletInfo($ID){
        return Wallet::find($ID);
    }

    /**
     * @param $userType
     * @param int $ID
     * @return mixed
     * @throws \Exception
     */
    private static function getUserData($userType, int $ID){

        // ---- Merchant Staff
        if($userType == 'merchant_staff'){
            $userType = 'merchant';
            $ID       = MerchantStaff::find($ID)->merchant->id;
        }
        // ---- Merchant Staff

        if(!array_key_exists($userType,self::$userType)){
            throw new \Exception('User Type Dosen\'t Exists');
        }
        $userData = self::$userType[$userType]::find($ID);
        if(!$userData){
            throw new \Exception('There Are No User with This #ID:'.$ID);
        }

        return $userData;
    }

}