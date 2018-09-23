<?php

namespace App\Modules\Website;

use App\Models\Merchant;
use App\Models\MerchantRequestRegister;
use App\Models\Staff;

use App\Modules\Website\WebsiteController;
use App\Models\ContactUs;
use Notification;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;

class HomeController extends WebsiteController {

    public function index(Request $request,$lang = 'ar'){
        if($lang == 'en'){
            \App::setLocale('en');
        }elseif($lang == 'ar' || empty($lang)){
            \App::setLocale('ar');
        }else{
            abort(404);
        }
        return view('website/home');
    }

    public function contactUs(Request $request){
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'subject' => 'required',
            'message' => 'required'
        ]);

        if(ContactUs::create($request->all())){
            return ['status'=> true];
        }
        return ['status'=> false];
    }

    public function RequestRegisterMerchant(Request $request){

        $validator = \Validator::make($request->toArray(), [
            'name'        => 'required|min:2',
            'shop_name'   => 'required|min:2',
            'mobile1'     => 'required|digits:11',
            'mobile2'     => 'required|digits:11',
            'national_id' => 'required|digits:14',
            'address'     => 'required|min:2',
            'front_id'    => 'required|image',
            'back_id'     => 'required|image',
            'utility_receipt'     => 'required|image'
        ]);

        if($validator->errors()->any()){
            return ['status'=>false,'code'=>'103','msg'=> implode("<br />",array_flatten($validator->errors()->all()))];
        }

       $data = [
           'name'=> $request->name,
           'shop_name'=> $request->shop_name,
           'mobile'=> $request->mobile1,
           'mobile2'=> $request->mobile2,
           'address'=> $request->address,
           'national_id'=> $request->national_id,
           'id_front'=> @base64_encode(file_get_contents($request->front_id->path())),
           'id_back' => @base64_encode(file_get_contents($request->back_id->path())),
           'utility_receipt' => @base64_encode(file_get_contents($request->utility_receipt->path())),
       ];
            if($request->code) {
                $reseller_merchant = Merchant::where('code', $request->code)->first();
                if($reseller_merchant)
                $data['reseller_id'] = $reseller_merchant->id;
            }
 
            if(MerchantRequestRegister::create($data))
        {
            if(!empty(setting('monitor_staff'))){
                $monitorStaff = Staff::whereIn('id',explode("\n",setting('monitor_staff')))
                    ->get();

                foreach ($monitorStaff as $key => $value){
                    $value->notify(
                        (new UserNotification([
                            'title'         => 'New Merchant: '.$request->name,
                            'description'   => $request->name.' | '.__('Request Approval'),
                            'url'           => route('merchant.request-register.index')
                        ]))
                            ->delay(5)
                    );
                }
            }
            return ['status'=>true,'code'=>'1','msg'=>__('Your request has been successfully sent')];
        }else{
            return ['status'=>false,'code'=>'0','errors'=>__('Unexpected error! please try again later')];
        }
    }




}