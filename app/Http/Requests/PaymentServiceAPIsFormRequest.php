<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class PaymentServiceAPIsFormRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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
                return [
                    'payment_service_id' => 'required|exists:payment_services,id',
                    'service_type' => 'required|in:payment,inquiry,inquire',
                    'name' =>  'required',
                    'external_system_id' =>  'required|numeric',
                    'price_type' =>  'required|in:0,1,2,3',
                    'service_value' =>  'numeric',
                    'min_value' =>  'numeric',
                    'max_value' =>  'numeric',
                    'commission_type' =>  'required|in:0,1,2,3',
                    'commission_value_type' =>  'required|in:0,1,2,3',
                    'fixed_commission' =>  'required|numeric',
                    'default_commission' =>  'numeric',
                    'from_commission' =>  'numeric',
                    'to_commission' =>  'numeric'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'payment_service_id' => 'required|exists:payment_services,id',
                    'service_type' => 'required|in:payment,inquiry,inquire',
                    'name' =>  'required',
                    'external_system_id' =>  'required|numeric',
                    'price_type' =>  'required|in:0,1,2,3',
                    'service_value' =>  'numeric',
                    'min_value' =>  'numeric',
                    'max_value' =>  'numeric',
                    'commission_type' =>  'required|in:0,1,2,3',
                    'commission_value_type' =>  'required|in:0,1,2,3',
                    'fixed_commission' =>  'required|numeric',
                    'default_commission' =>  'numeric',
                    'from_commission' =>  'numeric',
                    'to_commission' =>  'numeric'
                ];
            }
            default:break;
        }

    }
}
