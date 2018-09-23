<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantStaffGroupFormRequest extends FormRequest
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
                    'title'                         =>  'required',
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'merchant_id'                   =>  'required|exists:merchants,id',
                    'title'                         =>  'required',
                ];
            }
            default:break;
        }
    }
}
