<?php
namespace App\Modules\Api\User;

use App\Modules\Api\Transformers\User\OrderitemTransformer;
use App\Modules\Api\Transformers\User\TransactionTransformer;
use App\Modules\Api\Transformers\User\UserInfoTransformer;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class UserInfoApiController extends UserApiController
{
    protected $Transformer;
    public function __construct(UserInfoTransformer $userInfoTransformer)
    {
        parent::__construct();
        $this->Transformer = $userInfoTransformer;
    }

    public function info(){
        $userObj = Auth::user();

        $userObj->eCommerceWallet;

        if(!$userObj)
            return $this->respondNotFound(false,__('Could not retrieve user information'));

        return $this->respondSuccess($this->Transformer->transform($userObj->toArray(),$this->systemLang),__('User Information'));
    }


    public function updateInfo(){
        $userObj = Auth::user();

        $RequestData = array_filter($this->headerdata(['firstname','middlename','lastname','birthdate','image','address']));
        $validator = Validator::make($RequestData, [
            'firstname'             => 'nullable|string',
            'middlename'            => 'nullable|string',
            'lastname'              => 'nullable|string',
            'birthdate'             => 'nullable|date',
            'image'                 => 'nullable|string',
            'address'               => 'nullable|string|max:255',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        //TODO allow national_id update for specific user
        /*
         if(isset($RequestData['national_id_image'])){
            try {
                $img = Image::make(base64_decode($RequestData['national_id_image']));
                $imageName = 'users/national_id/' . $userObj->mobile . '_' . uniqid() . '.jpg';
                $dim = calcDim($img->width(),$img->height(),300,300);
                $img->resize($dim['width'], $dim['height'])->save(storage_path('app/public/') . $imageName);
                $RequestData['national_id_image'] = $imageName;
            } catch (\Exception $e){
                return $this->ValidationError($validator,__('National id copy image not accepted'));
            }
        }
         *
         */

        if(isset($RequestData['image'])){
            try {
                $img = Image::make(base64_decode($RequestData['image']));
                $imageName = 'users/profile/' . $userObj->mobile . '_' . uniqid() . '.jpg';
                $dim = calcDim($img->width(),$img->height(),300,300);
                $img->resize($dim['width'], $dim['height'])->save(storage_path('app/public/') . $imageName);
                $RequestData['image'] = $imageName;
            } catch (\Exception $e){
                return $this->ValidationError($validator,__('Image not accepted'));
            }
        }


        if(!$userObj->update($RequestData))
            return $this->respondWithError(false,__('Could not update user information'));
        $userObj->eCommerceWallet;
        return $this->respondSuccess($this->Transformer->transform($userObj->toArray(),$this->systemLang),__('User Information'));
    }


    public function changePassword(){
        $userObj = Auth::user();

        $RequestData = array_filter($this->headerdata(['password','password_confirmation','current_password']));
        $validator = Validator::make($RequestData, [
            'current_password'  => 'required|PasswordCheck:'.$userObj->password,
            'password'          => 'required|confirmed|min:6',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        if(!$userObj->update(['password'=>Hash::make($RequestData['password'])]))
            return $this->respondNotFound(false,__('Could not change password'));
        return $this->respondSuccess(true,__('Password successfuly changed'));
    }



}