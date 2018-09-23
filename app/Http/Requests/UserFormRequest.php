<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest
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
        $currantID = $this->segment(3);
        if($this->parent_id == 0){
            unset($this->parent_id);
        }
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                $validation = [
                    'firstname'                 =>  'required',
                    'middlename'                 =>  'required',
                    'lastname'                  =>  'required',
                    'email'                     =>  'required|email|unique:users,email',
                    'mobile'                    =>  'required|numeric|unique:users,mobile',
                    'password'                  =>  'required|confirmed',
                    'password_confirmation'     =>  'required',
                    'image'                     =>  'image',
                    'national_id'               =>  'required|numeric|digits:14|unique:users,national_id',
                    'birthdate'                 =>  'date_format:"Y-m-d"',
                    'status'                    =>  'required|in:active,in-active',
                ];

                if($this->parent_id){
                    $validation['parent_id'] = 'exists:users,id';
                }

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'firstname'                 =>  'required',
                    'middlename'                 =>  'required',
                    'lastname'                  =>  'required',
                    'email'                     =>  'required|email|unique:users,email,'.$currantID,
                    'mobile'                    =>  'required|numeric|unique:users,mobile,'.$currantID,
                    'password'                  =>  'confirmed',
                    'password_confirmation'     =>  '',
                    'image'                     =>  'image',
                    'national_id'               =>  'required|numeric|digits:14|unique:users,national_id,'.$currantID,
                    'birthdate'                 =>  'date_format:"Y-m-d"',
                    'status'                    =>  'required|in:active,in-active',
                ];

                if($this->parent_id){
                    $validation['parent_id'] = 'exists:users,id|not_in:'.$currantID;
                }

                return $validation;
            }
            default:break;
        }
    }
}
