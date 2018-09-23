<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class transferMoneyMainWalletsFormRequest extends FormRequest
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
                    'transfer_type'   =>  'required|in:transfer_in,transfer_out',
                    'main_wallet_id'  =>  'required|checkMainWallet:'.$this->transfer_type,
                    'password'        =>  'required|checkStaffPassword'
                ];

                if($this->transfer_type == 'transfer_in'){
                    $validation['amount_in'] = 'required|numeric|confirmed';
                }else{
                    $validation['amount_out'] = 'required|numeric|confirmed';
                    $validation['send_to']   = 'required|isSupervisorWalletAndNotMyWallet';
                }

                return $validation;
            }
            case 'PUT':
            case 'PATCH':
            {
                return false;
            }
            default:break;
        }
    }
}
