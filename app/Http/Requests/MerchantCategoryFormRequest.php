<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MerchantCategoryFormRequest extends FormRequest
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
        $rowid = $this->segment(4);
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                return [
                    'name_ar'               =>  'required',
                    'name_en'               =>  'required',
                    'description_ar'        =>  'required',
                    'description_en'        =>  'required',
                    'icon'                  =>  'required|image',
                    'status'                =>  [
                        'required',
                        Rule::in(['active', 'in-active']),
                    ],
                    'main_category_id'      => 'nullable|exists:merchant_categories,id',
                    'attribute_categories'  => 'nullable|array'
                ];

            }
            case 'PUT':
            case 'PATCH':
            {
                $return = [
                    'name_ar'               =>  'required',
                    'name_en'               =>  'required',
                    'description_ar'        =>  'required',
                    'description_en'        =>  'required',
                    'icon'                  =>  'image',
                    'status'                =>  [
                        'required',
                        Rule::in(['active', 'in-active']),
                    ],
                    'attribute_categories'  => 'nullable|array'
                ];

                if(!is_null($this->main_category_id)){
                    $return['main_category_id'] = 'exists:merchant_categories,id|not_in:'.$rowid;
                }

                return $return;

            }
            default:break;
        }

    }
}
