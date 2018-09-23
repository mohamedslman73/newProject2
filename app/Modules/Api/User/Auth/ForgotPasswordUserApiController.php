<?php

namespace App\Modules\Api\User\Auth;

use App\Models\User;
use App\Modules\Api\User\UserApiController;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordUserApiController extends UserApiController
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
    	return Password::broker('users');
    }

    
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->only(['username']), ['username' => 'required']);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

    	$user = User::where('email',$request->username)->first();
    	if(!$user)
            $user = User::where('mobile',$request->username)->first();

    	if($user) {
            //TODO test purpose only NEVER TURN IT ON
    	    //$token = $this->broker()->createToken($user);
            $response = $this->broker()->sendResetLink(
                ['email'=>$user->email]
            );
        }

    	if( ($response==Password::RESET_LINK_SENT) && $user)
            return $this->respondSuccess(false,__('Password reset Token successfuly created'));
        else
            return $this->respondWithError(false,__('User not found'));

    }

    public function verifyReset(Request $request){
        $theRequest = $request->only(['username','code','nat_last_4digits']);
        $validator = Validator::make($theRequest, [
            'username'              => 'required',
            'code'                  => 'required',
            'nat_last_4digits'      => 'required|digits:4'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $user = User::where('users.email','=',$request['username'])
                ->orWhere('users.mobile', '=', $theRequest['username'])->first();

        $pwdReset = $user->PwdReset;
        unset($user['PwdReset']);

        if(strpos($user->national_id,$theRequest['nat_last_4digits']) !== 10){
            return $this->respondWithError(false,__('Wrong provided information'));
        }

        if(Carbon::now() > Carbon::createFromFormat('Y-m-d H:i:s',$pwdReset->created_at)->addMinutes(config('auth.passwords.users.expire'))){
            return $this->respondWithError(false,__('Reset token expired'));
        }

        if(!Hash::check($theRequest['code'],$pwdReset->token)){
            return $this->respondWithError(false,__('wrong reset code'));
        }

        if($user){
            return $this->respondSuccess(['email'=>$user->email],'User found');
        } else{
            return $this->respondWithError(false,__('User not found'));
        }

    }
    
}
