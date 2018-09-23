<?php

namespace App\Modules\Api\Partner;

use App\Http\Controllers\Controller;
use App\Models\MerchantStaff;
use App\Modules\Merchant\MerchantController;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Lcobucci\JWT\Parser;


class PartnerApiController extends Controller
{
    public $systemLang;
    public $JsonData;
    public $StatusCode = 200;
    public $Code = 100;
    public $lastupdate;
    public $Date = '2018-01-27 12:00:11';
    public $AppVersion = '1.0';

    public function __construct()
    {

      $this->middleware('auth:ApiPartner');
 
        $this->content = [];

        $this->JsonData = request()->all();

        if ((isset($this->JsonData['lang'])) && (in_array($this->JsonData['lang'], ['ar', 'en']))) {
            $this->systemLang = $this->JsonData['lang'];
        } else {
            $this->systemLang = App::getLocale();
        }

    }

    function no_access()
    {
        return ['status' => false, 'msg' => __('You don\'t have permission to preform this action')];
    }


    function headerdata($keys)
    {
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


    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


    public function setStatusCode($StatusCode)
    {
        $this->StatusCode = $StatusCode;
        return $this;
    }


    public function getStatusCode()
    {
        return $this->StatusCode;
    }

    public function setCode($code)
    {
        $this->Code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->Code;
    }

    function ReturnMethod($condition, $truemsg, $falsemsg, $data = false)
    {
        if ($condition)
            return ['status' => true, 'msg' => $truemsg, 'data' => $data];
        else
            return ['status' => false, 'msg' => $falsemsg, 'data' => $data];
    }

    public function respondSuccess($data, $message = 'Success')
    {
        return $this->setStatusCode(200)->setCode(100)->respondWithoutError($data, $message);
    }

    public function respondCreated($data, $message = 'Row has been created')
    {
        return $this->setStatusCode(200)->setCode(100)->respondWithoutError($data, $message);
    }

    public function respondNotFound($data, $message = 'Not Found!')
    {
        return $this->setStatusCode(200)->setCode(101)->respondWithError($data, $message);
    }

    public function respond($data, $headers = [])
    {
        $data['version'] = $this->lastupdate;
        return response()->json($data, $this->getStatusCode(), $headers);
    }

    public function respondWithoutError($data, $message)
    {
//        if (is_array($data)) {
//            $data['version'] = $this->lastupdate;
//        } else if (is_object($data)) {
//            $data->version = $this->lastupdate;
//        } else {
//            $data = array_merge([$data], [
//                'version' => $this->lastupdate,
//            ]);
//        }
        return response()->json([
            'status' => true,
            'msg' => $message,
            'code' => $this->getCode(),
            'data' => $data
        ], $this->getStatusCode());
    }

    public function respondWithError($data, $message)
    {
        if (is_array($data)) {
            $data['version'] = $this->lastupdate;
        } else if (is_object($data)) {
            $data->version = $this->lastupdate;
        } else {
            $data = array_merge([$data], [
                'version' => $this->lastupdate,
            ]);
        }
        return response()->json([
            'status' => false,
            'msg' => $message,
            'code' => $this->getCode(),
            'data' => $data
        ], $this->getStatusCode());
    }

    public function permissions($permission = false)
    {
        $permissions = \Illuminate\Support\Facades\File::getRequire('../app/Modules/Merchant/Permissions.php');
        return $permission ? isset($permissions[$permission]) ? $permissions[$permission] : false : $permissions;
    }

    public function permissionsNames($permission = false, $reverse = false)
    {
        $permissions = $this->permissions();
        $data = [];
        foreach ($permissions as $key => $val) {
            $data = array_merge($data, [$key => __(ucfirst(str_replace('-', ' ', $key)))]);
        }
        if ($reverse)
            return array_search($permission, $data);
        else
            return $data ? isset($data[$permission]) ? $data[$permission] : false : $data;
    }

    public function ValidationError($validation, $message)
    {
        $errorArray = $validation->errors()->messages();

        $data = array_column(array_map(function ($key, $val) {
            return ['key' => $key, 'val' => implode('|', $val)];
        }, array_keys($errorArray), $errorArray), 'val', 'key');

        $data['version'] = $this->lastupdate;
        //$data['msgs'] = implode("\n",array_flatten($errorArray));

        return $this->setCode(103)->respondWithError($data, implode("\n", array_flatten($errorArray)));
    }

    public function DownloadApk(Request $request)
    {
        //$path = storage_path('app/public/latest-app.apk');

        //return response()->file($path);
        return response()->download(storage_path('app/public/latest-app.apk'));
    }

}