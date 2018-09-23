<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libs\WalletData;
use App\Models\MainWallets;
use App\Models\Merchant;
use App\Models\Staff;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use App\Models\Area;
use Illuminate\Support\Facades\Hash;


class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {




        Validator::extend('createMerchantStaffID', function ($attribute, $value, $parameters,$validator) {
            $user = Auth::user();
            if(is_null($user->supervisor_id) && in_array($value,$user->managed_staff_ids())){
                return true;
            }elseif(!is_null($user->supervisor_id) && $value == $user->id){
                return true;
            }

            return false;
        });

        Validator::extend('mobile', function ($attribute, $value, $parameters,$validator) {
            if(mb_strlen($value) != 11){
                return false;
            }
            return true;
        });

        Validator::extend('cleanerAttendanceToday', function ($attribute, $value, $parameters,$validator) {

        });


        Validator::extend('PasswordCheck', function ($attribute, $value, $parameters,$validator) {
            if(Hash::check($value,$parameters[0])) {
                return true;
            } else {
                $validator->errors()->add('current_password', __('Wrong Current password'));
                return false;
            }
        });



        Validator::extend('area_id', function ($attribute, $value, $parameters, $validator) {
            if(!is_array($value)) return false;
            $lastID = getLastNotEmptyItem($value);
            if(!$lastID) return false;

            if(Area::find($lastID)){
                return true;
            }else{
                return false;
            }
        });

        Validator::extend('is_array', function ($attribute, $value, $parameters, $validator) {
            if(is_array($value)){
                return true;
            }
            return false;
        });


        // Transfer Money supervisor
        Validator::extend('transferMoneyBySupervisor', function ($attribute, $value, $parameters, $validator) {

            $wallet = Wallet::find($parameters[0]);
            if(!$wallet || $wallet->type != 'payment'){
                return false;
            }
            $getSendToWallet = $wallet;
            
            if(!\Auth::user()->is_supervisor() || \Auth::id() == $getSendToWallet->walletowner_id ){
                return false;
            }elseif(!in_array($getSendToWallet->walletowner_id,array_column(\Auth::user()->managed_staff->toArray(),'id'))){
                return false;
            }elseif($value > WalletData::balance(\Auth::user()->paymentWallet->id)){
                return false;
            }
            
            return true;
        });



        Validator::extend('transferMoneyByStaff', function ($attribute, $value, $parameters, $validator) {

            $wallet = Wallet::find($parameters[0]);
            if(!$wallet || $wallet->type != 'payment'){
                return false;
            }

            $getSendToWallet = $wallet;
            
            if(\Auth::user()->is_supervisor()){
                $staffMerchants = [];
                foreach (\Auth::user()->managed_staff()->with('merchant')->get() as $key => $value2){
                    if($value2->merchant->isNotEmpty()){
                        foreach ($value2->merchant as $merchant){
                            $staffMerchants[] = $merchant;
                        }
                    }
                }
                $staffMerchants  = collect($staffMerchants)->toArray();
            }else{
                $staffMerchants  = \Auth::user()->merchant->toArray();
            }

            if(!in_array($getSendToWallet->walletowner_id,array_column($staffMerchants,'id')) ){
                return false;
            }elseif($value > WalletData::balance(\Auth::user()->paymentWallet->id)){
                return false;
            }

            return true;
        });


        Validator::extend('checkStaffPassword', function ($attribute, $value, $parameters, $validator) {
            if(\Hash::check($value, \Auth::user()->password)){
                return true;
            }
            return false;
        });

        // Transfer Money supervisor





        Validator::extend('managed_staff', function ($attribute, $value, $parameters, $validator) {
            $data = Staff::leftJoin('permission_groups','permission_groups.id','=','staff.permission_group_id');

            if(is_array($parameters) && isset($parameters[1])){
                $supervisorID = $parameters[1];
                $data->where('staff.supervisor_id','=',$supervisorID);
            }else{
                $data->where('staff.id','!=',$value);
            }

            $data->where('permission_groups.is_supervisor','=','no')
                ->first();

            if(!$data){
                return true;
            }

            return false;
        });

        Validator::extend('checkMainWallet', function ($attribute, $value, $parameters, $validator) {
            $data = Wallet::where('id',$value)
                ->where('walletowner_type','App\Models\MainWallets')
                ->first();
            if($data){
                if($parameters[0] == 'transfer_out' && $data->walletowner->transfer_out == 'yes'){
                    return true;
                }elseif($parameters[0] == 'transfer_in' && $data->walletowner->transfer_in == 'yes'){
                    return true;
                }
            }
            return false;
        });


        Validator::extend('isSupervisorAndNotMe', function ($attribute, $value, $parameters, $validator) {
            if($value == \Auth::id()){
                return false;
            }

            $data = Staff::find($value);
            if($data && $data->is_supervisor()){
                return true;
            }
            return false;
        });


        Validator::extend('isSupervisorWalletAndNotMyWallet', function ($attribute, $value, $parameters, $validator) {
            $checkData = Wallet::join('staff','staff.id','=','wallet.walletowner_id')
                ->join('permission_groups','permission_groups.id','=','staff.permission_group_id')
                ->where('wallet.id','=',$value)
                ->where('wallet.walletowner_type','=','App\Models\Staff')
                ->where('permission_groups.is_supervisor','=','yes')
                ->get([
                    'wallet.id as wallet_id',
                    'staff.id as staff_id'
                ])
                ->toArray();
                

            if(!$checkData){
                return false;
            }else{
                $checkData = array_column($checkData,'wallet_id','staff_id');
                if(array_key_exists(Auth::id(),$checkData)){
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
