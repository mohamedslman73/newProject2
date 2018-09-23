<?php

namespace App\Modules\System;

use App\Models\Setting;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class SettingController extends SystemController
{

    public function __construct(){
        parent::__construct();
        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Home'),
                'url'=> url('system'),
            ]
        ];
    }

    public function index(){
        $this->viewData['pageTitle'] = __('Setting');

        $settingGroups = Setting::select('group_name')->orderBy('sort','ASC')->groupBy('group_name')->get();

        $setting = [];
        foreach ($settingGroups as $key => $value) {
            $setting[] = Setting::where('group_name',$value->group_name)->orderBy('sort','ASC')->get();
        }


        $this->viewData['settingGroups'] = $settingGroups;
        $this->viewData['setting'] = $setting;

        return $this->view('setting.index',$this->viewData);
    }

    public function update(Request $request){
        $data = $request->all();

        $settingTable = Setting::get(['name']);
        foreach ($settingTable as $key => $value){
            if(isset($data[$value->name])){
                $valueToUpdate = $data[$value->name];
                if(is_array($valueToUpdate)){
                    $valueToUpdate = @serialize($valueToUpdate);
                }
                Setting::where(['name'=>$value->name])->update(['value'=>$valueToUpdate]);
            }else{
                Setting::where(['name'=>$value->name])->update(['value'=>'']);
            }
        }
        return back()->with('settingStatus',true);
    }



    /*
     * Send Notification
     */

    /*
    $data = Auth::user()->notifications()->limit(1)->get()->toArray();

    print_r($data);

    Notification::send(Auth::user(), new UserNotification([
    'title'=> 'Start Update Data',
    'description'=> 'Description Data',
    'url'=> route('merchant.merchant.index')
    ]));

    return '<body></body>';


        // Main View Vars
    $this->viewData['breadcrumb'][] = [
    'text'=> __('Setting'),
    ];

    $this->viewData['pageTitle'] = __('Setting');
    $this->viewData['setting'] = setting(null,true);

    $this->view('global.setting',$this->viewData);*/




}
