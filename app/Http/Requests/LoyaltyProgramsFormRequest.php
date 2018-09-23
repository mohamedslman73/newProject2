<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoyaltyProgramsFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(){

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                $return = [
                    'name_ar'   =>  'required',
                    'name_en'   =>  'required',
                    'type'      =>  'required|in:invoice,order',
                    'transaction_type' => 'required|in:wallet,cash',
                    'pay_type'  =>  'required|in:income,expenses',
                    'owner'     =>  'required|in:user,merchant,staff',
                    'list_type' => 'required|in:static,dynamic'
                ];

                if($this->list_type == 'static'){
                    $return['list_point']  = 'required|numeric';
                    $return['list_amount'] = 'required|numeric';
                }else{
                    $return['list']               = 'required|array';
                    $return['list.from_amount.*'] = 'required|numeric';
                    $return['list.to_amount.*']   = 'required|numeric';
                    $return['list.point.*']       = 'required|numeric';
                }
                return $return;
            }
            case 'PUT':
            case 'PATCH':
            {
                $return = [
                    'name_ar'   =>  'required',
                    'name_en'   =>  'required',
                    'type'      =>  'required|in:invoice,order',
                    'transaction_type' => 'required|in:wallet,cash',
                    'pay_type'  =>  'required|in:income,expenses',
                    'owner'     =>  'required|in:user,merchant,staff',
                    'list_type' => 'required|in:static,dynamic'
                ];

                if($this->list_type == 'static'){
                    $return['list_point']  = 'required|numeric';
                    $return['list_amount'] = 'required|numeric';
                }else{
                    $return['list']               = 'required|array';
                    $return['list.from_amount.*'] = 'required|numeric';
                    $return['list.to_amount.*']   = 'required|numeric';
                    $return['list.point.*']       = 'required|numeric';
                }
                return $return;
            }
            default:break;
        }
    }
}
