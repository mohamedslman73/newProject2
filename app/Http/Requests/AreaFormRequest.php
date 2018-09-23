<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AreaFormRequest extends FormRequest
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
                    'area_type_id'                  =>  'required',
                    'name_ar'                       =>  'required',
                    'name_en'                       =>  'required',
                    'latitude'                      =>  'required|numeric',
                    'longitude'                     =>  'required|numeric'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name_ar'                       =>  'required',
                    'name_en'                       =>  'required',
                    'latitude'                      =>  'required|numeric',
                    'longitude'                     =>  'required|numeric',
                ];
            }
            default:break;
        }
    }
}
