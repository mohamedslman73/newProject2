<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class transferMoneyStaffFormRequest extends FormRequest
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
            case 'PUT':
            case 'PATCH':
            case 'POST': {
                return [
                    'password'        => 'required|checkStaffPassword',
                    'send_to'         => 'required|exists:wallet,id',
                    'amount'          => 'required|numeric|confirmed|transferMoneyByStaff:'.$this->send_to,
                ];
            }
            default:break;
        }
    }
}
