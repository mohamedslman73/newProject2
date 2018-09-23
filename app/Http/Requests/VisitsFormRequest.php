<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisitsFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                $validation =  [
                    'shop_name' => 'required',
                    'name'      => 'required',
                    'address'   => 'required',
                    'phone'     => 'numeric',
                    'mobile'    => 'required|numeric',
                    'status'    => 'required|in:ok,reject,hesitant',
//                    'consumption'  => 'numeric',
                    'latitude'  => 'numeric',
                    'longitude' => 'numeric'
                ];

                if(!empty($this->consumption)){
                    $validation['consumption'] = 'numeric';
                }else{
                    $this->consumption = 0;
                }
                return $validation;
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'comment' => 'required'
                ];
            }
            default:break;
        }
    }
}
