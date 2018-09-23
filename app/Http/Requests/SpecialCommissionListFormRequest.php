<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpecialCommissionListFormRequest extends FormRequest
{
    public function authorize(){
        return true;
    }

    public function rules()
    {
        $rowid = $this->segment(3);
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                $validator = [
                    'merchant_id' => 'required|unique:special_commission_list,merchant_id|exists:merchants,id',
                   /* 'active_system_commission'=> 'in:yes',
                    'active_agent_commission'=> 'in:yes',
                    'active_merchant_commission'=> 'in:yes'*/
                ];

                return $validator;
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    /*'active_system_commission'=> 'in:yes',
                    'active_agent_commission'=> 'in:yes',
                    'active_merchant_commission'=> 'in:yes'*/
                ];
            }
            default:break;
        }
    }
}
