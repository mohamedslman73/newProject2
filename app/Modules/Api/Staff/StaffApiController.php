<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Complaint;
use App\Models\Item;
use App\Models\ItemCategories;
use App\Models\Project;
use App\Models\Staff;

use App\Models\Supplier;
use App\Models\SupplierCategories;
use App\Models\SupplierOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Lcobucci\JWT\Parser;
use UAParser\Exception\ReaderException;

class StaffApiController extends Controller {
    public $systemLang;
    public $JsonData;
    public $StatusCode = 200;
    public $Code = 100;
    public $lastupdate;
    public $Date = '2018-01-27 12:00:11';
    public $AppVersion = '1.0';






    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
//     $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function dashboard()
    {
            $data['itemCount'] = Item::count();
            $data['itemCategoryCount'] = ItemCategories::count();
            $data['clientCount'] = Client::count();
            $data['projectCount'] = Project::count();
            $data['projectThisMonth'] = Project::where(DB::raw('MONTH(created_at)'), '=', date('n'))->count();
            $data['supplierCategoryCount'] = SupplierCategories::count();
            $data['supplierCount'] = Supplier::whereStatus('active')->count();
           // $data['supplierCount'] = Supplier::count();
            $data['supplierOrderCount'] = SupplierOrders::count();
            $data['supplierOrderThisMonth'] = SupplierOrders::where(DB::raw('MONTH(created_at)'), '=', date('n'))->count();
            $data['staffCount'] = Staff::count();
            $data['complaint'] = Complaint::count();
            $data['complaintThisMonth'] = Complaint::where(DB::raw('MONTH(created_at)'), '=', date('n'))->count();
            $data['clients'] = Client::whereStatus('active')->count();
            return $this->json(true, __('Date of the Dashboard'), $data);

    }
    public function login(Request $request)
    {
      //  $this->dd();

        $RequestData = $request->only(['email', 'password']);
        $validator = Validator::make($RequestData, [
            'email'     => 'required|exists:staff,email',
            'password'  => 'required'
        ]);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        try {

            $client = new \GuzzleHttp\Client();
            $response = $client->post( 'http://localhost:8080/seattle/public/oauth/token', [
                'form_params' => [
                    'client_id' => 6,//8
                    'client_secret' => 'M7NuCKfrXEQKJyCyxo8KbP1IanD4OFMX6kHFTwJH',
                    'grant_type' => 'password',
                    'username' => $RequestData['email'],
                    'password' => $RequestData['password'],
                    'scope' => '*',
                ]
            ]);
            //dd($response);
            $auth = json_decode((string)$response->getBody());
            if ($auth->access_token) {
                $staff = Staff::select("staff.*")
                    ->where('email', $RequestData['email'])
                    ->first();
              //  dd($auth->access_token);
               // dd($staff->update(['api_token'=>$auth->access_token]));
                $auth->id = $staff->id;
                $auth->firstname = $staff->firstname;
                $auth->lastname = $staff->lastname;
                $auth->email = $staff->email;
                $auth->status = $staff->status;
                $auth->nationality = $staff->nationality;
                $auth->joining_date = $staff->joining_date;
                $auth->gender = $staff->gender;
                $auth->salary = $staff->salary;
                $auth->height = $staff->length;
                $auth->weight = $staff->weight;
                $auth->job_title = $staff->job_title;
                $auth->bank_account =$staff->bank_account;
                $auth->visa_status = $staff->visa_status;
                $auth->finger_print = $staff->finger_print;
                $auth->blood= $staff->blood;
                $auth->medical = $staff->merdical;
                if (!empty($staff->weekly_vacations)) {
                    $auth->weekly_vacations = explode(",", $staff->weekly_vacations);
                }
                $auth->age = $staff->age;
                $auth->visa_number = $staff->visa_number;
                $auth->passport_number = $staff->passport_number;
                $auth->date_of_visa_issue = $staff->date_of_visa_issue;
                $clothes = [];

                foreach ($staff->staff_clothes as $key=>$clothe){
                   $clothes[$key]['size'] = $clothe->size;
                  $clothes[$key]['name']   = $clothe->clothe->name;
                }
                $auth->staff_clothes = $clothes;
                $auth->permission_group_id = $staff->permission_group_id;
                return $this->json(true, __('login successful'),['auth'=>$auth]);
            } else {
                return $this->json(false,__('invalid Auth'));
            }
        } catch (ReaderException $e) {
            return $this->json(false,__('invalid credentials'));
        }

    }
    public function logout(Request $request)
    {
        $this->dd();
        $user = Auth::user();
        dd($user->id);
        $value = $request->bearerToken();


        $id = (new Parser())->parse($value)->getHeader('jti');
        $user->token()->where('id', '=', $id)->first()->revoke();

        return $this->json(true,__('Logged out'));
    }




    function no_access(){
        return ['status'=>false,'msg'=> __('You don\'t have permission to preform this action')];
    }


    function headerdata($keys){
        return request()->only($keys);

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

        return $this->setCode(103)->respondWithError($data,implode("\n",array_flatten($errorArray)));
    }
    public function json($status,$msg = '', $data = [], $code = 200)
    {
        echo json_encode( ['status' => $status,'msg' => $msg, 'code' => $code, 'data' => (object)$data]);


    }









}