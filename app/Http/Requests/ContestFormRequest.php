<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContestFormRequest extends FormRequest{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    public function all(){
        $data = parent::all();
        $data['except_beneficiary_ids'] = array_map('trim',explode("\n",$data['except_beneficiary_ids']));
        return $data;
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
                    'name_ar'       => 'required',
                    'name_en'       => 'required',
                    'terms_ar'      => 'required',
                    'terms_en'      => 'required',
                    'type'          => 'required|in:e-payment,e-commerce',
                    'beneficiary'   => 'required|in:users,merchants',
                    'target_type'   => 'required|in:one_service,all_services',
                    'target'        => 'required|numeric',
                    'prize'         => 'required|numeric',
                    'start_date'    => 'required|after_or_equal:'.date('Y-m-d'),
                    'end_date'      => 'required|after:'.$this->start_date,
                    'status'        => 'required|in:active,in-active',
                    'service_ids'   => 'required|array',
                ];


                if($this->type == 'e-payment'){
                    $validation['service_ids.*'] = 'required|numeric|exists:payment_services,id';
                }else{
                    // E-Commerce UPDATE
//                    $validation['service_ids.*'] = 'required|numeric|exists:payment_services,id';
                }


                if($this->beneficiary == 'merchants'){
                    $validation['except_beneficiary_ids.*'] = 'numeric|exists:merchants,id';
                }else{
                    $validation['except_beneficiary_ids.*'] = 'numeric';
                }

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'name_ar'       => 'required',
                    'name_en'       => 'required',
                    'terms_ar'      => 'required',
                    'terms_en'      => 'required',
                    'type'          => 'required|in:e-payment,e-commerce',
                    'beneficiary'   => 'required|in:users,merchants',
                    'target_type'   => 'required|in:one_service,all_services',
                    'target'        => 'required|numeric',
                    'prize'         => 'required|numeric',
                    'start_date'    => 'required|after_or_equal:'.date('Y-m-d'),
                    'end_date'      => 'required|after:'.$this->start_date,
                    'status'        => 'required|in:active,in-active',
                    'service_ids'   => 'required|array',
                ];


                if($this->type == 'e-payment'){
                    $validation['service_ids.*'] = 'required|numeric|exists:payment_services,id';
                }else{
                    // E-Commerce UPDATE
//                    $validation['service_ids.*'] = 'required|numeric|exists:payment_services,id';
                }


                if($this->beneficiary == 'merchants'){
                    $validation['except_beneficiary_ids.*'] = 'numeric|exists:merchants,id';
                }else{
                    $validation['except_beneficiary_ids.*'] = 'numeric';
                }

                return $validation;
            }
            default:break;
        }
    }
}
