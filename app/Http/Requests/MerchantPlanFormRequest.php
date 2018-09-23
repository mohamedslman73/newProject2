<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantPlanFormRequest extends FormRequest
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
                    'title'                         =>  'required',
//                    'description'                   =>  'required',
                    'months'                        =>  'required|numeric',
                    'amount'                        =>  'required|numeric',
                    'type'                        =>  'required|array|in:e-payment,e-commerce'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'title'                         =>  'required',
//                    'description'                   =>  'required',
                    'months'                        =>  'required|numeric',
                    'amount'                        =>  'required|numeric',
                    'type'                        =>  'required|array|in:e-payment,e-commerce'
                ];
            }
            default:break;
        }
    }
}
