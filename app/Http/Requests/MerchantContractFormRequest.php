<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantContractFormRequest extends FormRequest
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
        $planData = \App\Models\MerchantPlan::where('id',$this->plan_id)->select('amount')->first();
        if($planData){
            $planAmount = $planData->amount;
        }else{
            $planAmount = 0;
        }

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
                    'merchant_id'               =>  'required|exists:merchants,id',
                    'plan_id'                   =>  'required|exists:merchant_plans,id',
                    'price'                     =>  'required|numeric|min:'.$planAmount,
//                    'start_date'                =>  'required|date_format:Y-m-d',
//                    'end_date'                  =>  'required|date_format:Y-m-d',
//                    'contract_pdf'              =>  'file|mimes:pdf|max:3000',
                    'admin_name'                =>  'required',
                    'admin_job_title'           =>  'required',
                    'file.*'                    =>  'file|mimes:pdf,png,jpg,jpeg,doc,xls,xlsx'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
//                    'merchant_id'               =>  'required|exists:merchants,id',
//                    'plan_id'                   =>  'required|exists:merchant_plans,id',
//                    'price'                     =>  'required|numeric|min:'.$planAmount,
//                    'start_date'                =>  'required|date_format:Y-m-d',
//                    'end_date'                  =>  'required|date_format:Y-m-d',
//                    'contract_pdf'              =>  'file|mimes:pdf|max:3000',
                    'admin_name'                =>  'required',
                    'admin_job_title'           =>  'required',
                    'file.*'                    =>  'file|mimes:pdf,png,jpg,jpeg,doc,xls,xlsx'
                ];
            }
            default:

                break;
        }
    }
}
