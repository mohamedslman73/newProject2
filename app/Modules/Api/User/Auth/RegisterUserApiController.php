<?php

namespace App\Modules\Api\User\Auth;

use App\Mail\sendVerificationCode;
use App\Modules\Api\User\UserApiController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class RegisterUserApiController extends UserApiController
{
    public function __construct()
    {
        parent::__construct();
        request()->route()->middleware('guest');
    }

    public function CheckRegister(Request $request){
        $RequestData = $request->only(['national_id','email','mobile']);

        $rules = [];
        if(isset($RequestData['national_id'])){
            $rules['national_id'] = 'required|digits:14|unique:users,national_id';
        }

        if(isset($RequestData['email'])){
            $rules['email'] = 'required|email|unique:users,email';
        }

        if(isset($RequestData['mobile'])){
            $rules['mobile'] = 'required|digits:11|unique:users,mobile';
        }
        $validator = Validator::make($RequestData, $rules);

        if($validator->errors()->any()){
            if($validator->errors()->has('mobile'))
                return $this->ValidationError($validator,__('The mobile has already been used'));
            elseif($validator->errors()->has('email'))
                return $this->ValidationError($validator,__('The email has already been used'));
            elseif($validator->errors()->has('national_id'))
                return $this->ValidationError($validator,__('The national_id has already been used'));
            else
                return $this->ValidationError($validator,__('Validation Error'));
        }

        return $this->respondWithoutError(true,__('User can continue'));
    }

    public function register(Request $request)
    {
        $RequestData = $request->only(['firstname','middlename','lastname','nationality_id','national_id','national_id_image','email','mobile','gender','address','password']);
        $validator = Validator::make($RequestData, [
            'firstname'             => 'required|string',
            'middlename'            => 'required|string',
            'lastname'              => 'required|string',
            'national_id'           => 'required|string|unique:users|digits:14',
            'national_id_image'     => 'required',
            'email'                 => 'required|string|email|max:255|unique:users',
            'mobile'                => 'required|string|unique:users|digits:11',
            'gender'                => 'required|in:male,female',
            'nationality_id'        => 'required|exists:countries,id',
            'address'               => 'required|string|max:255',
            'password'              => 'required',
        ]);

        try {
            $date = str_split(substr($RequestData['national_id'],1,6),2);
            if($date[0] > date('y'))
                $date[0] = '19'.$date[0];
            else
                $date[0] = '20'.$date[0];
            $RequestData['birthdate'] = Carbon::createFromFormat('Y-m-d', implode('-',$date));
        } catch (\Exception $e){}

        if($validator->errors()->any() || (!$RequestData['birthdate'])){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        if(isset($RequestData['national_id_image'])){
            try {
                $img = Image::make(base64_decode($RequestData['national_id_image']));
                $imageName = 'users/national_id/' . $RequestData['national_id'] . '_' . uniqid() . '.jpg';
                //$img->resize($dim['width'], $dim['height'])
                $img->save(storage_path('app/public/') . $imageName);
                $RequestData['national_id_image'] = $imageName;
            } catch (\Exception $e){
                return $this->ValidationError($validator,__('National id copy image not accepted'));
            }
        }

        $RequestData['password'] = bcrypt($RequestData['password']);
        unset($RequestData['password_confirmation']);
        $user = User::create($RequestData);

        if($user) {
            $verification = $user->verification()->create(['user_id'=>$user->id,'code'=>rand(100,999).'-'.rand(100,900)]);
            Mail::to($user->email)->send(new sendVerificationCode(['code'=>$verification->code]));
            unset($user['verification']);
            if($this->actualLogin($RequestData['mobile'],$request->password)){
                $token = $this->GenerateToken($request->password);
                if($token['status']){
                    return $this->respondWithoutError($token['data'],'User successfuly created');
                } else {
                    return $this->setCode(302)->respondWithError(false,$token['msg']);
                }
            }
            return $this->respondWithError(false,__('Could not login user'));
        } else
            return $this->respondWithError(false,__('Could not create user'));
    }
}
