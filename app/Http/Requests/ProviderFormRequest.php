<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ProviderFormRequest extends FormRequest
{

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
                    'name_ar'                               =>  'required',
                    'name_en'                               =>  'required',
                    'description_ar'                        =>  'required',
                    'description_en'                        =>  'required',
                    'address'                               =>  'required',
                    'logo'                                  =>  'image',
                    'admin_name'                            =>  'required',
                    'admin_job_title'                       =>  'required',
                    'admin_email'                           =>  'required|email',
                    'admin_mobile1'                         =>  'required|digits:11',
                    'admin_mobile2'                         =>  'digits:11',
                    'admin_phone1'                          =>  'required|numeric',
                    'admin_phone2'                          =>  'numeric',
                    'admin_fax1'                            =>  'numeric',
                    'admin_fax2'                            =>  'numeric',
                    'status'                                =>  'in:active,in-active'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name_ar'                               =>  'required',
                    'name_en'                               =>  'required',
                    'description_ar'                        =>  'required',
                    'description_en'                        =>  'required',
                    'address'                               =>  'required',
                    'logo'                                  =>  'image',
                    'admin_name'                            =>  'required',
                    'admin_job_title'                       =>  'required',
                    'admin_email'                           =>  'required|email',
                    'admin_mobile1'                         =>  'required|numeric:11',
                    'admin_mobile2'                         =>  'numeric:11',
                    'admin_phone1'                          =>  'required|numeric',
                    'admin_phone2'                          =>  'numeric',
                    'admin_fax1'                            =>  'numeric',
                    'admin_fax2'                            =>  'numeric',
                    'status'                                =>  'in:active,in-active'
                ];
            }
            default:break;
        }

    }
}
