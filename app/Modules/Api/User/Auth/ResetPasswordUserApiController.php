<?php

namespace App\Modules\Api\User\Auth;

use App\Http\Controllers\Controller;
use App\Modules\Api\User\UserApiController;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResetPasswordUserApiController extends UserApiController
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
        $this->middleware('guest:web');
    }
    
    public function guard(){
    	return Auth::guard('web');
    }
    
    public function broker()
    {
    	return Password::broker('users');
    }

    public function reset(Request $request)
    {
        $theRequest = $request->only(['code','email','password','password_confirmation','revokeAll']);
        $validator = Validator::make($theRequest, [
            'code'         => 'required',
            'email'         => 'required|email',
            'password'      => 'required|confirmed|min:6',
            'revokeAll'     => 'required|in:1,0',
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }
        $request['token'] = $theRequest['code'];

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
            return $this->respondWithError(false,__('User not found'));
    }
    
}
