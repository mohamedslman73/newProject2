<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantProductFormRequest extends FormRequest
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
                    'merchant_product_category_id'  =>  'required|exists:merchant_product_categories,id',
                    'merchant_id'                   =>  'required|exists:merchants,id',
                    'name_ar'                       =>  'required',
                    'name_en'                       =>  'required',
                    'description_ar'                =>  'required',
                    'description_en'                =>  'required',
                    'price'                         =>  'required|numeric|min:0',
                    'file'                          =>  'required|array',
                    'file.*'                        =>  'image'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'merchant_product_category_id'  =>  'required|exists:merchant_product_categories,id',
                    'merchant_id'                   =>  'required|exists:merchants,id',
                    'name_ar'                       =>  'required',
                    'name_en'                       =>  'required',
                    'description_ar'                =>  'required',
                    'description_en'                =>  'required',
                    'price'                         =>  'required|numeric|min:0',
                    'file'                          =>  'required|array',
                    'file.*'                        =>  'image'
                ];
            }
            default:break;
        }
    }
}
