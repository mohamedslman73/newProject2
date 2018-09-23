<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RechargeListFormRequest extends FormRequest
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
                    'merchant_id' =>  'required|exists:merchants,id',
                    'numbers'     =>  'required|file|mimes:xls,xlsx'
                ];

                if($this->start_at == 'custom'){
                    $validation['cron_jobs'] = 'required|date_format:"Y-m-d H:i:s"|after_or_equal:now';
                }


                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            default:break;
        }
    }
}
