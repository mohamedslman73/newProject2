<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SenderFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }


    public function all()
    {
        $request = parent::all();
        foreach ($request as $key => $value){
            if(!$request[$key]){
                unset($request[$key]);
            }
        }

        return $request;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        file_put_contents('aa.txt',print_r($this->all(),1));
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {

                if($this->type == 'sms'){
                    $validation = [
                        'type'    =>  'required|in:sms,email',
                        'send_to' =>  'required',
                        'sms_body'    =>  'required'
                    ];
                }else{
                    $validation = [
                        'type'       =>  'required|in:sms,email',
                        'from_name'  =>  'required',
                        'from_email' =>  'required|email',
                        'send_to'    =>  'required|email',
                        'subject'    =>  'required',
                        'email_body'       =>  'required',
                        'file'       =>  'file|mimes:jpeg,bmp,png,zip,rar,pdf,doc,xls,xlsx'
                    ];
                }

                return $validation;

            }
            case 'PUT':
            default:break;
        }
    }
}
