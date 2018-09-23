<?php
namespace App\Modules\Api\User;

use App\Models\Merchant;
use App\Modules\Api\Transformers\User\MerchantTransformer;
use Auth;
use App\Libs\AreasData;
use Illuminate\Support\Facades\Validator;

class MerchantsApiController extends UserApiController {
    protected $Transformer;
    public function __construct(MerchantTransformer $merchantTransformer)
    {
        parent::__construct();
        $this->Transformer = $merchantTransformer;
    }


    public function viewAllMerchants(){
        $userobj = Auth()->user();

        $validator = Validator::make(request()->all(), [
            'name'          => 'nullable|string',
            'description'   => 'nullable|description',
            'areaId'        => 'nullable|numeric',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $eloquentData = Merchant::viewData($this->systemLang,['merchants.description_'.$this->systemLang,'merchants.area_id'])
            ->where('merchants.status','=','active')
            ->withcount('merchant_branch');

        if (request()->name) {
            orWhereByLang($eloquentData,'merchants.name',request()->name);
        }

        if (request()->description) {
            orWhereByLang($eloquentData,'merchants.description',request()->description);
        }

        if(request()->areaId){
            $eloquentData->whereIn('merchants.area_id',AreasData::getAreasDown((int) request()->areaId));
        }

        $lang = $this->systemLang;
        $eloquentData->with(['area'=>function($query)use($lang){
            $query->select(['id','areas.name_'.$this->systemLang.' as name']);
        }]);
        //dd($eloquentData->get()->toArray());
        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There Are no Merchants to display'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('Merchants'));

    }

    public function ViewMerchant($id){

        $row = Merchant::viewData($this->systemLang,['merchants.description_'.$this->systemLang])
            ->where('merchants.status','=','active')
            ->where('merchants.id','=',$id)
            ->with(['merchant_branch'=>function($query){
                $query->where('status','=','active');
            }])
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('Merchant not found'));
        else
            return $this->respondSuccess($this->Transformer->transform($row->toArray(),[$this->systemLang]),__('Merchant Details'));
    }

    public function ViewMerchantProducts($id)
    {
        $row = Merchant::viewData($this->systemLang,['merchants.description_'.$this->systemLang])
            ->where('merchants.status','=','active')
            ->where('merchants.id','=',$id)
            ->with(['merchant_product_categories'=>function($query){
                $query->where('status','=','active');
                $query->with(['product'=>function($sqlquery){
                    $sqlquery->where('status','=','active');
                    $sqlquery->with('uploadmodel');
                }]);
            }])
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('Merchant not found'));
        else
            return $this->respondSuccess($this->Transformer->transform($row->toArray(),[$this->systemLang]),__('Merchant Details'));
    }


}