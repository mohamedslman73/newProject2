<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentOutputFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rowid = $this->segment(4);
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                return [
                    'name'   =>  'required|unique:payment_output,name',
                    'key'    =>  'required|array',
                    'language'   =>  'required|array'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name'   =>  'required|unique:payment_output,name,'.$rowid,
                    'key'    =>  'required|array',
                    'language'   =>  'required|array'
                ];
            }
            default:break;
        }
    }
}
