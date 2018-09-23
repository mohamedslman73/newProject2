<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdvertisementFormRequest extends FormRequest
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

                $validation = [
                    'name'          => 'required',
                    'image'         => 'required|image',
                    'width'         => 'numeric',
                    'height'        => 'numeric',
                    'status'        => 'required|in:active,in-active',
                    'type'          => 'required|in:merchant,user',
                    'total_amount'  => 'required|numeric|min:1',
                    'merchant_id'   => 'exists:merchants,id',
                    'from_date'     => 'required|date_format:"Y-m-d"',
                    'to_date'       => 'required|date_format:"Y-m-d"'
                ];

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'name'          => 'required',
                    'image'         => 'image',
                    'width'         => 'numeric',
                    'height'        => 'numeric',
                    'status'        => 'required|in:active,in-active',
                    'type'          => 'required|in:merchant,user',
                    'total_amount'  => 'required|numeric|min:1',
                    'merchant_id'   => 'exists:merchants,id',
                    'from_date'     => 'required|date_format:"Y-m-d"',
                    'to_date'       => 'required|date_format:"Y-m-d"'
                ];

                return $validation;
            }
            default:break;
        }
    }
}
