<?php
namespace App\Modules\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller {
    public $systemLang;
    public $JsonData;
    public $StatusCode = 200;
    public $Code = 300;
    public $lastupdate;
    public $Date = '2017-11-12 12:11:11';

    public function __construct(){
        $this->middleware('auth:api')->except(['login','callback','sendResetLinkEmail','register','getDatabase','verifyReset',
            'CheckRegister','aboutUs','checkversion','checkUser']);
        $this->content = [];
        $this->systemLang = App::getLocale();
        $this->lastupdate = (object)[
            'Database'              => $this->Date,
            'Application'           => '1.5',
        ];
        if(in_array(request()->lang,['ar','en']))
            $this->systemLang = request()->lang;
        else
            $this->systemLang = 'en';
        $this->JsonData = request()->all();
    }

    public function actualLogin($username,$password){
        if(
            Auth('web')->attempt(['mobile' => $username, 'password' => $password,'status'=>'active'])
            || Auth('web')->attempt(['email' => $username, 'password' => $password,'status'=>'active'])
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function checkUser(Request $request){
        $RequestData = $request->only(['username']);
        $validator = Validator::make($RequestData, [
            'username'          =>  'required',
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $user = User::where('users.email','=',$RequestData['username'])
            ->orWhere('users.mobile', '=', $RequestData['username'])
            ->Active()
            ->first();

        if($user){
            return $this->respondWithoutError(true,__('User found'));
        } else {
            return $this->respondNotFound(false,__('User not found'));
        }
    }

    public function login(Request $request){
        $RequestData = $request->only(['username','password','rememberme']);
        $validator = Validator::make($RequestData, [
            'username'          =>  'required',
            'password'          =>  'required',
            'rememberme'        =>  'required|in:0,1'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        //TODO Token lifetime

        if($this->actualLogin($RequestData['username'],$RequestData['password'])){
            $token = $this->GenerateToken($RequestData['password']);
            if($token['status']){
                return $this->respondWithoutError($token['data'],'Successfuly logged in');
            } else {
                return $this->setCode(302)->respondWithError(false,$token['msg']);
            }
        } else {
            return $this->setCode(302)->respondWithError(false,__('Wrong username OR password'));
        }
    }

    public function checkuserStatus($user=null){
        $userobj = (($user) ? $user : (Auth::user()) ? Auth::user() : null);
        if(isset($userobj) && ($userobj->status == 'in-active'))
           return $this->respondWithError(false,__('Deactivated Account'));
    }

    public function GenerateToken($password){
        $client = new \GuzzleHttp\Client;
        try {
            $response = $client->post(getenv('APP_URL') . '/oauth/token', [
                'form_params' => [
                    'client_id' => getenv('auth.client.user.id'),
                    'client_secret' => getenv('auth.client.user.secret'),
                    'grant_type' => 'password',
                    'username' => Auth('web')->user()->email,
                    'password' => $password,
                    'scope' => '*',
                ]
            ]);
            return ['status'=>true,'data'=>json_decode( (string) $response->getBody() )];
        } catch (RequestException $e){
            return ['status'=>true,'msg'=>__('Couldn\'t generate token, try again later')];
        }
    }


    public function verify(Request $request){
        $RequestData = $request->only(['code']);
        $validator = Validator::make($RequestData, [
            'code'    =>  'required|min:7|max:7'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        if(($user = Auth::user()) && (isset($RequestData['code']))){
            if($user->verification->code == $RequestData['code']){
                if($user->verify($RequestData['code']))
                    return $this->respondSuccess(false,__('Account Successfuly verified'));
                else
                    return $this->respondWithError(false,__('Could not verify account'));
            } else
                return $this->respondWithError(false,__('Wrong verification code'));
        }
        return $this->respondNotFound(false,__('No verification code provided'));
    }


    //TODO Test Purposes
    public function data(){
        dd(Auth()->user());
    }

    function no_access(){
        return ['status'=>false,'msg'=> __('You don\'t have permission to preform this action')];
    }



    function headerdata($keys){
        if(is_array($keys)) {
            $response = [];
            foreach ($keys as $key) {
                $response[$key] = array_key_exists($key,$this->JsonData) ? $this->JsonData[$key] : null;
            }
            request()->merge($response);
            return $response;
        } elseif (isset($keys)){
            $response = array_key_exists($keys,$this->JsonData)  ? $this->JsonData[$keys] : null;
            request()->merge($response);
            return $response;
        } else {
            request()->merge($this->JsonData);
            return $this->JsonData;
        }
    }


    function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }



    public function setStatusCode($StatusCode){
        $this->StatusCode = $StatusCode;
        return $this;
    }

    public function getStatusCode(){
        return $this->StatusCode;
    }

    public function setCode($code){
        $this->Code = $code;
        return $this;
    }

    public function getCode(){
        return $this->Code;
    }

    function ReturnMethod($condition,$truemsg,$falsemsg,$data=false){
        if($condition)
            return ['status'=>true,'msg'=>$truemsg,'data'=>$data];
        else
            return ['status'=>false,'msg'=>$falsemsg,'data'=>$data];
    }

    public function respondSuccess($data,$message = 'Success'){
        return $this->setStatusCode(200)->setCode(300)->respondWithoutError($data,$message);
    }

    public function respondCreated($data,$message = 'Row has been created'){
        return $this->setStatusCode(200)->setCode(300)->respondWithoutError($data,$message);
    }

    public function respondNotFound($data,$message = 'Not Found!'){
        return $this->setStatusCode(200)->setCode(301)->respondWithError($data,$message);
    }

    public function respond($data,$headers=[]){
        $data['version'] = $this->lastupdate;
        return response()->json($data,$this->getStatusCode(),$headers);
    }

    public function respondWithoutError($data,$message){
        if(is_array($data)){
            $data['version'] = $this->lastupdate;
        } else if(is_object($data)) {
            $data->version = $this->lastupdate;
        } else {
            $data = array_merge([$data],[
                'version'=> $this->lastupdate,
            ]);
        }
        return response()->json([
            'status' => true,
            'msg' => $message,
            'code' => $this->getCode(),
            'data'=>$data
        ],$this->getStatusCode());
    }

    public function respondWithError($data,$message){
        if(is_array($data)){
            $data['version'] = $this->lastupdate;
        } else if(is_object($data)) {
            $data->version = $this->lastupdate;
        } else {
            $data = array_merge([$data],[
                'version'=> $this->lastupdate,
            ]);
        }
        return response()->json([
            'status' => false,
            'msg' => $message,
            'code' => $this->getCode(),
            'data'=>$data
        ],$this->getStatusCode());
    }

    public function ValidationError($validation,$message){
        $errorArray = $validation->errors()->messages();

        $data['errors'] = array_column(array_map(function($key,$val) {
            return ['key'=>$key,'val'=>implode('|',$val)];
        },array_keys($errorArray),$errorArray),'val','key');

        $data['version'] = $this->lastupdate;

        return $this->setCode(303)->respondWithError($data,$message);
    }


}