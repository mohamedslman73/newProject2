<?php


// @TODO: Handle Status HTML
function statusColor($status){
    return $status;
}

function countVisits(){
    $visitsCount = \App\Models\Visits::whereNull('comment_by_staff_id');
    if(!staffCan('show-tree-users-data',\Auth::id())){
        $visitsCount->whereIn('visits.staff_id',\Auth::user()->managed_staff_ids());
    }
    return $visitsCount->count();

}


function paymentDailyReportByEmail($date){

    // ---
    $Beebalance = \App\Libs\Payments\Adapters\Bee::balance()->data->balance;

    $supervisor_wallets = \App\Models\Wallet::where('type','payment')
        ->where('walletowner_type','App\Models\Staff')
        ->whereIn('walletowner_id',array_column(\App\Models\Staff::where('permission_group_id',5)->get(['id'])->toArray(),'id'))
        ->selectRaw("SUM(`balance`) as `sum`")
        ->first()
        ->sum;

    $sales_wallets = \App\Models\Wallet::where('type','payment')
        ->where('walletowner_type','App\Models\Staff')
        ->whereIn('walletowner_id',array_column(\App\Models\Staff::where('permission_group_id',6)->get(['id'])->toArray(),'id'))
        ->selectRaw("SUM(`balance`) as `sum`")
        ->first()
        ->sum;

    $merchantWallet = \App\Models\Wallet::where('type','payment')
        ->where('walletowner_type','App\Models\Merchant')
        ->selectRaw("SUM(`balance`) as `sum`")
        ->first()
        ->sum;
    // ---


    $data = [];
    $data['date']             = $date;
    $data['sdk_wallet']       = amount((string) \App\Libs\Payments\Adapters\Bee::balance()->data->balance,true);
    $data['actual_balance']   = amount($Beebalance-($supervisor_wallets+$sales_wallets+$merchantWallet),true);

    $data['supervisor_wallets'] = amount(
        \App\Models\Wallet::where('type','payment')
            ->where('walletowner_type','App\Models\Staff')
        ->whereIn('walletowner_id',array_column(\App\Models\Staff::where('permission_group_id',5)->get(['id'])->toArray(),'id'))
            ->selectRaw("SUM(`balance`) as `sum`")
            ->first()
            ->sum,
        true);
    $data['sales_wallets'] = amount(
        \App\Models\Wallet::where('type','payment')
            ->where('walletowner_type','App\Models\Staff')
        ->whereIn('walletowner_id',array_column(\App\Models\Staff::where('permission_group_id',6)->get(['id'])->toArray(),'id'))
            ->selectRaw("SUM(`balance`) as `sum`")
            ->first()
            ->sum,
        true);
    $data['merchant_wallets'] = amount(
        \App\Models\Wallet::where('type','payment')
            ->where('walletowner_type','App\Models\Merchant')
            ->selectRaw("SUM(`balance`) as `sum`")
            ->first()
            ->sum,
        true);


    $paymentInvoice = \App\Models\PaymentInvoice::whereRaw("DATE(`created_at`) = ?",[$date])
        ->where('status','paid')
        ->selectRaw("SUM(`total_amount`) as `sum`")
        ->selectRaw("COUNT(`id`) as `count`")
        ->first();

    $data['invoices_amount'] = amount($paymentInvoice->sum,true);
    $data['invoices_count']  = $paymentInvoice->count;

    $commission                   = \App\Libs\Commission::paymentSettlement($date.' 00:00:00',$date.' 23:59:59');
    $data['system_commission']    = @amount(array_sum(array_column($commission['data']->settlement,'system_commission')),true);
    $data['merchant_commission']  = @amount(array_sum(array_column($commission['data']->settlement,'merchant_commission')),true);

    $data['new_merchants'] = \App\Models\Merchant::whereRaw("DATE(`created_at`) = ?",[$date])->count();

    return $data;
}

function getStaffName($id,array $data){
    $firstname = array_column($data,'firstname','id');
    $lastname = array_column($data,'lastname','id');
    return @($firstname[$id].' '.$lastname[$id]);
}

function adminDefineUser($model,$id,$content){
    if($model == 'App\Models\Staff'){
        return $content;
    }elseif($model == 'App\Models\User'){
        return $content;
    }elseif($model =='App\Models\Merchant'){
        return  $content;
    }
}

function adminDefineUserWithName($model,$id,$lang){
    switch($model){
        case 'App\Models\MerchantStaff':
            $content = \App\Models\MerchantStaff::where('id','=',$id)->first()->Name;
            return '<a href="'.route('merchant.staff.show',[$id]).'">'.$content.'</a>';
            break;

        case 'App\Models\User':
            $content = \App\Models\User::where('id','=',$id)->first()->FullName;
            return '<a href="'.route('system.users.show',[$id]).'">'.$content.'</a>';
            break;

        case 'App\Models\Merchant':
            $content = \App\Models\User::Merchant('id','=',$id)->first()->{'name_'.$lang};
            return '<a href="'.route('merchant.merchant.show',[$id]).'">'.$content.'</a>';
            break;

        case 'App\Models\Staff':
        default:
            $content = \App\Models\Staff::where('id','=',$id)->first()->Fullname;
            return '<a href="'.route('system.staff.show',[$id]).'">'.$content.'</a>';
            break;

    }
}

function MerchantDefineUser($model,$id,$content){
    if($model == 'App\Models\Staff'){
        return $content;
    }elseif($model == 'App\Models\User'){
        return $content;
    }else{
        return '<a href="'.route('panel.merchant.employee.show',[$id]).'">'.$content.'</a>';
    }
}


function formError($error,$fieldName,$checkHasError = false){

    if($checkHasError){
        if($error->has($fieldName)){
            return ' has-danger';
        }else{
            return null;
        }
    }

    if($error->has($fieldName)){
        $return = '<p class="text-xs-left"><small class="danger text-muted">';

        foreach ($error->get($fieldName) as $errorMsg) {
            if(is_array($errorMsg)){
                $return .= implode(',',$errorMsg).'<br />';
            }else{
                $return .= $errorMsg.'<br />';
            }
        }
        $return .= '</small></p>';
        return $return;
    }else{
        return null;
    }

}

function generateMenu(array $array){
    $return = '';
    if(!isset($array['url'])){
        $array['url'] = '#';
    }

    if(!isset($array['icon'])){
        $array['icon'] = null;
    }

    if(!isset($array['class'])){
        $array['class'] = null;
    }

    if(!isset($array['aClass'])){
        $array['aClass'] = null;
    }


//    if(!empty($array['permission'])){
//        if(is_array($array['permission'])){
//            $oneTrue = false;
//            foreach($array['permission'] as $key => $value){
//                if(staffCan($value)){
//                    $oneTrue = true;
//                    break;
//                }
//            }
//
//            if(!$oneTrue){
//                return false;
//            }
//        }else{
//            if(!staffCan($array['permission'])){
//                return false;
//            }
//        }
//    }


    if(isset($array['permission'])){
        if(!staffCan($array['permission']))
            return false;
    }


    if(isset($array['permission']) && MenuRoute($array['permission'])){
        $array['class'] .= ' active';
    }
if(!empty($array['text']))
    $text = $array['text'];
    else
        $text = 'dddd';

    $return.= '<li class="nav-item'.iif(!empty($array['class']),' '.$array['class']).'">
                <a '.iif(!empty($array['aClass']),'class="'.$array['aClass'].'"').' href="'.iif(!empty($array['url']),' '.$array['url']).'">
                    '.iif(!empty($array['icon']),'<i class="'.$array['icon'].'"></i>').'
                    <span data-i18n="" class="menu-title">'.$text.'</span>';

    if(isset($array['count']) && !empty($array['count'])){
        $return.= '<span class="tag tag tag-primary tag-pill float-xs-right mr-2">'.$array['count'].'</span>';
    }

    $return.='</a>';

    if(isset($array['sub']) && !empty($array['sub'])){
        $return.= '<ul class="menu-content">';
        foreach ($array['sub'] as $key=> $value){
            $return.= generateMenu($value);
        }
        $return.= '</ul>';
    }

    $return.=  '</li>';
    return $return;

}

function GenerateHorizMenu(array $array, $sub=false){
    $data['class']  = ((isset($array['class']))?' '.$array['class']:'');
    $data['icon']   = ((isset($array['icon']))?' '.$array['icon']:'');
    $data['url']    = ((isset($array['url']))?$array['url']:'#');


    if(isset($array['onclick'])){
        $data['onclick'] = ' onclick="'.$array['onclick'].'" ';
    }else{
        $data['onclick'] = '';
    }


    if(!$sub){
        $data['class'] = 'nav-item '.$data['class'];
        $data['data-menu'] = 'dropdown';
        $data['aclass'] = 'dropdown-toggle nav-link';
    } else {
        if(isset($array['sub']) && count($array['sub'])) {
            $data['class'] = 'dropdown-submenu ' . $data['class'];
            $data['data-menu'] = 'dropdown-submenu';
            $data['aclass'] = 'dropdown-item dropdown-toggle';
        } else {
            $data['class'] = '';
            $data['data-menu'] = '';
            $data['aclass'] = 'dropdown-item';
        }
    }

    if(isset($array['permission'])){
        if(!merchantcan($array['permission']))
            return false;
    }

    if(isset($array['url']) && MenuRoute($array['permission']))
        $data['class'] .= ' active';

    $menu = "<li data-menu='{$data['data-menu']}' class='dropdown {$data['class']}'>
            <a ".$data['onclick']." href='{$data['url']}' data-toggle='dropdown' class='{$data['aclass']}' ".((!$sub)?'aria-expanded="false"':null).">
                <i class='{$data['icon']}'></i><span>{$array['text']}</span>
            </a>";
    if(isset($array['sub']) && count($array['sub'])){
        $menu .= "<ul class='dropdown-menu'>";
        foreach($array['sub'] as $key=>$item){
            $menu .= GenerateHorizMenu($item,true);
        }
        $menu .= "</ul>";
    }
    $menu .= "</li>";
    return $menu;
}

function MenuRoute($routename){
    $requestRoute = request()->route()->getName();
    if(is_array($routename)){
        if(in_array($requestRoute,$routename)){
            return true;
        }
        return false;
    }

    return ($requestRoute == $routename) ? true : false;
}

function staffCan($routename,$staffId = null){
    if($staffId && $staffId == request()->user()->id){
        $staffId = null;
    }

    $userObj = $staffId ? \App\Models\Staff::where('id',$staffId)->first() : request()->user();

    static $permissions;
    if(is_null($permissions)){
        $permissions = \App\Models\Staff::StaffPerms($userObj->id)->toArray();
    }

    if(is_array($routename)) {
        $arr = array_diff($routename,$permissions);
        return (!$arr) ? true : ((count($arr) == count($routename))? false:true);
    } else {
        return (in_array($routename,$permissions)) ? true : false;
    }
}