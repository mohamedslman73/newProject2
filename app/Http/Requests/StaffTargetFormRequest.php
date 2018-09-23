<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StaffTargetFormRequest extends FormRequest
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
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                return [
                    'staff_id'  =>  'required|exists:staff,id',
                    'year'      =>  'required|date_format:"Y"',
                    'month'     =>  Rule::unique('staff_target','month')
                        ->where('year',$this->year)
                        ->where('staff_id',$this->staff_id),
                    'amount'    =>  'required|numeric|min:1'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'amount'    =>  'required|numeric|min:1'
                ];
            }

            default:break;
        }

    }
}
