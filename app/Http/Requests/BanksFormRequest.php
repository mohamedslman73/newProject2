<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BanksFormRequest extends FormRequest
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
                    'name_ar'          =>  'required',
                    'name_en'          =>  'required',
                    'logo'            =>  'required|image',
                    'swift_code'          =>  'required',
                    'account_id'          =>  'required|numeric'
                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'name_ar'          =>  'required',
                    'name_en'          =>  'required',
                    'logo'            =>  'image',
                    'swift_code'          =>  'required',
                    'account_id'          =>  'required|numeric'
                ];

                return $validation;
            }
            default:break;
        }
    }
}
