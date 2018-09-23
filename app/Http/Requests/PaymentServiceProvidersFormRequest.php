<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class PaymentServiceProvidersFormRequest extends FormRequest
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
                    'payment_service_provider_category_id' => 'required|exists:payment_service_provider_categories,id',
                    'name_ar' =>  'required',
                    'name_en' =>  'required',
                    'logo'    =>  'required|image',
                    'status'  =>  'required|in:active,in-active',
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'payment_service_provider_category_id' => 'required|exists:payment_service_provider_categories,id',
                    'name_ar' =>  'required',
                    'name_en' =>  'required',
                    'logo'    =>  'image',
                    'status'  =>  'required|in:active,in-active',
                ];
            }
            default:break;
        }

    }
}
