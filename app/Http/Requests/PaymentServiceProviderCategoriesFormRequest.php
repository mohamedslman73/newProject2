<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class PaymentServiceProviderCategoriesFormRequest extends FormRequest
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
                    'name_ar' =>  'required',
                    'name_en' =>  'required',
                    'icon'    =>  'required|image',
                    'status'  =>  'required|in:active,in-active'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name_ar' =>  'required',
                    'name_en' =>  'required',
                    'icon'    =>  'image',
                    'status'  =>  'required|in:active,in-active'
                ];
            }
            default:break;
        }

    }
}
