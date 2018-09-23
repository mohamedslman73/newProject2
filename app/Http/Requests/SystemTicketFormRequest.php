<?php

namespace App\Http\Requests;

use App\Models\Staff;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\SystemTicket;
use Auth;
class SystemTicketFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){

        if($this->reply_to) {
            $id = $this->reply_to;
            $staffID = Auth::id();
            $result = SystemTicket::where('email.id', $id)
                ->where(function ($query) use ($staffID) {
                    $query->where(function ($query) use ($staffID) {
                        $query->where('email.sendermodel_type', 'App\Models\Staff');
                        $query->where(function ($query) use ($staffID) {
                            $query->where('email.sendermodel_id', $staffID);
                            $query->orWhereNull('email.sendermodel_id');
                        });
                    })
                        ->orWhere(function ($query) use ($staffID) {
                            $query->where('email_receiver.receivermodel_type', 'App\Models\Staff');
                            $query->where(function ($query) use ($staffID) {
                                $query->where('email_receiver.receivermodel_id', $staffID);
                                $query->orWhereNull('email_receiver.receivermodel_id');
                            });
                        });
                })
                ->leftjoin('email_receiver','email.id','=','email_receiver.email_id')
                ->first();

            if (!$result) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function all()
    {
        $request = parent::all();
        foreach ($request as $key => $value){
            if(!$request[$key]){
                unset($request[$key]);
            }
        }
        return $request;
    }

    public function rules(){
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST': {
                if($this->reply_to){
                    $validation = [
                        'reply_to'   =>  'required|exists:email,id',
                        'subject'   =>  'required',
                        'body'      =>  'required',
                        'file'      =>  'file|mimes:jpeg,bmp,png,zip,rar,pdf,doc,xls,xlsx',
                    ];
                }else{
                    $validation = [
                        'subject'           =>  'required',
                        'body'              =>  'required',
                        'receivermodel_id'  =>  'exists:merchant_staff,id',
                        'file'              =>  'file|mimes:jpeg,bmp,png,zip,rar,pdf,doc,xls,xlsx',
                    ];

                    if(Auth::user() instanceof Staff){
                        if($this->send_to_type == 'merchant'){
                            $validation['merchant_id'] = 'required|exists:merchants,id';
                        }else{
                            $validation['staff_id'] = 'required|exists:staff,id';
                        }
                    }

                }

                return $validation;
            }
            case 'PUT':
            default:break;
        }
    }
}
