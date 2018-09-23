<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class PaymentServiceAPIParameterFormRequest extends FormRequest
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
                    'external_system_id'       => 'required|numeric',
                    'payment_services_api_id'  => 'required|exists:payment_service_apis,id',
                    'name_ar' =>  'required',
                    'name_en' =>  'required',
                    'position' =>  'numeric',
                    'visible' =>  'required|in:yes,no',
                    'required' =>  'required|in:yes,no',
                    'type' =>  'required',
                    'is_client_id' =>  'required|in:yes,no',
                    'min_length' =>  'numeric',
                    'max_length' =>  'numeric'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'external_system_id'       => 'required|numeric',
                    'payment_services_api_id'  => 'required|exists:payment_service_apis,id',
                    'name_ar' =>  'required',
                    'name_en' =>  'required',
                    'position' =>  'numeric',
                    'visible' =>  'required|in:yes,no',
                    'required' =>  'required|in:yes,no',
                    'type' =>  'required',
                    'is_client_id' =>  'required|in:yes,no',
                    'min_length' =>  'numeric',
                    'max_length' =>  'numeric'
                ];
            }
            default:break;
        }

    }
}
