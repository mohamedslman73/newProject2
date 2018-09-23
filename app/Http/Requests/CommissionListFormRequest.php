<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommissionListFormRequest extends FormRequest
{
    public function authorize(){
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
                $validator = [
                    'name'            =>  'required|unique:commission_list,name',
                    'commission_type' =>  'required|in:one,multiple'
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
                    'name'            =>  'required|unique:commission_list,name,'.$rowid,
                    'commission_type' =>  'required|in:one,multiple'
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
