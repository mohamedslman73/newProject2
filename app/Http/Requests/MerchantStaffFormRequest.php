<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantStaffFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $currantID = $this->segment(4);

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                return [
                     'merchant_staff_group_id'   =>  'required|exists:merchant_staff_groups,id',
                    'firstname'                 =>  'required',
                    'lastname'                  =>  'required',
                    'national_id'               =>  'required|numeric|digits:14|unique:merchant_staff,national_id',
//                    'username'                  =>  'required|unique:merchant_staff,username',
           //         'email'                     =>  'email|unique:merchant_staff,email',
                    'password'                  =>  'required|confirmed',
                    'password_confirmation'     =>  'required',
                    'mobile'                    =>  'required|numeric|unique:merchant_staff,mobile',
                    'birthdate'                 =>  'date_format:"Y-m-d"',
                    'status'                    =>  'required|in:active,in-active',
                    'branches'                  =>  'required',
                    'branches.*'                =>  'required|exists:merchant_branches,id'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'merchant_staff_group_id'   =>  'required|exists:merchant_staff_groups,id',
                    'firstname'                 =>  'required',
                    'lastname'                  =>  'required',
                    'national_id'               =>  'required|numeric|digits:14|unique:merchant_staff,national_id'.iif($currantID,','.$currantID),
//                    'username'                  =>  'required|unique:merchant_staff,username'.iif($currantID,','.$currantID),
                    'email'                     =>  'required|email|unique:merchant_staff,email'.iif($currantID,','.$currantID),
                    'password'                  =>  'confirmed',
//                    'password_confirmation'     =>  'required',
                    'mobile'                    =>  'required|numeric|unique:merchant_staff,mobile'.iif($currantID,','.$currantID),
                    'birthdate'                 =>  'date_format:"Y-m-d"',
                    'status'                    =>  'required|in:active,in-active',
                    'branches'                  =>  'required',
                    'branches.*'                =>  'required|exists:merchant_branches,id'
                ];
            }
            default:break;
        }
    }
}
