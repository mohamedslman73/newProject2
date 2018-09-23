<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class PaymentServicesFormRequest extends FormRequest
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
        $rowid = $this->segment(3);
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                return [
                    'payment_sdk_id' => 'required|exists:payment_sdk,id',
                    'payment_service_provider_id' => 'required|exists:payment_service_providers,id',
                    'payment_output_id' => 'required|exists:payment_output,id',
                    'commission_list_id'=> 'required|exists:commission_list,id',
                    'name_ar' =>  'required',
                    'name_en' =>  'required',
                    'status' =>  'required|in:active,in-active',
                    'icon' =>  'image',
                    'request_amount_input' =>  'required|in:yes,no'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'payment_sdk_id' => 'required|exists:payment_sdk,id',
                    'payment_service_provider_id' => 'required|exists:payment_service_providers,id',
                    'payment_output_id' => 'required|exists:payment_output,id',
                    'commission_list_id'=> 'required|exists:commission_list,id',
                    'name_ar' =>  'required',
                    'name_en' =>  'required',
                    'status' =>  'required|in:active,in-active',
                    'icon' =>  'image',
                    'request_amount_input' =>  'required|in:yes,no'
                ];
            }
            default:break;
        }

    }
}
