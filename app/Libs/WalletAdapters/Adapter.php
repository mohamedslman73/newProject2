<?php

namespace App\Libs\WalletAdapters;

use App\Models\Wallet;


class Adapter{

    protected static $types             = ['order','invoice','settlement'];
    protected static $transactionsType  = ['wallet','cash'];
    protected static $statusType        = ['pending','paid','reverse'];
    public    static $walletsType       = ['payment','e-commerce','loyalty'];

    public static $modelType = [
        'order'         => 'App\Models\Order',
        'invoice'       => 'App\Models\PaymentInvoice',
        'settlement'    => 'App\Models\WalletSettlement'
    ];
    public static $merchantModelType = [
        'transfer' => null,
        'order'         => 'App\Models\Order',
        'settlement'    => 'App\Models\WalletSettlement'
    ];


    public static $ownerType = [
        'users'             => 'App\Models\User',
        'staff'             => 'App\Models\Staff',
        'merchant'          => 'App\Models\Merchant',
        'service_providers' => 'App\Models\PaymentServiceProviders',
        'main_wallets'      => 'App\Models\MainWallets',
    ];


    public static function getWallet($walletID){
        if(is_numeric($walletID)){
            $wallet = Wallet::find($walletID);
        }elseif($walletID instanceof Wallet){
            $wallet = $walletID;
        }else{
            return false;
        }

        return $wallet;
    }


}