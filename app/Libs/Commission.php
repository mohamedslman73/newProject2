<?php

namespace App\Libs;

use App\Models\CommissionList;
use App\Models\Merchant;
use App\Models\PaymentInvoice;
use App\Models\SpecialCommissionListData;
use App\Models\WalletSettlement;
use Illuminate\Support\Facades\DB;

function floorp($val, $precision = 2)
{
    $mult = pow(10, $precision);
    return floor($val * $mult) / $mult;
}

class CommissionReturn
{
    private $data, $modelPath;

    public function __construct($input, $modelPath)
    {
        $this->data = $input;
        $this->modelPath = $modelPath;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getModelPath()
    {
        return $this->modelPath;
    }
}

class Commission
{

    /**
     * @param $status
     * @param $msg
     * @param $code
     * @param array $data
     * @return array
     */

    private static $commissionData      = [],
        $commissionDataMD5   = [],
        $dataAfterSettlement = [];

    private static function outPut($status, $msg, $code, $data = [])
    {
        return [
            'status' => $status,
            'msg' => __($msg),
            'code' => $code,
            'data' => (object)$data
        ];
    }

    private static function calculatePercent($amount, $percent){
        return ($amount * $percent) / 100;
    }

    private static function getCommissionData($ID,$creatable){

        $agentID = 0;
        if($creatable instanceof Merchant && $creatable->parent_id){
            $agentID = $creatable->parent_id;
        }

        $MD5Hash = md5('ID:'.$ID.'|AgentID:'.$agentID);
        if (isset(self::$commissionDataMD5[$MD5Hash])){
            return [
                'commission_list'           => self::$commissionData['commission_list'][$ID],
                'special_commission_list'   => self::$commissionData['special_commission_list'][$agentID][$ID],
            ];
        }

        self::$commissionData['commission_list'][$ID] = CommissionList::where('id', $ID)->first();
        if($agentID){
            self::$commissionData['special_commission_list'][$agentID][$ID] = SpecialCommissionListData::join('special_commission_list','special_commission_list.id','=','special_commission_list_data.special_commission_list_id')
                ->where('special_commission_list_data.commission_list_id',$ID)
                ->where('special_commission_list.merchant_id',$agentID)
                ->select([
                    'special_commission_list_data.commission_list_id as id',
                    'special_commission_list_data.commission_type',
                    'special_commission_list_data.condition_data',

//                    'special_commission_list.active_system_commission',
//                    'special_commission_list.active_agent_commission',
//                    'special_commission_list.active_merchant_commission'
                ])
                ->first();
        }else{
            self::$commissionData['special_commission_list'][$agentID][$ID] = null;
        }

        self::$commissionDataMD5[$MD5Hash] = true;

        return [
            'commission_list'           => self::$commissionData['commission_list'][$ID],
            'special_commission_list'   => self::$commissionData['special_commission_list'][$agentID][$ID],
        ];
    }

    public static function calculateCommission($amount, int $commissionListID,$creatable){
        $commissionData = self::getCommissionData($commissionListID,$creatable);

        if (!$commissionData['commission_list']) {
            return self::outPut(false, 'Commission List not available', 20001);
        }

        if($commissionData['special_commission_list']){
            $commissionData = $commissionData['special_commission_list'];
            $commissionFrom = 'agent';
        }else{
            $commissionData = $commissionData['commission_list'];
            $commissionFrom = 'system';
        }


        // Proccess Commission Type [ONE]
        if ($commissionData->commission_type == 'one') {
            // Proccess Charge Type Fixed
            if ($commissionData->condition_data['charge_type'] == 'fixed') {
                return self::outPut(true, 'Data With Fixed Commission', 100, [
                    'system_commission' => $commissionData->condition_data['system_commission'],
                    'merchant_commission' => $commissionData->condition_data['merchant_commission'],
                    'agent_commission' => $commissionData->condition_data['agent_commission'],

                    'DB_charge_type' => $commissionData->condition_data['charge_type'],
                    'DB_system_commission' => $commissionData->condition_data['system_commission'],
                    'DB_merchant_commission' => $commissionData->condition_data['merchant_commission'],
                    'DB_agent_commission' => $commissionData->condition_data['agent_commission'],

                    'commission_from'=> $commissionFrom
                ]);
            } else {
                return self::outPut(true, 'Data With Percent Commission', 100, [
                    'system_commission' => self::calculatePercent($amount, $commissionData->condition_data['system_commission']),
                    'merchant_commission' => self::calculatePercent($amount, $commissionData->condition_data['merchant_commission']),
                    'agent_commission' => self::calculatePercent($amount, $commissionData->condition_data['agent_commission']),

                    'DB_charge_type' => $commissionData->condition_data['charge_type'],
                    'DB_system_commission' => $commissionData->condition_data['system_commission'],
                    'DB_merchant_commission' => $commissionData->condition_data['merchant_commission'],
                    'DB_agent_commission' => $commissionData->condition_data['agent_commission'],

                    'commission_from'=> $commissionFrom
                ]);
            }
        } else {
            $lastConditionDataInLoop = [];
            foreach ($commissionData->condition_data as $key => $value) {
                if (isset($value['system_commission'], $value['merchant_commission'], $value['agent_commission'], $value['charge_type'], $value['amount_from'], $value['amount_to'])) {
                    $lastConditionDataInLoop = $value;
                    if ($amount >= $value['amount_from'] && $amount <= $value['amount_to']) {
                        if ($value['charge_type'] == 'fixed') {
                            return self::outPut(true, 'Data With Fixed Commission', 100, [
                                'system_commission' => $value['system_commission'],
                                'merchant_commission' => $value['merchant_commission'],
                                'agent_commission' => $value['agent_commission'],

                                'DB_charge_type' => $value['charge_type'],
                                'DB_system_commission' => $value['system_commission'],
                                'DB_merchant_commission' => $value['merchant_commission'],
                                'DB_agent_commission' => $value['agent_commission'],

                                'commission_from'=> $commissionFrom
                            ]);
                        } else {
                            return self::outPut(true, 'Data With Percent Commission', 100, [
                                'system_commission' => self::calculatePercent($amount, $value['system_commission']),
                                'merchant_commission' => self::calculatePercent($amount, $value['merchant_commission']),
                                'agent_commission' => self::calculatePercent($amount, $value['agent_commission']),

                                'DB_charge_type' => $value['charge_type'],
                                'DB_system_commission' => $value['system_commission'],
                                'DB_merchant_commission' => $value['merchant_commission'],
                                'DB_agent_commission' => $value['agent_commission'],

                                'commission_from'=> $commissionFrom
                            ]);
                        }
                    }
                } else {
                    return self::outPut(false, 'Commission List not available', 20001);
                }
            }


            // If Commission bigger than loop amount
            if(
                !empty($lastConditionDataInLoop) &&
                $amount >= $lastConditionDataInLoop['amount_from'] &&
                $amount >  $lastConditionDataInLoop['amount_to']
            ){

                if ($lastConditionDataInLoop['charge_type'] == 'fixed') {
                    return self::outPut(true, 'Data With Fixed Commission', 100, [
                        'system_commission' => $lastConditionDataInLoop['system_commission'],
                        'merchant_commission' => $lastConditionDataInLoop['merchant_commission'],
                        'agent_commission' => $lastConditionDataInLoop['agent_commission'],

                        'DB_charge_type' => $lastConditionDataInLoop['charge_type'],
                        'DB_system_commission' => $lastConditionDataInLoop['system_commission'],
                        'DB_merchant_commission' => $lastConditionDataInLoop['merchant_commission'],
                        'DB_agent_commission' => $lastConditionDataInLoop['agent_commission'],

                        'commission_from'=> $commissionFrom
                    ]);
                } else {
                    return self::outPut(true, 'Data With Percent Commission', 100, [
                        'system_commission' => self::calculatePercent($amount, $lastConditionDataInLoop['system_commission']),
                        'merchant_commission' => self::calculatePercent($amount, $lastConditionDataInLoop['merchant_commission']),
                        'agent_commission' => self::calculatePercent($amount, $lastConditionDataInLoop['agent_commission']),

                        'DB_charge_type' => $lastConditionDataInLoop['charge_type'],
                        'DB_system_commission' => $lastConditionDataInLoop['system_commission'],
                        'DB_merchant_commission' => $lastConditionDataInLoop['merchant_commission'],
                        'DB_agent_commission' => $lastConditionDataInLoop['agent_commission'],

                        'commission_from'=> $commissionFrom
                    ]);
                }

            }

        }

        return self::outPut(false, 'Commission List not available', 20001);

    }

    public static function paymentSettlement($formDateTime, $toDateTime, $modelPath = 'App\Models\Merchant', $modelID = null){

        $invoicesByMerchantsEloquent = PaymentInvoice::join('payment_transactions', 'payment_transactions.id', '=', 'payment_invoice.payment_transaction_id')
            ->join('payment_services', 'payment_services.id', '=', 'payment_transactions.payment_services_id')
            ->where('payment_invoice.creatable_type', '=', $modelPath)
            ->where('payment_invoice.status', '=', 'paid')
            ->whereNull('payment_invoice.wallet_settlement_id')
            ->groupBy('payment_invoice.creatable_id')
            ->with('creatable');

        if ($modelID) {
            $invoicesByMerchantsEloquent->where('payment_invoice.creatable_id', '=', $modelID);
        }

        whereBetween($invoicesByMerchantsEloquent, '`payment_invoice`.`created_at`', $formDateTime, $toDateTime);

        $invoicesByMerchants = $invoicesByMerchantsEloquent->get([
            'payment_invoice.creatable_id as merchant_id',
            'payment_invoice.creatable_id',
            'payment_invoice.creatable_type',
        ]);

        if ($invoicesByMerchants->isEmpty()) {
            return self::outPut(false, 'There are no data to settled', 20001);
        }


        $settlementProcess = [];

        foreach ($invoicesByMerchants as $keyMerchant => $merchant) {

            // Get All Invoices to sattled
            $getInvoicesEloquent = PaymentInvoice::join('payment_transactions', 'payment_transactions.id', '=', 'payment_invoice.payment_transaction_id')
                ->join('payment_services', 'payment_services.id', '=', 'payment_transactions.payment_services_id')
                ->where('payment_invoice.creatable_type', '=', $merchant->creatable_type)
                ->where('payment_invoice.creatable_id', '=', $merchant->merchant_id)
                ->where('payment_invoice.status', '=', 'paid')
                ->whereNull('payment_invoice.wallet_settlement_id')
                ->with(['creatable','payment_transaction'=>function($query){
                    $query->with(['payment_services'=>function($query2){
                        $query2->with('payment_service_provider');
                    }]);
                }]);

            whereBetween($getInvoicesEloquent, 'payment_invoice.created_at', $formDateTime, $toDateTime);

            $getInvoices = $getInvoicesEloquent->get([
                // Invoice
                'payment_invoice.id',
                'payment_invoice.creatable_id as merchant_id',
                'payment_invoice.payment_transaction_id',
                'payment_invoice.creatable_id',
                'payment_invoice.creatable_type',
                'payment_invoice.total',
                'payment_invoice.total_amount',
                'payment_invoice.status',
                'payment_invoice.created_at',
                'payment_invoice.updated_at',

                // Service
                'payment_services.id as payment_service_id',
                'payment_services.commission_list_id'

            ]);


            if (!$getInvoices){
                continue;
            }

            $settlementProcess[$merchant->merchant_id] = [
                'success' => [], // Invoice ID
                'error' => [], // Invoice ID
                'system_commission' => 0,
                'merchant_commission' => 0,
                'agent_commission' => 0,
                'agent_model'=> $getInvoices->first()->creatable->parent,
                'model'=> $merchant->creatable,
                'model_type'=> $merchant->creatable_type,
                'model_id'=> $merchant->creatable_id,
                'from_date_time'=> $formDateTime,
                'to_date_time'=> $toDateTime
            ];

            foreach ($getInvoices as $key => $value) {
                $calculateCommission = self::calculateCommission($value->total_amount, $value->commission_list_id,$value->creatable);

                if (!$calculateCommission['status']) {
                    $settlementProcess[$merchant->merchant_id]['error'][] = [
                        'invoice_id' => $value->id,
                        'error_msg' => $calculateCommission['msg']
                    ];
                } else {

                    $systemCommission   = 0;
                    $agentCommission    = 0;
                    $merchantCommission = $calculateCommission['data']->merchant_commission;

                    // Is merchant
                    if(
                        $value->creatable->modelPath == 'App\Models\Merchant' &&
                        $value->creatable->is_reseller == 'in-active' &&
                        !is_null($value->creatable->parent)
                    ){

                        $systemCommission = $calculateCommission['data']->system_commission;
                        $agentCommission  = $calculateCommission['data']->agent_commission;
                    }else{
                        $systemCommission = $calculateCommission['data']->system_commission + $calculateCommission['data']->agent_commission;
                    }


                    $settlementProcess[$merchant->merchant_id]['system_commission']   += $systemCommission;
                    $settlementProcess[$merchant->merchant_id]['agent_commission']    += $agentCommission;
                    $settlementProcess[$merchant->merchant_id]['merchant_commission'] += $merchantCommission;

                    // Invoice ID Success
                    $settlementProcess[$merchant->merchant_id]['success'][] = [
                        'service_name'              => $value->payment_transaction->payment_services->payment_service_provider->name_en.' '.$value->payment_transaction->payment_services->name_en,
                        'service_id'                => $value->payment_transaction->payment_services->id,

                        'invoice_id'                => $value->id,
                        'amount'                    => $value->total_amount,
                        'system_commission'         => $systemCommission,
                        'agent_commission'          => $agentCommission,
                        'merchant_commission'       => $merchantCommission,

                        'DB_charge_type'            => $calculateCommission['data']->DB_charge_type,
                        'DB_system_commission'      => $calculateCommission['data']->DB_system_commission,
                        'DB_agent_commission'       => $calculateCommission['data']->DB_agent_commission,
                        'DB_merchant_commission'    => $calculateCommission['data']->DB_merchant_commission,
                        'commission_from'           => $calculateCommission['data']->commission_from
                    ];

                }
            }
        }


        self::$dataAfterSettlement = self::outPut(true, 'Done Settlement', 100, [
            'settlement' => $settlementProcess,
            'modelPath' => $modelPath
        ]);

        return self::$dataAfterSettlement;

    }

    public static function paymentAlreadySettlement($formDate, $toDate, $modelPath = 'App\Models\Merchant', $modelID = null){

        $eloquentData = WalletSettlement::join('wallet','wallet.id','=','wallet_settlement.wallet_id')
            ->where('wallet_settlement.from_date_time',$formDate.' 00:00:00')
            ->where('wallet_settlement.to_date_time',$toDate.' 23:59:59')
            ->where('wallet.walletowner_type',$modelPath)
            ->select([
                'wallet_settlement.*',
            ])
            ->with([
                'payment_invoice'=>function($query){
                    $query->with(['creatable','payment_transaction'=>function($query2){
                        $query2->with(['payment_services'=>function($query3){
                            $query3->with('payment_service_provider');
                        }]);
                    }]);
                },
                'wallet'=> function($query){
                    $query->with('walletowner');
                }
            ]);


        if ($modelID) {
            $eloquentData->where('wallet.walletowner_id', '=', $modelID);
        }

        $eloquentData = $eloquentData
            ->get()
            ->toArray();

        $systemCommission = $merchantCommission = $agentCommission = 0;


        if($eloquentData){
            $systemCommission   = array_sum(array_column($eloquentData,'system_commission'));
            $merchantCommission = array_sum(array_column($eloquentData,'merchant_commission'));
            $agentCommission    = array_sum(array_column($eloquentData,'agent_commission'));
        }

        return [
            'status'=> $eloquentData ? true : false,
            'data'  => $eloquentData,
            'total' => [
                'system_commission'     => $systemCommission,
                'merchant_commission'   => $merchantCommission,
                'agent_commission'      => $agentCommission
            ]
        ];

    }

    /*
     * To Save Settlement
     */

    public static function savePaymentSettlement()
    {
        if(empty(self::$dataAfterSettlement)){
            return self::outPut(false, 'There are no data to settled', 20001);
        }

        $data      = (array)self::$dataAfterSettlement['data']->settlement;
        $modelPath = self::$dataAfterSettlement['data']->modelPath;


        foreach ($data as $key => $value) {

            $merchantID = $key;
            $merchant   = $value['model'];

            if (!$merchant) {
                setError($value, $modelPath, $merchantID,__('Model Unavailable'));
                continue;
            } elseif (!$merchant->paymentWallet) {
                setError($value, $modelPath, $merchantID,__('Payment Wallet Unavailable'));
                continue;
            }

            $walletSettlement = WalletSettlement::create([
                'staff_id'              => $merchant->staff_id,
                'wallet_id'             => $merchant->paymentWallet->id, // Merchant Payment Wallet
                'agent_wallet_id'       => ($value['agent_model'] == null) ? null : $value['agent_model']->paymentWallet->id, // Agent Payment Wallet
                'system_commission'     => $value['system_commission'],
                'merchant_commission'   => $value['merchant_commission'],
                'agent_commission'      => $value['agent_commission'],
                'from_date_time'        => $value['from_date_time'],
                'to_date_time'          => $value['to_date_time'],
                'num_success'           => count($value['success']),
                'num_error'             => count($value['error'])
            ]);


            DB::beginTransaction();

            foreach ($value['success'] as $keyInvoice => $valueInvoice) {
                PaymentInvoice::where('id', '=', $valueInvoice['invoice_id'])->update([
                    'wallet_settlement_id' => $walletSettlement->id,
                    'wallet_settlement_data' => @serialize($valueInvoice)
                ]);
            }

            WalletData::makeTransactionWithoutModel  (true);
            WalletData::makeTransactionWithoutBalance(true);

            $transferToSystem = WalletData::makeTransaction(
                $value['system_commission'],
                'wallet',
                setting('payment_wallet_id'),
                setting('payment_settlement_wallet_id'),
                'settlement',
                $walletSettlement->id,
                null,
                null,
                'paid'
            );

            if(!is_null($value['agent_model']) && $value['agent_commission'] > 0){
                $transferToAgent = WalletData::makeTransaction(
                    $value['agent_commission'],
                    'wallet',
                    setting('payment_wallet_id'),
                    $value['agent_model']->paymentWallet->id,
                    'settlement',
                    $walletSettlement->id,
                    null,
                    null,
                    'paid'
                );
            }

            $transferToMerchant = WalletData::makeTransaction(
                $value['merchant_commission'],
                'wallet',
                setting('payment_wallet_id'),
                $merchant->paymentWallet->id,
                'settlement',
                $walletSettlement->id,
                null,
                null,
                'paid'
            );

            $walletSettlement->update([
                'status' => 'done'
            ]);

            if(
                (!$transferToSystem || is_array($transferToSystem)) ||
                (!$transferToMerchant || is_array($transferToMerchant)) ||
                (isset($transferToAgent) && (!$transferToAgent || is_array($transferToAgent)) )
            ){
                setError($value, $modelPath, $merchantID,__('Error Create Transaction'));
                DB::rollBack();

                $walletSettlement->update([
                    'status' => 'error'
                ]);
                continue;
            }


            DB::commit();

        }

    }


}