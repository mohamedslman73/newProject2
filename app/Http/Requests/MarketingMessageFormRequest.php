<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketingMessageFormRequest extends FormRequest
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
                    'message_type' =>  'required|in:sms,email,notification',
                    'title'          =>  'required',
                    'send_to'       =>  'required|in:user,merchant,marketing_message_data',
                    'send_at'     => 'required|date_format:"Y-m-d H:i:s"'
                ];

                if($this->message_type == 'sms'){
                    $validation['sms_content_ar'] = 'required';
                    $validation['sms_content_en'] = 'required';
                }elseif($this->message_type == 'email'){
                    $validation['email_name_ar'] = 'required';
                    $validation['email_name_en'] = 'required';
                    $validation['email_content_ar'] = 'required';
                    $validation['email_content_en'] = 'required';
                }else{
                    $validation['notification_name_ar'] = 'required';
                    $validation['notification_name_en'] = 'required';
                    $validation['notification_content_ar'] = 'required';
                    $validation['notification_content_en'] = 'required';
                    $validation['url_ar'] = 'required';
                    $validation['url_en'] = 'required';
                }


                if($this->send_to == 'marketing_message_data'){
                    $validation['marketing_filter_data'] = 'required|exists:marketing_message_data,id';
                }

                return $validation;

            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'message_type' =>  'required|in:sms,email,notification',
                    'title'          =>  'required',
                    'send_to'       =>  'required|in:user,merchant,marketing_message_data',
                    'send_at'     => 'required|date_format:"Y-m-d H:i:s"'
                ];

                if($this->message_type == 'sms'){
                    $validation['sms_content_ar'] = 'required';
                    $validation['sms_content_en'] = 'required';
                }elseif($this->message_type == 'email'){
                    $validation['email_name_ar'] = 'required';
                    $validation['email_name_en'] = 'required';
                    $validation['email_content_ar'] = 'required';
                    $validation['email_content_en'] = 'required';
                }else{
                    $validation['notification_name_ar'] = 'required';
                    $validation['notification_name_en'] = 'required';
                    $validation['notification_content_ar'] = 'required';
                    $validation['notification_content_en'] = 'required';
                    $validation['url_ar'] = 'required';
                    $validation['url_en'] = 'required';
                }


                if($this->send_to == 'marketing_message_data'){
                    $validation['marketing_filter_data'] = 'required|exists:marketing_message_data,id';
                }

                return $validation;
            }
            default:break;
        }
    }
}
