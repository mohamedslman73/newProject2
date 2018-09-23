<?php

namespace App\Modules\System;

use Illuminate\Http\Request;
use App\Models\Notifications;
use Auth;

class NotificationController extends SystemController
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


    public function index(Request $request){
        if(isset($request->page)){
            $this->viewData['notifications'] = \Illuminate\Support\Facades\Auth::user()->notifications()->orderBy('created_at','DESC')->paginate(10);
            return ['content'=>$this->view('global.notification-view',$this->viewData)->render(),'next'=> $this->viewData['notifications']->nextPageUrl()];
        }else{
            $this->viewData['breadcrumb'][] = [
                'text'=> __('Notifications'),
            ];

            $this->viewData['pageTitle'] = 'Notifications';

            return $this->view('global.notification',$this->viewData);
        }
    }


    public function url($ID){
        $notifications = Auth::user()->notifications()->where('id',$ID)->first();
        if($notifications){
            if($notifications->read_at == null){
                $notifications->markAsRead();
            }
            return redirect($notifications->data['url']);
        }else{
            return back();
        }
    }



}
