<?php

function getWalletOwnerName(&$wallet,$systemLang,$linkType = null){
    $walletOwner = $wallet->walletowner;

    $url = '';
    if($linkType == 'wallet'){
        $url = route('system.wallet.show',$wallet->id);
    }

    if(
        $walletOwner instanceof \App\Models\Staff ||
        $walletOwner instanceof \App\Models\User
    ){

        if($linkType == 'profile'){
            if($walletOwner instanceof \App\Models\Staff){
                $url = route('system.staff.show',$walletOwner->id);
            }else{
                $url = route('system.user.show',$walletOwner->id);
            }
        }

        $return = $walletOwner->firstname.' '.$walletOwner->lastname;
    }elseif(
        $walletOwner instanceof \App\Models\Merchant
    ){

        if($linkType == 'profile'){
            $url = route('merchant.merchant.show',$walletOwner->id);
        }

        $return = $walletOwner->{'name_'.$systemLang};
    }elseif($walletOwner instanceof \App\Models\MainWallets){
        if($linkType == 'profile'){
            $url = route('system.wallet.main-wallets');
        }

        $return = $walletOwner->name;
    }else{
        $return = '[UNKNOWN]';
    }

    if(!empty($url)){
        return '<a href="'.$url.'">'.$return.'</a>';
    }

    return $return;

}