<?php

function BypassMerchantPermissions(){
    return [
        'merchant.access.denied',
        'panel.merchant.home',
        /*
         * Ajax requests
         */
        'panel.merchant.get','panel.merchant.post',
        'panel.merchant.user.update-info','panel.merchant.user.edit-info','panel.merchant.user.change-password',
        /*
         * News
         */
        'panel.merchant.news.home','panel.merchant.news.show','panel.merchant.news.category',
        /*
         * Update user info
         */
        'panel.merchant.user.update-info','panel.merchant.user.edit-info',
        'panel.merchant.user.change-password','panel.merchant.user.update-password',
        /*
         * Merchant API
         */
        'panel.merchant.user.info',
        'panel.merchant.payment.getDatabase','panel.merchant.aboutus','panel.merchant.latest-apk',
        'panel.merchant.checkversion',
    ];
}

function merchantcan($routename,$merchantstaffid = null){
    $userObj = ((isset($merchantstaffid)) ? \App\Models\MerchantStaff::where('id',$merchantstaffid)->first() : request()->user());
    if(!$userObj)
        return false;
    $IgnredRoytes = BypassMerchantPermissions();
    static $merchantPermissions;
    if(is_null($merchantPermissions))
        $merchantPermissions = array_merge(\App\Models\MerchantStaff::MerchantStaffPerms($userObj->id)->toArray(),BypassMerchantPermissions());
    if($userObj->merchant()->merchant_staff_group->first()->id != $userObj->merchant_staff_group_id) {
        if(is_array($routename)) {
            $arr = array_diff($routename,$merchantPermissions);
            return (!$arr) ? true : ((count($arr) == count($routename))? false:true);
        } else {
            return (in_array($routename,$merchantPermissions)) ? true : false;
        }
    } else
        return true;
}

function MediaFiles($name=false,$id=null){
    $paths = [
        'merchant_images' => (($id)?'merchants/'.$id.'/images':'merchants/tmp'),
    ];
    if($name){
        return $paths[$name];
    }

    return $paths;
}