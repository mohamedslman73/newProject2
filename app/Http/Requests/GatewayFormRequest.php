<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GatewayFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
                $validation = [
                    'name_ar'                   =>'required',
                    'name_en'                   =>'required',
                    'driver_path'               =>'required',
                    'status'                    =>'required|in:active,in-active',
                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'name_ar'                   =>'required',
                    'name_en'                   =>'required',
                    'driver_path'               =>'required',
                    'status'                    =>  'required|in:active,in-active',
                ];



                return $validation;
            }
            default:break;
        }
    }
}
