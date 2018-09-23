<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsFormRequest extends FormRequest
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
                $validation = [
                    'name_ar'          =>  'required',
                    'name_en'          =>  'required',
                    'content_ar'       =>  'required',
                    'content_en'       =>  'required',
                    'image'            =>  'required|image',
                    'status'           =>  'required|in:active,in-active',
                    'news_category_id' =>  'required|unique:news_categories,id',
                ];

                return $validation;


            }
            case 'PUT':
            case 'PATCH':
            {
                $validation = [
                    'name_ar'          =>  'required',
                    'name_en'          =>  'required',
                    'content_ar'       =>  'required',
                    'content_en'       =>  'required',
                    'image'            =>  'image',
                    'status'           =>  'required|in:active,in-active',
                    'news_category_id' =>  'required|exists:news_categories,id',
                ];

                return $validation;
            }
            default:break;
        }
    }
}
