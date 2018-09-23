<?php
namespace App\Modules\Api\Merchant;

use App\Models\MerchantProduct;
use App\Modules\Api\Transformers\ProductTransformer;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductApiController extends MerchantApiController
{
    protected $Transformer;
    public function __construct(ProductTransformer $productTransformer)
    {
        parent::__construct();
        $this->Transformer = $productTransformer;
    }


    public function getalldata(Request $request){
        $merchantStaff = Auth::user();
        $merchantProductCategories = $merchantStaff->merchant->merchant_product_catgories()->pluck('id')->toArray();

        $eloquentData = MerchantProduct::viewDataApi($this->systemLang,['merchant_products.merchant_id']);

        $eloquentData->wherein('merchant_products.merchant_product_category_id',$merchantProductCategories);

        whereBetween($eloquentData,'merchant_products.created_at',$request->created_at1,$request->created_at2);

        if($request->productId){
            $eloquentData->where('merchant_products.id', '=',$request->productId);
        }

        if($request->name){
            orWhereByLang($eloquentData,'merchant_products.name',$request->name);
        }

        if($request->description){
            orWhereByLang($eloquentData,'merchant_products.description',$request->description);
        }

        whereBetween($eloquentData,'merchant_products.price',$request->price1,$request->price2);

        if($request->active){
            $status = (($request->active)   ?   'active':'in-active');
            $eloquentData->where('merchant_products.status','=',$status);
        }

        whereBetween($eloquentData,'merchant_products.approved_at',$request->approved_at1,$request->approved_at2);

        if($request->categoryId){
            $eloquentData->where('merchant_products.merchant_product_category_id',$request->categoryId);
        }


        $eloquentData->distinct();
        $eloquentData->with('category')->with('uploadmodel')->with('merchant');

        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('No products to display'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('Products details'));

    }

    public function create(Request $request){
        $merchantStaff = Auth::user();

        $RequestData = $this->headerdata(['name_en','description_en','name_ar','description_ar','status','merchant_product_category_id','price','image']);

        $categories = $merchantStaff->merchant->merchant_product_categories()->pluck('id')->toArray();
        $validator = Validator::make($RequestData, [
            'name_en'                               => 'required',
            'description_en'                        => 'required',
            'name_ar'                               => 'required',
            'description_ar'                        => 'required',
            'merchant_product_category_id'          => 'numeric|in:'.implode(',',$categories),
            'price'                                 => 'numeric',
            'status'                                => 'in:active,in-active',
            'image.*'                               => 'image'
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }


        $theRequest = $RequestData;
        $theRequest['created_by_merchant_staff_id'] = $merchantStaff->id;
        $theRequest['merchant_product_category_id'] = (int) $theRequest['merchant_product_category_id'];

        try {
            $product = $merchantStaff->merchant->merchant_products()->create($theRequest);
            $row = MerchantProduct::where('id',$product->id)->with('merchant')->with('category')->first();
            return $this->respondCreated($this->Transformer->transform($row->toArray(),$this->systemLang),__('Product successfuly added'));
        } catch (QueryException $e){
            if($e->getCode() == '23000')
                    return $this->setCode(106)->respondWithoutError(false,__('Duplicated product'));
                return $this->setCode(107)->respondWithoutError(false,__('Couldn\'t add product'));
        }
    }

    public function view($id){
        $merchantStaff = Auth::user();
        $row = MerchantProduct::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)
            ->with('merchant')->with('category')
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('This Product doesn\'t exist'));

        return $this->respondSuccess($this->Transformer->transform($row->toArray(),$this->systemLang),__('Product details'));
    }

    public function edit($id,Request $request){
        $merchantStaff = Auth::user();
        $row = MerchantProduct::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)->first();
        if(!$row)
            return $this->setStatusCode(403)->respondWithError(false,__('Product doesn\'t exist'));

        $categories = $merchantStaff->merchant->merchant_product_categories()->pluck('id')->toArray();
        $RequestData = $this->headerdata(['name_en','description_en','name_ar','description_ar','status','merchant_product_category_id','price','image']);
        $validator = Validator::make($RequestData, [
            'name_en'                               => 'required',
            'description_en'                        => 'required',
            'name_ar'                               => 'required',
            'description_ar'                        => 'required',
            'merchant_product_category_id'          => 'numeric|in:'.implode(',',$categories),
            'price'                                 => 'numeric',
            'status'                                => 'in:active,in-active',
            'image.*'                               => 'image'
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }


        $theRequest = $RequestData;
        $theRequest['created_by_merchant_staff_id'] = $merchantStaff->id;
        $theRequest['merchant_product_category_id'] = (int) $theRequest['merchant_product_category_id'];

        try {
            $row->update($theRequest);
            $nrow = MerchantProduct::where('id',$row->id)->with('merchant')->with('category')->first();
            return $this->respondSuccess($this->Transformer->transform($nrow->toArray(),$this->systemLang),__('Successfuly edited Product'));
        } catch (QueryException $e){
            return $this->respondWithError(false,__('Product couldn\'t be updated'));
        }

    }

    public function delete($id){
        $merchantStaff = Auth::user();
        $row = MerchantProduct::where('merchant_id',$merchantStaff->merchant->id)->where('id',$id)->first();
        if(!$row)
            return $this->setStatusCode(403)->respondWithError(false,__('Product doesn\'t exist'));

        if(!$row->delete())
            return $this->setStatusCode(403)->respondWithError(false,__('Product couldn\'t be deleted'));

        return $this->respondSuccess(false,__('Product successfully deleted'));
    }

}