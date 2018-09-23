<?php
namespace App\Modules\Api\User;

use App\Models\MerchantProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Modules\Api\Transformers\User\OrderitemTransformer;
use App\Modules\Api\Transformers\User\OrderTransformer;
use App\Modules\Api\Transformers\User\ProductTransformer;
use App\Modules\Api\Transformers\User\TransactionTransformer;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartApiController extends UserApiController
{
    protected $Transformer;
    public function __construct(ProductTransformer $productTransformer)
    {
        parent::__construct();
        $this->Transformer = $productTransformer;
    }

    public function checkProducts(Request $request){
        $userObj = Auth::user();
        $RequestData = $this->headerdata(['productId','qty']);

        $validator = Validator::make($RequestData, [
            'productId.*'            => 'required|numeric|exists:merchant_products,id',
            'qty.*'                  => 'required|numeric',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $eloquentData = MerchantProduct::viewDataApi($this->systemLang,[]);

        $eloquentData->whereIn('merchant_products.id',$RequestData['productId']);


        $rows = $eloquentData->get();
        if(!$rows)
            return $this->respondNotFound(false,__('No products to show'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),$this->systemLang),__('Orders\'s Products'));
    }

}