<?php
namespace App\Modules\Api\User;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Auth;
class FacebookApiController extends UserApiController
{

    public function callback(){
        //$token = request('token');
        //$token = 'EAAB93EtWVmsBAHZByUr80Tfhp8XdGA4EKdWoNZA6PmXRJVZCWpLPU2QTCVtLX49ZBmNaNZBBa83o2aIph1lyQZAJDmzCuZCCf2ICDOIiMKQazEkLZAyMHJY4P5UtBwKOQpaMadqnIQr88bFsaj3LZCWpTPZA5RKeyfVPmFMZA5JYq7EYeVc5dr94oOPD4YVEzYePwym4JslV7T72dPQWhDn71zm';


        $user = Socialite::driver('facebook')->stateless()->user();

            $token = $user->token;
            $tokenSecret = $user->tokenSecret;

            //$user->getAvatar();

        if($user->getId()){
            $userobj = User::where('facebook_user_id',$user->getId())->first();
            if($userobj){
                Auth::guard('web')->login($userobj);
                $this->content['token'] = $userobj->createToken('EgPaY')->accessToken;
                $this->content['status'] = true;
                $this->content['code'] = 300;
                $status = 200;
            } else {
                $this->content['msg'] = __('Unauthorised');
                $this->content['data'] = [
                    'userId'    => $user->getId(),
                    'userName'  => $user->getName(),
                    'userEmail' => $user->getEmail()
                ];
                $status = 200;
                $this->content['status'] =  false;
                $this->content['code'] = 302;
            }
        } else {
            $this->content['msg'] = __('Unauthorised');
            $status = 401;
            $this->content['status'] =  false;
            $this->content['code'] = 302;
        }

        return response()->json($this->content, $status);

    }

}