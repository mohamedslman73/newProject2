<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SpecialCommissionListDataFormRequest extends FormRequest
{
    public function authorize(){
        return true;
    }

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
                $validator = [
                    'commission_type' =>  'required|in:one,multiple',
                    'special_commission_list_id'=> 'required|exists:special_commission_list,id',
                    'commission_list_id'=> [
                        'required',
                        'exists:commission_list,id',
                        Rule::unique('special_commission_list_data','commission_list_id')->where(function ($query) {
                            $query->where('special_commission_list_id', $this->special_commission_list_id);
                        })
                    ]
                ];

                if($this->commission_type == 'one'){
                    $validator['condition_data_charge_type'] = 'required|in:fixed,percent';
                    $validator['condition_data_system_commission'] = 'required|numeric';
                    $validator['condition_data_agent_commission'] = 'required|numeric';
                    $validator['condition_data_merchant_commission'] = 'required|numeric';
                }else{
                    $validator['condition_data.amount_from.*'] = 'required|numeric';
                    $validator['condition_data.amount_to.*'] = 'required|numeric';
                    $validator['condition_data.charge_type.*'] = 'required|in:fixed,percent';
                    $validator['condition_data.system_commission.*'] = 'required|numeric';
                    $validator['condition_data.agent_commission.*'] = 'required|numeric';
                    $validator['condition_data.merchant_commission.*'] = 'required|numeric';
                }
                return $validator;

            }
            case 'PUT':
            case 'PATCH':
            {
                $validator = [
                    'id'=> 'required|exists:special_commission_list_data,id'
                ];

                if($this->commission_type == 'one'){
                    $validator['condition_data_charge_type'] = 'required|in:fixed,percent';
                    $validator['condition_data_system_commission'] = 'required|numeric';
                    $validator['condition_data_merchant_commission'] = 'required|numeric';
                }else{
                    $validator['condition_data.amount_from.*'] = 'required|numeric';
                    $validator['condition_data.amount_to.*'] = 'required|numeric';
                    $validator['condition_data.charge_type.*'] = 'required|in:fixed,percent';
                    $validator['condition_data.system_commission.*'] = 'required|numeric';
                    $validator['condition_data.merchant_commission.*'] = 'required|numeric';
                }

                return $validator;
            }
            default:break;
        }
    }
}
