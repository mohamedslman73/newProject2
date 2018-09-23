<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoyaltyProgramIgnoreFormRequest extends FormRequest
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
                    'loyalty_program_id' => 'required|exists:loyalty_programs,id',
                    'ignoremodel_type'   => 'required|in:App\Models\MerchantProduct,App\Models\PaymentServices,App\Models\Staff,App\Models\Merchant,App\Models\User',
                    'ignoremodel_id'     => 'required|numeric'
                ];
                return $validation;
            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'ignoremodel_type'   => 'required|in:App\Models\MerchantProduct,App\Models\PaymentServices,App\Models\Staff,App\Models\Merchant,App\Models\User',
                    'ignoremodel_id'     => 'required|numeric'
                ];

                return $validation;
            }
            default:break;
        }
    }
}
