<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubMerchantFormRequest extends FormRequest
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
        $currantID = $this->segment(3);

        $validation = [
            'name_en'                               => 'required',
            'description_en'                        => 'required',
            'name_ar'                               => 'required',
            'description_ar'                        => 'required',
            'merchant_category_id'                  => 'numeric',
            'area_id'                               => 'required',
            'logo'                                  =>  'image',
            'contact.name.*'                        =>  'required',
            'contact.email.*'                       =>  'required|email',
            'contact.mobile.*'                      =>  'required|digits:11',

            //Branch validation
            'branch_name_en'                        => 'required',
            'branch_address_en'                     => 'required',
            'branch_description_en'                 => 'required',
            'branch_name_ar'                        => 'required',
            'branch_address_ar'                     => 'required',
            'branch_description_ar'                 => 'required',
            'branch_latitude'                       => 'required',
            'branch_longitude'                      => 'required',

            //Employee validation
            'staff_firstname'                    =>  'required',
            'staff_lastname'                     =>  'required',
            'staff_email'                        =>  'required|email|unique:merchant_staff,email',
            'staff_national_id'                  =>  'required|digits:14',
            'staff_password'                     =>  'required|confirmed',
        ];
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                return $validation;
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name_en'                   => 'required',
                    'description_en'            => 'required',
                    'name_ar'                   => 'required',
                    'description_ar'            => 'required',
                    'merchant_category_id'      => 'numeric',
                    'area_id'                   => 'required',
                    'logo'                      => 'nullable|image',
                    'contact.name.*'            => 'required',
                    'contact.email.*'           => 'required|email',
                    'contact.mobile.*'          => 'required|digits:11',
                ];
            }
            default:break;
        }
    }
}
