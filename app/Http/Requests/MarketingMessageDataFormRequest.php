<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketingMessageDataFormRequest extends FormRequest
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
        //dd($this->send_at);
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                $validation = [
                    'type'           =>  'required|in:sms,email',
                    'title'          =>  'required',
                ];

                if($this->type == 'sms'){
                    $validation['sms_name'] = 'required|array';
                    $validation['sms_name.*'] = 'required';
                    $validation['mobile'] = 'required|array';
                    $validation['mobile.*'] = 'required|digits:11';

                }elseif($this->type == 'email'){
                    $validation['email_name'] = 'required|array';
                    $validation['email_name.*'] = 'required';
                    $validation['email'] = 'required|array';
                    $validation['email.*'] = 'required|email';
                }
                return $validation;

            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'type' =>  'required|in:sms,email',
                    'title'          =>  'required',
                ];

                if($this->type == 'sms'){
                    $validation['mobile'] = 'required|array';
                    $validation['sms_name'] = 'required|array';
                    $validation['sms_name.*'] = 'required';
                    $validation['mobile.*'] = 'required|digits:11';

                }elseif($this->type == 'email'){
                    $validation['email_name'] = 'required|array';
                    $validation['email_name.*'] = 'required';
                    $validation['email'] = 'required|array';
                    $validation['email.*'] = 'required|email';
                }

                return $validation;
            }
            default:break;
        }
    }
}
