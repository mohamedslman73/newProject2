<?php
namespace App\Modules\Api\Merchant;

use App\Models\Merchant;
use App\Modules\Api\Transformers\SubMerchantTransformer;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubMerchantApiController extends MerchantApiController {

    protected $Transformer;
    public function __construct(SubMerchantTransformer $subMerchantTransformer)
    {
        parent::__construct();
        $this->Transformer = $subMerchantTransformer;
    }


    public function getalldata(Request $request){
        $merchantStaff = Auth::user();

        $eloquentData = Merchant::viewData($this->systemLang,['merchants.merchant_category_id','merchants.address']);

        $eloquentData->where('merchants.parent_id', '=',$merchantStaff->merchant->id);

        whereBetween($eloquentData,'merchants.created_at',$request->created_at1,$request->created_at2);

        if($request->subMerchantId){
            $eloquentData->where('merchants.id', '=',$request->subMerchantId);
        }

        if($request->name){
            orWhereByLang($eloquentData,'merchants.name',$request->name);
        }

        if($request->categoryId){
            $eloquentData->where('merchants.merchant_category_id', '=',$request->categoryId);
        }

        if($request->address){
            $eloquentData->where('merchants.address','LIKE','%'.$request->address.'%');
        }

        if ($request->active) {
            $status = ($request->active) ? 'active' : 'in-active';
            $eloquentData->where('merchants.status', $status);
        }

        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There Are no Sub-Merchant to display'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('Sub-Merchants to display'));
    }

    public function create(Request $request){
        $merchantStaff = Auth::user();

        $RequestData = $this->headerdata(['name_en','description_en','name_ar','description_ar','merchant_category_id','address','area_id','logo','contact','branch',
            'branch_name_en','branch_address_en','branch_description_en','branch_name_ar','branch_address_ar','branch_description_ar','branch_latitude','branch_longitude','branch_status',
            'employee_firstname','employee_lastname','employee_email','employee_national_id','employee_password','employee_password_confirmation','employee_status'
        ]);


        $validator = Validator::make($RequestData, [
            'name_en'                               => 'required',
            'description_en'                        => 'required',
            'name_ar'                               => 'required',
            'description_ar'                        => 'required',
            'address'                               => 'required',
            'merchant_category_id'                  => 'numeric',
            'area_id'                               => 'required',
            //TODO logo validation
            //'logo'                                  => 'image',
            'contact.name.*'                        => 'required',
            'contact.email.*'                       => 'required|email',
            'contact.mobile.*'                      => 'required|digits:11',

            //Branch validation
            'branch_name_en'                        => 'required',
            'branch_address_en'                     => 'required',
            'branch_description_en'                 => 'required',
            'branch_name_ar'                        => 'required',
            'branch_address_ar'                     => 'required',
            'branch_description_ar'                 => 'required',
            'branch_latitude'                       => 'required',
            'branch_longitude'                      => 'required',
            'branch_status'                         => 'required|in:active,in-active',

            //Employee validation
            'employee_firstname'                    => 'required',
            'employee_lastname'                     => 'required',
            'employee_email'                        => 'required',
            'employee_national_id'                  => 'required|digits:14',
            'employee_password'                     =>  'required|confirmed',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $theRequest = $RequestData;
        $theRequest['area_id'] = getLastNotEmptyItem($RequestData['area_id']);
        $theRequest['parent_id'] = $merchantStaff->merchant->id;

        //TODO upload logo
        $theRequest['logo'] = base64_encode('logo');
        /*
        if($request->file('logo')) {
            $theRequest['logo'] = $request->logo->store('merchantlogo');
        }else{
            unset($theRequest['logo']);
        }
        */

        try {
            $GLOBALS['status'] = true;
            DB::transaction(function () use ($theRequest,$merchantStaff) {
                $merchant = $merchantStaff->merchant->child()->create($theRequest);
                $GLOBALS['merchant_id'] = $merchant->id;
                if(!$merchant)
                    $GLOBALS['status'] = false;
            });
            $eloquentData = $eloquentData = Merchant::viewData($this->systemLang,['merchants.merchant_category_id','merchants.address']);
            $eloquentData->where('merchants.parent_id', '=',$merchantStaff->merchant->id);
            $eloquentData->where('merchants.id',$GLOBALS['merchant_id']);
            $row = $eloquentData->first();
            return $this->respondCreated($this->Transformer->transform($row->toArray(), [$this->systemLang]), __('Sub-Merchant has been successfully added, data will be reviewed by admin'));
        } catch (QueryException $e){
            return $this->respondWithError(false,__('Sorry Couldn\'t add Sub-Merchant'));
        }

    }

    public function view($id){
        $merchantStaff = Auth::user();
        $eloquentData = Merchant::viewData($this->systemLang,['merchants.merchant_category_id','merchants.address']);
        $eloquentData->where('merchants.parent_id', '=',$merchantStaff->merchant->id);
        $eloquentData->where('merchants.id', '=',$id);

        $row = $eloquentData->first();
        if(!$row)
            return $this->respondNotFound(false,__('Sub-Merchant not found'));
        else
            return $this->respondSuccess($this->Transformer->transform($row->toArray(), [$this->systemLang]), __('Sub-Merchant details'));
    }

    public function edit($id,Request $request){
        $merchantStaff = Auth::user();
        $eloquentData = Merchant::viewData($this->systemLang,['merchants.merchant_category_id','merchants.address']);
        $eloquentData->where('merchants.parent_id', '=',$merchantStaff->merchant->id);
        $eloquentData->where('merchants.id', '=',$id);

        $row = $eloquentData->first();
        if(!$row)
            return $this->setStatusCode(403)->respondWithError(false,__('Sub-Merchant doesn\'t exist, Or you don\'t have permissions to edit it'));

        $RequestData = $this->headerdata(['name_en','description_en','name_ar','description_ar','merchant_category_id','address','area_id','logo','contact','branch',
            'branch_name_en','branch_address_en','branch_description_en','branch_name_ar','branch_address_ar','branch_description_ar','branch_latitude','branch_longitude','branch_status',
            'employee_firstname','employee_lastname','employee_email','employee_national_id','employee_password','employee_status'
        ]);
        $validator = Validator::make($RequestData, [
            'name_en'                               => 'required',
            'description_en'                        => 'required',
            'name_ar'                               => 'required',
            'description_ar'                        => 'required',
            'address'                               => 'required',
            'merchant_category_id'                  => 'numeric',
            'area_id'                               => 'required',
            'logo'                                  => 'image',
            'contact.name.*'                        => 'required',
            'contact.email.*'                       => 'required|email',
            'contact.mobile.*'                      => 'required|digits:11',

            //Branch validation
            'branch_name_en'                        => 'required',
            'branch_address_en'                     => 'required',
            'branch_description_en'                 => 'required',
            'branch_name_ar'                        => 'required',
            'branch_address_ar'                     => 'required',
            'branch_description_ar'                 => 'required',
            'branch_latitude'                       => 'required',
            'branch_longitude'                      => 'required',
            'branch_status'                         => 'required',

            //Employee validation
            'employee_firstname'                    => 'required',
            'employee_lastname'                     => 'required',
            'employee_email'                        => 'required',
            'employee_national_id'                  => 'required|digits:14',
            'employee_password'                     => 'required',
            'employee_password_confirmation'        => 'confirmed'
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $theRequest = $RequestData;
        $theRequest['area_id'] = getLastNotEmptyItem($RequestData['area_id']);

        $theRequest['logo'] = base64_encode('logo');
        /*
         * TODO upload logo
        if($request->file('logo')) {
            $theRequest['logo'] = $request->logo->store('merchantlogo');
        }else{
            unset($theRequest['logo']);
        }
        */

        try {
            $row->update($theRequest);
            return $this->respondSuccess($this->Transformer->transform($row->toArray(),[$this->systemLang]),__('Sub-Merchant successfully updated'));
        } catch (QueryException $e){
            return $this->respondWithError(false,__('Sub-Merchant couldn\'t be updated'));
        }
    }

    public function delete($id){
        $merchantStaff = Auth::user();
        $eloquentData = Merchant::viewData($this->systemLang,['merchants.merchant_category_id','merchants.address']);
        $eloquentData->where('merchants.parent_id', '=',$merchantStaff->merchant->id);
        $eloquentData->where('merchants.id', '=',$id);

        $row = $eloquentData->first();
        if(!$row)
            return $this->setStatusCode(403)->respondWithError(false,__('Sub-Merchant doesn\'t exist, Or you don\'t have permissions to delete it'));

        if(!$row->delete())
            return $this->setStatusCode(403)->respondWithError(false,__('Sub-Merchant couldn\'t be deleted'));

        return $this->respondSuccess(false,__('Sub-Merchant successfully deleted'));

    }


}