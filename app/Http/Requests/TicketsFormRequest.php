<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class TicketsFormRequest extends FormRequest
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
            case 'PUT':
            case 'DELETE':{
                return false;
            }
            case 'POST': {
                $rules =  [
                    'subject'        => 'required',
                    'details'        => 'required',
                    //'to_id_group'    => 'required|exists:permission_groups,id',
                ];

                if($this->merchant_id){
                    $rules['merchant_id'] = 'exists:merchants,id';
                }

                if($this->invoiceable_id){
                    $rules['invoiceable_id'] = 'exists:payment_invoice,id';
                }

                if($this->to_id){
                    $rules['to_id'] = 'exists:staff,id';
                }
                if($this->cc_id){
                    $rules['cc_id'] = 'exists:staff,id';
                }

                return $rules;
            }
            case 'PATCH':{

            }
            default:break;
        }

    }
}
