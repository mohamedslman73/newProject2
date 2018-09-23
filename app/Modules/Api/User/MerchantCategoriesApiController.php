<?php
namespace App\Modules\Api\User;

use App\Models\MerchantBranch;
use App\Models\MerchantCategory;
use App\Modules\Api\Transformers\User\MerchantCategoriesTransformer;
use Auth;
use Illuminate\Support\Facades\Validator;

class MerchantCategoriesApiController extends UserApiController {
    protected $Transformer;
    public function __construct(MerchantCategoriesTransformer $categoriesTransformer)
    {
        parent::__construct();
        $this->Transformer = $categoriesTransformer;
    }


    public function MerchantCategories(){
        $userobj = Auth()->user();
        $eloquentData = MerchantCategory::viewData($this->systemLang,[])
            ->withcount(['Merchants'=>function($query){
            $query->where('status','=','active');
        }]);

        if (request()->name) {
            orWhereByLang($eloquentData,'merchant_categories.name',request()->name);
        }

        if (request()->description) {
            orWhereByLang($eloquentData,'merchant_categories.description',request()->address);
        }

        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There are no Stores categories to show'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('Merchant Categories'));

    }

    public function ViewCategory($id)
    {
        $row = MerchantCategory::viewData($this->systemLang,[])
            ->where('id','=',$id)
            ->with(['Merchants'=>function($query){
                $query->where('status','=','active');
                $query->withcount('merchant_branch');
            }])
        ->where('status','=','active')
        ->first();

        if(!$row)
            return $this->respondNotFound(false,__('Category not found'));
        else
            return $this->respondSuccess($this->Transformer->transform($row->toArray(),[$this->systemLang]),__('Category Details'));
    }


}