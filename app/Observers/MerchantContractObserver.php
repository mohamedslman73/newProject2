<?php
namespace App\Observers;
use App\Models\MerchantContract;
use App\Models\Merchant;

class MerchantContractObserver {

    public function created(MerchantContract $MerchantContract){
        Merchant::where('id',$MerchantContract->merchant_id)
            ->update(['merchant_contract_id'=>$MerchantContract->id]);
    }

}
