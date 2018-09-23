<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StaffFormRequest extends FormRequest
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
                    'firstname' =>  'required',
                    'lastname'  =>  'required',
                    'national_id'=>  'required|digits:14|unique:staff,national_id',
                    'email'      =>  'required|email|unique:staff,email',
                    'mobile'     =>  'required|mobile|unique:staff,mobile',
                    'avatar'                     =>  'image',
                    'gender'     =>  'required|in:male,female',
                    'birthdate' => 'required|date_format:"Y-m-d"',
                    'job_title'=> 'required',
                    'status'     =>  'required|in:active,in-active',
                    'permission_group_id'=> 'required|exists:permission_groups,id',
                    'password'                  =>  'required|confirmed',
                    'password_confirmation'     =>  'required',
                    //'supervisor_id' => 'required_if:permission_group_id|unique:staff',
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'firstname' =>  'required',
                    'lastname'  =>  'required',
                    'national_id'=>  'required|digits:14|unique:staff,national_id,'.$rowid,
                    'email'       =>  'required|email|unique:staff,email,'.$rowid,
                    'mobile'     =>  'required|mobile|unique:staff,mobile,'.$rowid,
                    'avatar'                     =>  'image',
                    'gender'     =>  'required|in:male,female',
                    'birthdate' => 'required|date_format:"Y-m-d"',
                    'job_title'=> 'required',
                    'status'     =>  'required|in:active,in-active',
                    'permission_group_id'=> 'required|exists:permission_groups,id',
                    'password'                  =>  'confirmed',
//                    'password_confirmation'     =>  'required_without:password'
                ];
            }
            default:break;
        }

    }
}
