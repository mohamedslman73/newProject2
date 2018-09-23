<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantBranchFormRequest extends FormRequest
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
                    'address_ar'                    =>  'required',
                    'address_en'                    =>  'required',
                    'description_ar'                =>  'required',
                    'description_en'                =>  'required',
                    'latitude'                      =>  'required|numeric',
                    'longitude'                     =>  'required|numeric',
                    'area_id'                       =>  'required|area_id',
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'merchant_id'                   =>  'required|exists:merchants,id',
                    'name_ar'                       =>  'required',
                    'name_en'                       =>  'required',
                    'address_ar'                    =>  'required',
                    'address_en'                    =>  'required',
                    'description_ar'                =>  'required',
                    'description_en'                =>  'required',
                    'latitude'                      =>  'required|numeric',
                    'longitude'                     =>  'required|numeric',
                    'area_id'                       =>  'required|area_id',
                ];
            }
            default:break;
        }
    }
}
