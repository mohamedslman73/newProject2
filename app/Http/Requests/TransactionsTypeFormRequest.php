<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionsTypeFormRequest extends FormRequest
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
                    'name'=> 'required|max:100|unique:transactions_type,name'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name'=> 'required|max:100|unique:transactions_type,name,'.$rowid
                ];
            }
            default:break;
        }
    }
}
