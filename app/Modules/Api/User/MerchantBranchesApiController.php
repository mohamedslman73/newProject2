<?php
namespace App\Modules\Api\User;

use App\Models\MerchantBranch;
use App\Models\MerchantProduct;
use App\Models\MerchantProductCategory;
use App\Modules\Api\Transformers\User\StoreTransformer;
use Auth;
use Illuminate\Support\Facades\Validator;

class MerchantBranchesApiController extends UserApiController {
    protected $Transformer;
    public function __construct(StoreTransformer $storeTransformer)
    {
        parent::__construct();
        $this->Transformer = $storeTransformer;
    }


    public function nearByMerchants(){
        $userobj = Auth()->user();
        $RequestData = $this->headerdata(['latitude','longitude','distance']);
        $validator = Validator::make($RequestData, [
            'latitude'          => 'required',
            'longitude'         => 'required',
            'distance'          => 'required',
        ]);

        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $eloquentData = MerchantBranch::findBranch($this->systemLang,$RequestData['latitude'],$RequestData['longitude'],$RequestData['distance']);

        if (request()->name) {
            orWhereByLang($eloquentData,'merchant_branches.name',request()->name);
        }

        if (request()->address) {
            orWhereByLang($eloquentData,'merchant_branches.address',request()->address);
        }

        $eloquentData->with('area')->withCount('categories');
        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There Are no Stores near you to display, try to search farther'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('Stores near you'));

    }

    public function ViewStore($branchid){

        $RequestData = $this->headerdata(['latitude','longitude','branchId','distance']);
        $validator = Validator::make($RequestData, [
            'latitude'          => 'required',
            'longitude'         => 'required',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $row = MerchantBranch::viewData($this->systemLang,['merchant_branches.merchant_id'])
            ->where('merchant_branches.id',(int) $branchid)
            ->with('categories')
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('Store not found'));
        else
            return $this->respondSuccess($this->Transformer->transform($row->toArray(),[$this->systemLang]),__('Store Details'));
    }


}