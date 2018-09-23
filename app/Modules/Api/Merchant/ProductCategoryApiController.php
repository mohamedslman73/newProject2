<?php
namespace App\Modules\Api\Merchant;

use App\Models\MerchantProductCategory;
use App\Modules\Api\Transformers\ProductCategoryTransformer;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoryApiController extends MerchantApiController {

    protected $Transformer;

    public function __construct(ProductCategoryTransformer $productCategoryTransformer)
    {
        parent::__construct();
        $this->Transformer =$productCategoryTransformer;
    }

    function getalldata(Request $request){
        $merchantStaff = Auth::user();
        $eloquentData = MerchantProductCategory::viewData($this->systemLang,['merchant_product_categories.status']);
        $eloquentData->where('merchant_product_categories.merchant_id',$merchantStaff->merchant->id);

        whereBetween($eloquentData,'merchant_product_categories.created_at',$request->created_at1,$request->created_at2);

        if($request->productCategoryId){
            $eloquentData->where('merchant_product_categories.id',$request->productCategoryId);
        }

        if($request->name){
            orWhereByLang($eloquentData,'merchant_product_categories.name',$request->name);
        }

        if($request->description){
            orWhereByLang($eloquentData,'merchant_product_categories.description',$request->description);
        }

        whereBetween($eloquentData,'merchant_product_categories.approved_at',$request->approved_at1,$request->approved_at2);

        if($request->isActive){
            $status = ($request->isActive)    ?   'active'    :   'in-active';
            $eloquentData->where('merchant_product_categories.status',$status);
        }


        $eloquentData->with('merchant');
        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There Are no Product Categories to display'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('Product Categories to display'));
    }

    function create(Request $request){
        $merchantStaff = Auth::user();

        $RequestData = $this->headerdata(['name_en','description_en','name_ar','description_ar','status','icon']);

        $validator = Validator::make($RequestData, [
            'name_en'                       => 'required',
            'description_en'                => 'required',
            'name_ar'                       => 'required',
            'description_ar'                => 'required',
            'status'                        => 'required|in:active,in-active',
            'icon'                          => 'nullable|image'
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }


        $theRequest = $RequestData;
        $theRequest['created_by_merchant_staff_id'] = $merchantStaff->id;


        if($request->file('icon')) {
            $theRequest['icon'] = $request->icon->store('productcategory');
        }else{
            unset($theRequest['icon']);
        }

        try {
            $ProductCategory = $merchantStaff->merchant->merchant_product_categories()->create($theRequest);
                return $this->respondCreated($this->Transformer->transform($ProductCategory,[$this->systemLang]),__('Category has been successfully added'));
        } catch (QueryException $e){
            if($e->getCode() == '23000')
                return $this->setCode(106)->respondWithError(false,__('Duplicated Category'));
                return $this->respondWithError(false,__('Sorry Couldn\'t add Category'));
        }

    }

    function view($id){
        $merchantStaff = Auth::user();
        $row = MerchantProductCategory::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)->with('merchant')->first();
        if(!$row)
            return $this->respondNotFound(false,__('Category not found'));
        else
            return $this->respondSuccess($this->Transformer->transform($row->toArray(),$this->systemLang),__('Category Details'));
    }

    function edit($id,Request $request){
        $merchantStaff = Auth::user();
        $row = MerchantProductCategory::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)
            ->get(['id','merchant_id','name_ar','name_en','description_ar','description_en','icon','status'])
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('Category not found'));

        $RequestData = $this->headerdata(['name_en','description_en','name_ar','description_ar','status','icon']);
        $validator = Validator::make($RequestData, [
            'name_en'                       => 'required',
            'description_en'                => 'required',
            'name_ar'                       => 'required',
            'description_ar'                => 'required',
            'status'                        => 'required|in:active,in-active',
            'icon'                          => 'nullable|image'
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $theRequest = $RequestData;
        $theRequest['created_by_merchant_staff_id'] = $merchantStaff->id;


        if($request->file('icon')) {
            $theRequest['icon'] = $request->icon->store('productcategory');
        }else{
            unset($theRequest['icon']);
        }

        try {
            $row->update($theRequest);
            return $this->respondSuccess($this->Transformer->transform(MerchantProductCategory::where('id',$row->id)->with('merchant')->first()->toArray(),[$this->systemLang]),__('Category successfully updated'));
        } catch (QueryException $e){
            return $this->respondWithError(false,__('Category couldn\'t be updated'));
        }

    }

    function delete($id){
        $merchantStaff = Auth::user();
        $row = MerchantProductCategory::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)->first();
        if(!$row)
            return $this->setStatusCode(403)->respondWithError(false,__('Category doesn\'t exist, Or you don\'t have permissions to delete it'));

        if(!$row->delete())
            return $this->setStatusCode(403)->respondWithError(false,__('Product category Couldn\'t be deleted'));

        return $this->respondSuccess(false,__('Product category has been deleted successfully'));

    }

}