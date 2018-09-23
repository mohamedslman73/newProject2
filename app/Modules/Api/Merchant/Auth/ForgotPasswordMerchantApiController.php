<?php

namespace App\Modules\Api\Merchant\Auth;

use App\Models\MerchantStaff;
use App\Modules\Api\Merchant\MerchantApiController;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordMerchantApiController extends MerchantApiController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function broker()
    {
    	return Password::broker('merchant');
    }

    
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->only(['username']), ['username' => 'required|exists:merchant_staff,id']);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

    	$user = MerchantStaff::where('id',$request->username)->first();
    	if($user) {
            $response = $this->broker()->sendResetLink(
                ['email'=>$user->email]
            );
        }

    	if( ($response==Password::RESET_LINK_SENT) && $user)
            return $this->respondSuccess(false,__('Check your email for reset token'));
        else
            return $this->respondWithError(false,__('User not found'));

    }
    
}
