<?php

namespace App\Modules\Api\Merchant\Auth;

use App\Http\Controllers\Controller;
use App\Models\MerchantStaff;
use App\Modules\Api\Merchant\MerchantApiController;
use App\Modules\Api\User\UserApiController;
use App\Services\CustomPasswordBrokerManager;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResetPasswordMerchantApiController extends MerchantApiController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/api/user';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:merchant_staff');
    }
    
    public function guard(){
    	return Auth::guard('merchant_staff');
    }
    
    public function broker()
    {
    	return Password::broker('merchant');
    }

    public function CheckCode(Request $request)
    {
        $validator = Validator::make($request->only(['username','smsCode']), [
            'username'          => 'required|numeric',
            'smsCode'           => 'required|min:6|max:6',
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }


        $user = MerchantStaff::select('email')->where('id',$request->username)->first();

        $BrokerManager = new CustomPasswordBrokerManager(Application::getInstance());
        $config = config('auth.passwords.merchant');

        $DataBaseToken = $BrokerManager->GetDatabaseTokenInstance($config);

        $CheckCode = $DataBaseToken->exists($user,$request->smsCode);

        $data = ['email'=>$user->email,'code'=>$request->smsCode];

        if ($CheckCode)
            return $this->respondSuccess($data,__('Verification code found'));
        else
            return $this->setCode(101)->respondWithError(false,__('Wrong reset code, Or code expired'));
    }

    public function reset(Request $request)
    {
        $request['token'] = $request->smsCode;
        $validator = Validator::make($request->only(['smsCode','token','email','password','password_confirmation','revokeAll']), [
            'smsCode'       => 'required',
            'email'         => 'required|email',
            'password'      => 'required|confirmed|min:6',
            'revokeAll'     => 'nullable|in:1,0',
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $GLOBALS['revoke'] = $request->revokeAll;

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
                if($GLOBALS['revoke']) {
                    foreach ($user->tokens()->get() as $onetoken) {
                        $onetoken->revoke();
                    }
                }
            }
        );

        if ($response == Password::PASSWORD_RESET)
            return $this->respondSuccess(false,__('Your Password has been successfuly reset, Use your new password to login'));
        else
            return $this->setCode(101)->respondWithError(false,__('User not found'));
    }
    
}
