<?php
namespace App\Modules\Api\Merchant;

use App\Models\MobileDevice;
use App\Modules\Api\Transformers\StaffInfoTransformer;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class StaffInfoApiController extends MerchantApiController
{
    protected $Transformer;
    public function __construct(StaffInfoTransformer $staffInfoTransformer)
    {
        parent::__construct();
        $this->Transformer = $staffInfoTransformer;
    }

    public function info(Request $request){
        $RequestedData = $request->only(['device_token','device_version','device_model','device_serial']);
        $userObj = Auth::user();
        if(!empty($RequestedData['device_token'])){
            $DeviceRow = MobileDevice::where('device_token',$RequestedData['device_token'])
                ->where('user_id',$userObj->id)
                ->where('user_type',get_class($userObj))
            ->first();
            if(!$DeviceRow){
                MobileDevice::create([
                    'user_id'           =>      $userObj->id,
                    'user_type'         =>      get_class($userObj),
                    'device_token'      =>      $RequestedData['device_token'],
                    'device_version'    =>      $RequestedData['device_version'],
                    'device_model'      =>      $RequestedData['device_model'],
                    'device_serial'     =>      $RequestedData['device_serial'],
                ]);
            } else {
                $DeviceRow->update([
                    'updated_at'    =>      Carbon::now(),
                ]);
            }
        }
        $userObj->paymentWallet;
        $userObj->dddd = 'dddd';
        if(!$userObj)
            return $this->respondNotFound(false,__('Could not retrieve user information'));

        return $this->respondSuccess($this->Transformer->transform($userObj->toArray(),[$this->systemLang]),__('User Information'));
    }


    public function updateInfo(){
        $userObj = Auth::user();
        $userObj->paymentWallet;
        $RequestData = array_filter($this->headerdata(['firstname','lastname','mobile']));
        $validator = Validator::make($RequestData, [
            'firstname'             => 'nullable|string',
            'lastname'              => 'nullable|string',
            //'mobile'                => 'nullable|digits:11',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        try {
            $userObj->update($RequestData);
            return $this->respondSuccess($this->Transformer->transform($userObj->toArray(),$this->systemLang),__('User Information'));
        } catch (QueryException $e){
            if($e->getCode() == '23000')
                return $this->setCode(106)->respondWithError(false,__('Duplicated information'));
            return $this->respondWithError(false,__('Could not retrieve user information'));
        }

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

        if($RequestData['current_password'] == $RequestData['password']){
           return $this->respondWithError(false,__('Can not change password to current password'));
        }

        try {
            $userObj->update([
                'password'              => Hash::make($RequestData['password']),
                'must_change_password'  => 0,
            ]);

            foreach ($userObj->tokens as $key => $value){
                $value->revoke();
            }

            return $this->respondSuccess(false,__('Successfully changed password'));
        } catch (QueryException $e){
            return $this->respondNotFound(false,__('Could not change user password'));
        }
    }



}