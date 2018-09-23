<?php

namespace App\Modules\System;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SystemController extends Controller{

    protected $systemLang;
    protected $user = null;
    protected $viewData = [];

    public function __construct(){
        $this->middleware(['auth:staff','staffcan:'.request()->route()->getName().'']);
        $this->viewData['systemLang'] = \DataLanguage::get();


    }


    protected function view($file,array $data = []){
        return view('system.'.$file,$data);
    }

    public function access_denied()
    {
        dd('Access Denied '.Session::get('msg'));
    }


    public function permissions($permission=false){
        $permissions = \Illuminate\Support\Facades\File::getRequire('../app/Modules/System/Permissions.php');
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



}