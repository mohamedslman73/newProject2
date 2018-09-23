<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantProductCategoryFormRequest extends FormRequest
{
    public function authorize()
    {
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
                return [
                    'merchant_id'                   =>  'required|exists:merchants,id',
                    'name_ar'                       =>  'required',
                    'name_en'                       =>  'required',
                    'description_ar'                =>  'required',
                    'description_en'                =>  'required',
                    'image'                         =>  'image|max:3000',
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'merchant_id'                   =>  'required|exists:merchants,id',
                    'name_ar'                       =>  'required',
                    'name_en'                       =>  'required',
                    'description_ar'                =>  'required',
                    'description_en'                =>  'required',
                    'image'                         =>  'image|max:3000',
                ];
            }
            default:break;
        }
    }
}
