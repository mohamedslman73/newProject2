<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class MerchantFormRequest extends FormRequest
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
                $planData = \App\Models\MerchantPlan::where('id',$this->contract_plan_id)
                    ->select('amount')
                    ->first();

                if($planData){
                    $planAmount = $planData->amount;
                }else{
                    $planAmount = 0;
                }


                $rules = [

                    // Merchant
                    'name_ar'              => 'required',
                    'name_en'              => 'required',
                    // 'description_ar'       => 'required',
                    // 'description_en'       => 'required',
                    'merchant_category_id' => 'required|exists:merchant_categories,id',
                    'area_id'              => 'required|area_id',
                    'address'              => 'required',
                    'is_reseller'          => 'required|in:active,in-active',
                    'logo'                 => 'image',
                    'status'               => 'required|in:active,in-active',
                    'contact.name'         => 'required|array',
//                    'contact.email'        => 'required|array',
                    'contact.mobile'       => 'required|array',
//                    'contact.email.*'      => 'required|email',
                    'contact.mobile.*'     => 'required|digits:11',
                    // 'staff_id'             => 'createMerchantStaffID',
                    'imagefiles.*'          =>  'nullable|image',

                    // Branch
                    'branch_name_ar'        =>  'required',
                    'branch_name_en'        =>  'required',
                    'branch_address_ar'     =>  'required',
                    'branch_address_en'     =>  'required',
                    // 'branch_description_ar' =>  'required',
                    // 'branch_description_en' =>  'required',
                    'branch_latitude'       =>  'required|numeric',
                    'branch_longitude'      =>  'required|numeric',

                    // Contract
                    'contract_plan_id'          =>  'required|exists:merchant_plans,id',
                    'contract_price'            =>  'required|numeric|min:'.$planAmount,
                    'contract_admin_name'       =>  'required',
                    'contract_admin_job_title'  =>  'required',
                    'file.*'                    =>  'file|mimes:pdf,png,jpg,jpeg,doc,xls,xlsx',

                    // Staff
                    'staff_firstname'                 =>  'required',
                    'staff_lastname'                  =>  'required',
                    'staff_national_id'               =>  'required|numeric|digits:14|unique:merchant_staff,national_id',
                    'staff_email'                     =>  'required|email|unique:merchant_staff,email',
                    'staff_mobile'                    =>  'required|numeric|unique:merchant_staff,mobile',
                    'staff_birthdate'                 =>  'date_format:"Y-m-d"',


                ];

                if(!$this->temp_data){
                    $rules['staff_password'] = 'required|confirmed';
                }

                return $rules;

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'merchant_category_id' =>  'required|exists:merchant_categories,id',
                    'area_id'              =>  'required|area_id',
                    'name_ar'              =>  'required',
                    'name_en'              =>  'required',
                    // 'description_ar'       =>  'required',
                    // 'description_en'       =>  'required',
                    'address'              =>  'required',
                    'logo'                 =>  'image',
                    'status'               =>  'in:active,in-active',
                    'contact.name'         =>  'required|array',
//                    'contact.email'        =>  'required|array',
                    'contact.mobile'       =>  'required|array',
//                    'contact.email.*'      =>  'required|email',
                    'contact.mobile.*'     =>  'required|digits:11'
                ];
            }
            default:break;
        }

    }
}
