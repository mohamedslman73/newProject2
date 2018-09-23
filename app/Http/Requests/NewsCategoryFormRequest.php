<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsCategoryFormRequest extends FormRequest
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
                    'descriptin_ar'       =>  'required',
                    'descriptin_en'       =>  'required',
                    'icon'            =>  'required|image',
                    'status'           =>  'required|in:active,in-active',
                    'type'           =>  'required|in:merchant,user',

                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'name_ar'          =>  'required',
                    'name_en'          =>  'required',
                    'descriptin_ar'       =>  'required',
                    'descriptin_en'       =>  'required',
                    'icon'            =>  'image',
                    'status'           =>  'required|in:active,in-active',
                    'type'           =>  'required|in:merchant,user',

                ];


                return $validation;
            }
            default:break;
        }
    }
}
