<?php

namespace App\Libs;

use App\Models\LoyaltyPrograms;
use App\Models\LoyaltyWalletTransaction;
use App\Models\MainWallets;
use App\Models\MerchantStaff;
use App\Models\Staff;
use App\Models\TransactionsStatus;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Events\WalletEvent;
use DateTime;
use DB;

class WalletData extends WalletAdapters\Adapter{

    public static $latitude  = 0.0,
                  $longitude = 0.0;

    /*
     * Make Transaction without Model ( For Transfer Money )
     */

    private static $makeTransactionWithoutModel   = false;
    private static $makeTransactionWithoutBalance = false;

    public static $transactionsTypeID  = 1;
    public static $transactionsComment = null;


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
        $wallet = Wallet::find($walletID);
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
        $wallet = Wallet::find($walletID);
        if(!$wallet){
            return $return;
        }

        $modelType = self::$modelType;
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

    public static function getWalletOwnerTypeModel($type){
        if(array_key_exists($type,self::$ownerType)){
            return self::$ownerType[$type];
        }
        return false;
    }

    public static function getWalletOwnerType($model){
        if(in_array($model,self::$ownerType)){
            return array_search($model,self::$ownerType);
        }
        return false;
    }

    /**
     * @param $ownerType
     * @param int $ID
     * @return mixed
     */

    public static function balance($walletID,$dateTo = 'now'){
        $wallet = self::getWallet($walletID);
        if(!$wallet) return false;

        if($dateTo == 'now'){
            return $wallet->balance;
        }else{
            return self::getBalanceTransaction($wallet,$dateTo);
        }

    }


    /**
     * @param $ownerType
     * @param int $ID
     * @param string $dateTo
     */

    public static function getBalanceTransaction($walletID, $dateTo = 'now'){

        $wallet = self::getWallet($walletID);
        if(!$wallet) return false;

        // FROM
        $transactionFrom = $wallet->transactionFrom()
            ->whereIn('status',['pending','paid'])
            ->select(DB::raw('SUM(`amount`) as `total`'));

        if($dateTo != 'now'){
            $checkdate = DateTime::createFromFormat("Y-m-d H:i:s", $dateTo);
            if($checkdate === false){
                $transactionFrom->whereRaw('DATE(`created_at`) <= "'.$dateTo.'"');
            }else{
                $transactionFrom->whereRaw('`created_at` <= "'.$dateTo.'"');
            }
        }

        $fromAmount = $transactionFrom->first();

        // TO
        $transactionTo   = $wallet->transactionTo()
            ->whereIn('status',['pending','paid'])
            ->select(DB::raw('SUM(`amount`) as `total`'));

        if($dateTo != 'now'){
            $checkdate = DateTime::createFromFormat("Y-m-d H:i:s", $dateTo);
            if($checkdate === false){
                $transactionTo->whereRaw('DATE(`created_at`) <= "'.$dateTo.'"');
            }else{
                $transactionTo->whereRaw('`created_at` <= "'.$dateTo.'"');
            }
        }

        $toAmount = $transactionTo->first();
        // TO
        return $toAmount->total - $fromAmount->total;
    }

    /**
     * @param $fromUserType
     * @param int $fromID
     * @param $toUserType
     * @param int $toID
     */

    public static function makeTransaction($amount,$transactionsType,$fromWalletID,$toWalletID,$modelType,$modelID,$creatableType = null,$creatableID = null,$status = 'pending'){
        // Start moneyTransfer Event
        event(new WalletEvent([
            'event'=> 'start_moneyTransfer',
            'amount'=> $amount,
            'transactionsType'=> $transactionsType,
            'fromWalletID'=> $fromWalletID,
            'toWalletID'=> $toWalletID,
            'modelType'=> $modelType,
            'modelID'=> $modelID,
            'status'=> $status
        ]));


        $fromWallet     = self::getWallet($fromWalletID);
        $toWallet       = self::getWallet($toWalletID);


        $getFromBalance = self::balance($fromWallet);

        // Start Validation
        if(!$fromWallet || !$toWallet){
            return ['status'=>false,'error_code'=> 1];
        }elseif(!is_numeric($amount) || $amount <= 0){
            return ['status'=>false,'error_code'=> 2];
        }elseif(!in_array($transactionsType,self::$transactionsType)){
            return ['status'=>false,'error_code'=> 3];
        }elseif(!in_array($status,self::$statusType)){
            return ['status'=>false,'error_code'=> 4];
        }elseif(!self::$makeTransactionWithoutModel && !array_key_exists($modelType,self::$modelType)){
            return ['status'=>false,'error_code'=> 5];
        }elseif($fromWallet->id == $toWallet->id){
            return ['status'=>false,'error_code'=> 6];
        }elseif($fromWallet->type == 'payment' && $toWallet->type == 'e-commerce'){
            return ['status'=>false,'error_code'=> 7];
        }elseif($transactionsType == 'wallet' && $amount > $getFromBalance && self::$makeTransactionWithoutBalance === false){
            // Check if wallet balance is bigger than or equal amount
            return ['status'=>false,'error_code'=> 8,'balance'=>$getFromBalance];
        }


        $walletTransaction = new \stdClass();
        DB::transaction(function () use($amount,$transactionsType,$fromWallet,$toWallet,$modelType,$modelID,$status,$creatableType,$creatableID,&$walletTransaction) {
            // Start moneyTransfer Transaction Event
            event(new WalletEvent(['event'=> 'startTransaction_moneyTransfer']));

            // Start Transfer Money
            // Transactions Type
            switch ($transactionsType){
                case 'wallet':
                    if($status == 'pending'){
                        $fromWallet->decrement('balance',$amount);
                    } elseif ($status == 'paid'){
                        $fromWallet ->decrement('balance',$amount);
                        $toWallet   ->increment('balance',$amount);
                    }

                    $walletTransaction = WalletTransaction::create([
                        'model_id'      => $modelID ?? null,
                        'model_type'    => self::$modelType[$modelType] ?? null,
                        'amount'        => $amount,
                        'from_id'       => $fromWallet->id,
                        'to_id'         => $toWallet->id,
                        'type'          => $transactionsType,
                        'status'        => $status,
                        'latitude'      => self::$latitude,
                        'longitude'     => self::$longitude,
                        'creatable_id'  => $creatableID,
                        'creatable_type'=> $creatableType,
                        'ip'            => getRealIP(),
                        'transaction_type_id' => self::$transactionsTypeID,
                        'comment' => self::$transactionsComment
                    ]);

                    break;

                case 'cash':
                    $walletTransaction = WalletTransaction::create([
                        'model_id'      => $modelID ?? null,
                        'model_type'    => self::$modelType[$modelType] ?? null,
                        'amount'        => $amount,
                        'from_id'       => $fromWallet->id,
                        'to_id'         => $toWallet->id,
                        'type'          => $transactionsType,
                        'status'        => $status,
                        'latitude'      => self::$latitude,
                        'longitude'     => self::$longitude,
                        'creatable_id'  => $creatableID,
                        'creatable_type'=> $creatableType,
                        'ip'            => getRealIP(),
                        'transaction_type_id' => self::$transactionsTypeID,
                        'comment' => self::$transactionsComment
                    ]);
                    break;

                default:
                    return false;
                    break;
            }

            // Add Loyalty to Expenses
            self::addLoyaltyAfterTransaction(
                $amount,
                $walletTransaction->id,
                $fromWallet->walletowner->loyaltyWallet,
                $modelType,
                $transactionsType,
                'expenses',
                array_search($fromWallet->walletowner->modelPath,self::$ownerType),
                $status
            );

            // Add Loyalty to Reverse
            self::addLoyaltyAfterTransaction(
                $amount,
                $walletTransaction->id,
                $toWallet->walletowner->loyaltyWallet,
                $modelType,
                $transactionsType,
                'income',
                array_search($toWallet->walletowner->modelPath,self::$ownerType),
                $status
            );

            // Transactions Status
            TransactionsStatus::create([
                'transaction_id'    => $walletTransaction->id,
                'status'            => $status,
                'user_type'         => $creatableType,
                'user_id'           => $creatableID,
                'ip'            => getRealIP()
            ]);


            // ---- Indebtedness
            // ---- Indebtedness
            if(is_null($walletTransaction->model_id) && is_null($walletTransaction->model_type) && $walletTransaction->status == 'paid' ){
                $walletTransaction->with(['fromWallet.walletowner','toWallet.walletowner']);

                // From Staff To Staff
                if(
                    $walletTransaction->fromWallet->walletowner   instanceof Staff &&
                    $walletTransaction->toWallet->walletowner     instanceof Staff
                ){
                    $walletTransaction->fromWallet->walletowner->decrement('indebtedness',$walletTransaction->amount);
                    $walletTransaction->toWallet->walletowner->increment('indebtedness',$walletTransaction->amount);
                }elseif(
                    $walletTransaction->fromWallet->walletowner   instanceof MainWallets &&
                    $walletTransaction->toWallet->walletowner     instanceof Staff
                ){
                    $walletTransaction->toWallet->walletowner->increment('indebtedness',$walletTransaction->amount);
                }

            }
            // ---- Indebtedness
            // ---- Indebtedness


        });

        // End moneyTransfer Transaction Event
        event(new WalletEvent(['event'=> 'endTransaction_moneyTransfer']));

        return $walletTransaction;
    }


    /**
     * @param $amount
     * @param $transaction_id
     * @param $toWalletID
     * @param $modelType
     * @param $transactionsType
     * @param $pay_type
     * @param $owner
     * @param $status
     * @return bool
     *
     * Loyalty Programs
     * System Will add new transaction to loyalty wallet using
     * wallet_transaction table
     */
    private static function addLoyaltyAfterTransaction($amount, $transactionID, $toWalletID, $modelType, $transactionsType, $payType, $owner, $status){


        $getToWalletData = $toWalletID;
            /*Wallet::where('id','=',$toWalletID)
            ->where('type','=','loyalty')
            ->first();*/

        if(!$getToWalletData) return false;

        $loyaltyProgram = LoyaltyPrograms::select('list')
            ->where('type','=',$modelType)
            ->where('transaction_type','=',$transactionsType)
            ->where('status','=','active')
            ->where('pay_type','=',$payType)
            ->where('owner','=',$owner)
            ->first();


        if(
            $loyaltyProgram &&
            isset($loyaltyProgram->list) &&
            !empty($loyaltyProgram->list) &&
            is_array($loyaltyProgram->list)
        ){
            if($loyaltyProgram->list['type'] == 'static'){
                if(
                    $loyaltyProgram->list['list']['amount'] &&
                    $loyaltyProgram->list['list']['point']
                ){
                    $calculateLoyaltyExpenses = @$amount/$loyaltyProgram->list['list']['amount'];
                    if(strpos($calculateLoyaltyExpenses,'.')){
                        $calculateLoyaltyExpenses = explode('.',$calculateLoyaltyExpenses)[0];
                    }

                    if($calculateLoyaltyExpenses){
                        $pointsToTransfare = $calculateLoyaltyExpenses*$loyaltyProgram->list['list']['point'];

                        DB::beginTransaction();
                            if ($status == 'paid'){
                                $getToWalletData->increment('balance',$pointsToTransfare);
                            }
                            LoyaltyWalletTransaction::create([
                                'transaction_id'=> $transactionID,
                                'point'         => $pointsToTransfare,
                                'from_id'       => setting('loyalty_wallet_id'),
                                'to_id'         => $getToWalletData->id,
                                'status'        => $status,
                                'ip'            => getRealIP()
                            ]);
                        DB::commit();
                        return true;
                    }

                }
            }else{
                if(
                    is_array($loyaltyProgram->list['list']) &&
                    !empty($loyaltyProgram->list['list'])
                ){
                    foreach ($loyaltyProgram->list['list'] as $key => $value) {
                        if($amount >= $value['from_amount'] && $amount <= $value['to_amount']){

                            DB::beginTransaction();
                                if ($status == 'paid'){
                                    $getToWalletData->increment('balance',$value['point']);
                                }
                                LoyaltyWalletTransaction::create([
                                    'transaction_id'=> $transactionID,
                                    'point'         => $value['point'],
                                    'from_id'       => setting('loyalty_wallet_id'),
                                    'to_id'         => $getToWalletData->id,
                                    'status'        => $status,
                                    'ip'            => getRealIP()
                                ]);
                            DB::commit();
                            return true;

                            break;
                        }
                    }
                }
            }
        }

        return false;

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
    public static function changeTransactionStatus($transactionID, $status,$creatableType = null,$creatableID = null,$comment = null){
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

                    if($data->status == 'pending'){
                        $data->toWallet()->increment('balance',$data->amount);
                        $data->update(['status'=> $status]);

                        // Loyalty Transaction
                        $loyaltyTransaction = LoyaltyWalletTransaction::where('transaction_id','=',$data->id)
                            ->get();
                        if($loyaltyTransaction){
                            foreach ($loyaltyTransaction as $key => $value){
                                $value->toWallet()->increment('balance',$value->point);
                                $value->update(['status'=> $status]);
                            }
                        }
                    }else{
                        $data->toWallet()->increment('balance',$data->amount);
                        $data->fromWallet()->decrement('balance',$data->amount);
                        $data->update(['status'=> $status]);

                        // Loyalty Transaction
                        $loyaltyTransaction = LoyaltyWalletTransaction::where('transaction_id','=',$data->id)
                            ->get();
                        if($loyaltyTransaction){
                            foreach ($loyaltyTransaction as $key => $value){
                                $value->toWallet()->increment('balance',$value->point);
                                $value->fromWallet()->decrement('balance',$value->point);
                                $value->update(['status'=> $status]);
                            }
                        }
                    }

                    // ---- Indebtedness
                    // ---- Indebtedness
                    if(is_null($data->model_id) && is_null($data->model_type) ){
                        $data->with(['fromWallet.walletowner','toWallet.walletowner']);

                        // From Staff To Staff
                        if(
                            $data->fromWallet->walletowner   instanceof Staff &&
                            $data->toWallet->walletowner     instanceof Staff
                        ){
                            $data->fromWallet->walletowner->decrement('indebtedness',$data->amount);
                            $data->toWallet->walletowner->increment('indebtedness',$data->amount);
                        }elseif(
                            $data->fromWallet->walletowner   instanceof MainWallets &&
                            $data->toWallet->walletowner     instanceof Staff
                        ){
                            $data->toWallet->walletowner->increment('indebtedness',$data->amount);
                        }

                    }
                    // ---- Indebtedness
                    // ---- Indebtedness


                });
                break;
            case 'reverse':
                DB::transaction(function () use($data,$status) {
                    if($data->status == 'pending'){
                        $data->fromWallet()->increment('balance',$data->amount);

                        // Loyalty Transaction
                        $loyaltyTransaction = LoyaltyWalletTransaction::where('transaction_id','=',$data->id)
                            ->get();
                        if($loyaltyTransaction){
                            foreach ($loyaltyTransaction as $key => $value){
                                $value->fromWallet()->increment('balance',$value->point);
                                $value->update(['status'=> $status]);
                            }
                        }

                    }else{
                        $data->fromWallet() ->increment('balance',$data->amount);
                        $data->toWallet()   ->decrement('balance',$data->amount);

                        // Loyalty Transaction
                        $loyaltyTransaction = LoyaltyWalletTransaction::where('transaction_id','=',$data->id)
                            ->get();
                        if($loyaltyTransaction){
                            foreach ($loyaltyTransaction as $key => $value){
                                $value->fromWallet()->increment('balance',$value->point);
                                $value->toWallet()->decrement('balance',$value->point);
                                $value->update(['status'=> $status]);
                            }
                        }

                    }

                    $data->update(['status'=> $status]);


                    // ---- Indebtedness
                    // ---- Indebtedness
                    if(is_null($data->model_id) && is_null($data->model_type) ){
                        $data->with(['fromWallet.walletowner','toWallet.walletowner']);

                        // From Staff To Staff
                        if(
                            $data->fromWallet->walletowner   instanceof Staff &&
                            $data->toWallet->walletowner     instanceof Staff
                        ){
                            $data->fromWallet->walletowner->increment('indebtedness',$data->amount);
                            $data->toWallet->walletowner->decrement('indebtedness',$data->amount);
                        }elseif(
                            $data->fromWallet->walletowner   instanceof MainWallets &&
                            $data->toWallet->walletowner     instanceof Staff
                        ){
                            $data->toWallet->walletowner->decrement('indebtedness',$data->amount);
                        }

                    }
                    // ---- Indebtedness
                    // ---- Indebtedness


                });
                break;
        }

        // Transactions Status
        TransactionsStatus::create([
            'transaction_id'    => $data->id,
            'status'            => $status,
            'user_type'         => $creatableType,
            'user_id'           => $creatableID,
            'comment'           => $comment,
            'ip'            => getRealIP()
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
     * @param $ownerType
     * @param int $ID
     * @return mixed
     * @throws \Exception
     */
    public static function getWalletByUserData($ownerType, int $ID,$walletType){
        if(!in_array($walletType,self::$walletsType)){
            return false;
        }

        if($walletType == 'payment'){

            $wallet = (new $ownerType)->find($ID);
            if($wallet instanceof User){
                $wallet = $wallet->eCommerceWallet;
            }else{
                $wallet = $wallet->paymentWallet;
            }

        }else{
            $wallet = (new $ownerType)->find($ID)->eCommerceWallet;
        }

        return $wallet;

    }

}