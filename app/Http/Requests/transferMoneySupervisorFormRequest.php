<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class transferMoneySupervisorFormRequest extends FormRequest
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
                    'amount'          => 'required|confirmed|numeric|transferMoneyBySupervisor:'.$this->send_to,
                ];

            }
            default:break;
        }
    }
}
