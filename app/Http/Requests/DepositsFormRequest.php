<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositsFormRequest extends FormRequest
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
                    'transaction_id' => 'required|unique:deposits,transaction_id',
                    'bank_id'        => 'required|exists:banks,id',
                    'amount'         => 'required|numeric',
                    'image'          => 'required|image',
                    'date'           => 'required|before_or_equal:"'.date('Y-m-d').'"'
                ];
                if ($this->deposit_to =='staff'){
                    $validation['staff_id'] = 'required|exists:staff,id';
                }elseif ($this->deposit_to =='merchant'){
                    $validation['merchant_id'] = 'required|exists:merchants,id';
                }
                return $validation;
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'status'=> 'required|in:approved,rejected'
                ];
            }
            default:break;
        }
    }
}
