<?php
namespace App\Modules\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use App\Models\Contest;
use App\Models\ContestWinners;
use App\Models\MerchantRequestRegister;
use App\Models\MerchantStaff;
use App\Modules\Merchant\MerchantController;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Lcobucci\JWT\Parser;
use DB;

class MerchantApiController extends Controller {
    public $systemLang;
    public $JsonData;
    public $StatusCode = 200;
    public $Code = 100;
    public $lastupdate;
    public $Date = '2018-01-27 12:00:11';
    public $AppVersion = '1.0';
    public function __construct(){

        $this->Date = setting('merchant_mobile_app_database_lastupdate');
        $this->AppVersion = setting('merchant_mobile_application_version');


        $this->middleware('auth:apiMerchant')->except(['login','callback','sendResetLinkEmail','register','getDatabase',
            'aboutUs','checkversion','DownloadApk','RequestRegisterMerchant']);


        $this->lastupdate = (object)[
            'Database'              => $this->Date,
            'Application'           => $this->AppVersion,
        ];
        $this->content = [];

        $this->JsonData = request()->all();

        if((isset($this->JsonData['lang'])) && (in_array($this->JsonData['lang'],['ar','en']))){
            $this->systemLang = $this->JsonData['lang'];
        }else{
            $this->systemLang = App::getLocale();
        }

        //$headerdata = file_get_contents("php://input");
        //$headerdata = request();
        /*
        if($this->isJson($headerdata) || ($headerdata == '') || ($headerdata=='""')) {
            if(strlen($headerdata) != '""')
                $this->JsonData = json_decode($headerdata,true);
            else
                $this->JsonData = array();
        } else
            return abort(404);
        */
    }

    public function login(Request $request){

        $RequestData = $request->only(['username','password'/*,'rememberme'*/,'merchant_id']);
        $validator = Validator::make($RequestData, [
            'username'          =>  'required|exists:merchant_staff,id',
            'password'          =>  'required',
            //'merchant_id'       =>  'required|exists:merchants,id',
//            'rememberme'        =>  'required|in:0,1'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        //TODO Token lifetime

        if(Auth('merchant_staff')->validate(['id' => $RequestData['username'], 'password' => $RequestData['password']])){
            $User = MerchantStaff::where('id',$RequestData['username'])->first();
            if($User->status !== 'active')
                return $this->setCode(102)->respondWithError(false,__('User account not activated'));
//            if($User->merchant()->id != $RequestData['merchant_id'])
//                return $this->setCode(102)->respondWithError(false,__('Wrong merchant id'));

            if($User->merchant()->status !== 'active')
                return $this->setCode(102)->respondWithError(false,__('Merchant account not activated'));

            /*
            if($User->merchant_staff_group->status !== 'active')
                return $this->setCode(102)->respondWithError(false,__('Employee group not activate'));
            */
            $client = new \GuzzleHttp\Client;
            try {
                $response = $client->post('http://localhost/git_2/public' . '/oauth/token', [
                    'form_params' => [
                        'client_id' => getenv('auth.client.merchant.id'),
                        // The secret generated when you ran: php artisan passport:install
                        'client_secret' => getenv('auth.client.merchant.secret'),
                        'grant_type' => 'password',
                        'username' => $RequestData['username'],
                        'password' => $RequestData['password'],
                        'scope' => '*',
                    ]
                ]);
                $auth = json_decode( (string) $response->getBody() );
                /*
                 * Must Change password
                 */
                $auth->request_info = $User->merchant()->request_device_info;
                if($User->must_change_password == 1){
                    $auth->must_change_password = true;
                }else{
                    $auth->must_change_password = false;
                }

                // get ballance
               $auth->balance = $User->paymentWallet->balance.' ' . __('LE');

                // ---- Contest Module
                $auth->contest_text          = '';
                $auth->contest_winner_header = __('Congratulations');
                $auth->contest_winner_body   = '';
                $auth->is_contest_winner     = false;
                $auth->is_contest            = false;

                $contest = Contest::where('status', 'active')
                    ->where('type', 'e-payment')
                    ->where('beneficiary', 'merchants')
                    ->whereRaw("('" . Carbon::now()->format('Y-m-d') . "' BETWEEN `start_date` AND `end_date`)")
                    ->first();

                if($contest){
                    $auth->is_contest = true;
                    $winner = ContestWinners::where('beneficiary_id',$User->merchant()->id)
                        ->where('date',Carbon::now()->format('Y-m-d'))
                        ->first();

                    if($winner){
                        $auth->is_contest_winner = true;
                        $auth->contest_winner_body = __('You are win :amount',['amount'=>$winner->prize]);
                    }

                    $total  = DB::select("
                    SELECT 
                      SUM(payment_invoice.total_amount) as `total`
                    FROM
                      `payment_invoice`
                    INNER JOIN payment_transactions ON payment_transactions.id = payment_invoice.payment_transaction_id
                    WHERE
                        payment_invoice.status = 'paid'
                        AND payment_invoice.creatable_type = 'App\\\\Models\\\\Merchant'
                        AND payment_invoice.creatable_id = :id
                        AND -- NEED update
                        DATE(payment_invoice.created_at) = :created_at
                        AND payment_transactions.payment_services_id IN(".implode(',',$contest->service_ids).")
                    ",['created_at'=>Carbon::now()->format('Y-m-d'),'id'=> $User->merchant()->id]);

                    $target = $contest->target;
                    if ($total[0]->total != null) {
                        $new_total  = $total[0]->total;
                        $total = $target-$new_total;

                        if($new_total < $target){
                            $auth->contest_text = "متبقي لك ".amount($total,true)." لدخول السحب اليومي";
                        }else{
                            $auth->contest_text = __('مبروك! .. انت الان داخل السحب اليومي');
                        }

                    }else{
                        $new_total  = 0;
                        $total = $target-$new_total;

                        $auth->contest_text = "متبقي لك ".amount($total,true)." لدخول السحب اليومي";
                    }

                }

                // ---- Contest Module


















                return $this->respondWithoutError($auth,'Successfully logged in');
            } catch (RequestException $e){
                return $this->setCode(102)->respondWithError(false,__('Couldn\'t generate token, try again later'));
            }
        } else {
            return $this->setCode(102)->respondWithError(false,__('Wrong username OR password'));
        };
    }

    public function logout(Request $request){
        $value = $request->bearerToken();
        $user = Auth::user();
        $id= (new Parser())->parse($value)->getHeader('jti');
        $user->tokens()->where('id','=',$id)->first()->revoke();
        $json = [
            'status' => true,
            'code' => 100,
            'msg' => __('Logged out'),
        ];
        return response()->json($json, '200');
    }

    public function checkuserStatus($user=null){
        $userobj = (($user) ? $user : (Auth::user()) ? Auth::user() : null);
        if(isset($userobj) && ($userobj->status == 'in-active'))
            return $this->respondWithError(false,__('Deactivated Account'));
        //TODO add Check for Merchant if its active
        if(isset($userobj) && ($userobj->merchant()->status == 'in-active'))
            return $this->respondWithError(false,__('Deactivated Merchant'));
    }

    function no_access(){
        return ['status'=>false,'msg'=> __('You don\'t have permission to preform this action')];
    }


    function headerdata($keys){
        return request()->only($keys);
        /*
        if(count($this->JsonData) == 0)
            return [];
        if(is_array($keys)) {
            $response = [];
            foreach ($keys as $key) {
                $response[$key] = array_key_exists($key,$this->JsonData) ? $this->JsonData[$key] : null;
            }
            request()->merge($response);
            return $response;
        } elseif (isset($keys)){
            $response = array_key_exists($keys,$this->JsonData)  ? [$keys=>$this->JsonData[$keys]] : null;
                request()->merge($response);
            return $response;
        } else {
            request()->merge($this->JsonData);
            return $this->JsonData;
        }
        */
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
        return $this->setStatusCode(200)->setCode(100)->respondWithoutError($data,$message);
    }

    public function respondCreated($data,$message = 'Row has been created'){
        return $this->setStatusCode(200)->setCode(100)->respondWithoutError($data,$message);
    }

    public function respondNotFound($data,$message = 'Not Found!'){
        return $this->setStatusCode(200)->setCode(101)->respondWithError($data,$message);
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

    public function permissions($permission=false){
        $permissions = \Illuminate\Support\Facades\File::getRequire('../app/Modules/Merchant/Permissions.php');
        return $permission ? isset($permissions[$permission]) ? $permissions[$permission] : false : $permissions;
    }

    public function permissionsNames($permission=false,$reverse=false){
        $permissions = $this->permissions();
        $data = [];
        foreach($permissions as $key=>$val){
            $data = array_merge($data,[$key=>__(ucfirst(str_replace('-',' ',$key)))]);
        }
        if($reverse)
            return array_search($permission,$data);
        else
            return $data ? isset($data[$permission]) ? $data[$permission] : false : $data;
    }

    public function ValidationError($validation,$message){
        $errorArray = $validation->errors()->messages();

        $data = array_column(array_map(function($key,$val) {
            return ['key'=>$key,'val'=>implode('|',$val)];
        },array_keys($errorArray),$errorArray),'val','key');

        $data['version'] = $this->lastupdate;
        //$data['msgs'] = implode("\n",array_flatten($errorArray));

        return $this->setCode(103)->respondWithError($data,implode("\n",array_flatten($errorArray)));
    }

    public function DownloadApk(Request $request)
    {
        //$path = storage_path('app/public/latest-app.apk');

        //return response()->file($path);
        return response()->download(storage_path('app/public/latest-app.apk'));
    }




    public function RequestRegisterMerchant(Request $request){

        $RequestData = $request->all();

        $validator = Validator::make($RequestData, [
            'name'      =>  'required',
            'mobile'    =>  'required',
            'address'   =>  'required',
            'id_front'  =>  'required',
            'id_back'   =>  'required',
            'utility_receipt' => 'required'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        if(
        MerchantRequestRegister::create([
            'name'=> $RequestData['name'],
            'mobile'=> $RequestData['mobile'],
            'address'=> $RequestData['address'],
            'id_front'=> $RequestData['id_front'],
            'id_back'=> $RequestData['id_back'],
            'utility_receipt'=> $RequestData['utility_receipt']
        ])
        ){
            return $this->setCode(102)->respondSuccess(false,__('Your order has been successfully sent'));
        }else{
            return $this->setCode(101)->respondSuccess(false,__('Unexpected error! please try again later'));
        }

    }






    public function requestEquipment(Request $request){

        $validator = Validator::make($request->all(), [
            'type'      =>  'required|in:mobiwire,printer,paper'
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        if(
            ContactUs::create(
                [
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'mobile' => Auth::user()->mobile,
                    'subject' => 'Request '.ucfirst($request->type),
                    'message' => '#ID: '.Auth::id().' --  Request '.ucfirst($request->type)
                ]
            )
        ){
            return $this->respondSuccess([],__('Your Request Has been send successfully'));
        }else{
            return $this->respondWithError([],__('Unknown Error, Please try again later'));
        }



    }





}