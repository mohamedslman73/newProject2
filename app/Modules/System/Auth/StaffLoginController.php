<?php

namespace App\Modules\System\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\System\SystemController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class StaffLoginController extends SystemController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    function showLoginForm(){
        return parent::view('auth.login');
    }
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/system/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    protected function guard()
    {
        return Auth::guard('staff');
    }

    protected function attemptLogin(Request $request)
    {
        if ( $this->guard()->attempt($this->credentials($request)+['status'=>'active'], $request->has('remember'))){
            return redirect()->route('system.dashboard');
        }
    }

    public function __construct(){
       $this->middleware('guest:staff')->except('logout');
    }

    protected function logout(Request $request){
        //$this->guard()->logout();
       // $request->session()->invalidate();
        Auth::guard('staff')->logout();
       return redirect('/system/login');
    }
}
