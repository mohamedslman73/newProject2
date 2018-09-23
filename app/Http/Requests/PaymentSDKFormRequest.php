<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class PaymentSDKFormRequest extends FormRequest
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
                    'name'    =>  'required',
                    'adapter_name' =>  'required|unique:payment_sdk,adapter_name',
                    'address' =>  'required',
                    'logo'    =>  'image',
                    'area_id' =>  'required|area_id'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name'    =>  'required',
                    'address' =>  'required',
                    'logo'    =>  'image',
                    'area_id' =>  'required|area_id'
                ];
            }
            default:break;
        }

    }
}
