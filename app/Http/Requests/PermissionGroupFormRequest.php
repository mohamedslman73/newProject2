<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionGroupFormRequest extends FormRequest
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
        $rowid = $this->segment(2);
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                return [
                    'name' => 'required',
                    'is_supervisor' => 'required|in:yes,no',
                    'permissions' => 'array|required'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name' => 'required',
                    'is_supervisor' => 'required|in:yes,no',
                    'permissions' => 'array|required'

                ];
            }
            default:break;
        }

    }
}
