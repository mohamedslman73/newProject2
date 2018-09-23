<?php
namespace App\Modules\Api\Merchant;

use App\Models\MerchantBranch;
use App\Models\MerchantProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\User;
use App\Modules\Api\Transformers\OrderitemTransformer;
use App\Modules\Api\Transformers\OrderTransformer;
use App\Modules\Api\Transformers\TransactionTransformer;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderApiController extends MerchantApiController
{
    protected $Transformer;
    public function __construct(OrderTransformer $orderTransformer)
    {
        parent::__construct();
        $this->Transformer = $orderTransformer;
    }


    public function getalldata(Request $request){
        $merchantStaff = Auth::user();

        if(!count($merchantStaff->staff_branches()))
            return $this->respondNotFound(false,__('You don\'t have access to any branches to view their orders'));

        $eloquentData = Order::viewData($this->systemLang,['merchant_branch_id']);

        whereBetween($eloquentData,'orders.created_at',$request->created_at1,$request->created_at2);

        $eloquentData->where('merchant_branches.merchant_id','=',$merchantStaff->merchant->id);
        $eloquentData->wherein('merchant_branches.id',$merchantStaff->staff_branches());

        if ($request->orderId) {
            $eloquentData->where('orders.id', '=', $request->orderId);
        }

        if ($request->isPaid) {
            $is_paid = ($request->isPaid)   ?   'yes'       :       'no';
            $eloquentData->where('orders.id', '=', $is_paid);
        }

        if ($request->branchId){
            $eloquentData->where('orders.merchant_branch_id', '=', $request->branchId);
        }


        $eloquentData->with(['orderitems'=>function($sql){
            $sql->with('orderItemAttribute');
        },'trans']);
        $rows = $eloquentData->jsonPaginate();
        if(!$rows->items())
            return $this->respondNotFound(false,__('There are no orders to display'));

        return $this->respondSuccess($this->Transformer->transformCollection($rows->toArray(),[$this->systemLang]),__('Orders to display'));
    }

    public function create(Request $request){
        $merchantStaff = Auth::user();

        $RequestData = $this->headerdata(['merchant_branch_id','users','product']);
        $merchantproducts = $merchantStaff->merchant->merchant_products->pluck('id')->toArray();
        $merchant = $merchantStaff->merchant;

        $validator = Validator::make($RequestData, [
            'merchant_branch_id'        => 'required|integer',
            'users.*'                   => 'required|array:3',
            'users.*.id'                => 'required|integer',
            'users.*.amount'            => 'required|integer',
            'users.*.paytype'           => 'required|in:wallet,cash',
            'product.*.id'              => 'required|integer|in:'.implode(',',$merchantproducts),
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $GLOBALS['status'] = false;
        $lang = $this->systemLang;
        DB::transaction(function () use ($RequestData,$request,$merchant,$lang) {
            $Order = Order::create([
                'merchant_branch_id'            => $RequestData['merchant_branch_id'],
                'creatable_id'                  => Auth::id(),
                'creatable_type'                => get_class($request->user()),
            ]);


            $ProIds = recursiveFind($RequestData['product'],'id');
            $Products = $merchant->merchant_products()->whereIn('id',$ProIds)->with('attribute')->get();

            $totalArr = [];
            foreach($RequestData['product'] as $oneProduct){
                $productData = $Products->where('id',$oneProduct['id'])->first();
                $productAttributes = $productData->attribute()->with('attribute')->get();
                $orderItemData = [];
                //Validate product Attributes
                $orderItemAttribute = [];
                if(count($productAttributes)){
                    $attributeAddPrice = [];
                    if($requiredAttr = $productAttributes->where('required','=',1)){
                        foreach($requiredAttr as $onerequiredAttr){
                            if(!in_array($onerequiredAttr->attribute_id,array_keys($oneProduct['attribute']))){
                                //Required Attribute not provided, exit here
                                return false;
                            }
                        }
                    }

                    //Validate the chosen attributes
                    foreach($oneProduct['attribute'] as $attID => $AttVal){
                        if(is_array($AttVal['val']))
                            $AttVal['val'] = end($AttVal['val']);
                        if(!in_array($productAttributes->where('attribute_id',$attID)->first()->attribute->type,['text','textarea'])){
                            if(!$attrValue = AttributeValue::where('attribute_id','=',$attID)->where('text_'.$lang,'=',$AttVal['val'])->first()){
                                //Value inserted not from our attribute values
                                return false;
                            } else {
                                if($plusPrice = $productAttributes->where('selected_attribute_value',$attrValue->id)->first()->plus_price){
                                    $attributeAddPrice[] = $plusPrice;
                                }
                            }
                        }

                        $orderItemAttribute[] = [
                            'attribute_id'  => $attID,
                            'attribute_value'=> $attrValue->id,
                            'attribute_data'=> $AttVal['val'],
                        ];
                    }
                    //All prices Added, Lets add product price
                    $attributeAddPrice[] = $productData->product_price();
                    $productPrice = array_sum($attributeAddPrice);
                    $totalArr[] = $productPrice * $oneProduct['qty'];
                } else {
                    $productPrice = $productData->product_price();
                    $totalArr[] = $productPrice * $oneProduct['qty'];
                }

                //Add to Order total
                $Order->increment('total',$productPrice * $oneProduct['qty']);

                // Insert Product
                $oneorederitem = [
                    'order_id'              => $Order->id,
                    'merchant_product_id'   => $productData->id,
                    'qty'                   => $oneProduct['qty'],
                    'price'                 => $productPrice,
                ];
                $OrderItem = $Order->orderitems()->create($oneorederitem);


                if(isset($orderItemAttribute) && count($orderItemAttribute)) {
                    //insert attributes if any
                    foreach ($orderItemAttribute as $oneorderitemAttribute) {
                        $OrderItem->orderItemAttribute()->create($oneorderitemAttribute);
                    }
                }

            }

            if(array_sum($totalArr) != array_sum([recursiveFind($RequestData['users'],'amount')])){
                return false;
            }


            if(isset($RequestData['users'])){
                foreach($RequestData['users'] as $user){
                    if($walletid = User::where('id',$user['id'])->first()->wallet->id) {
                        $Order->trans()->create([
                            'amount'            => $user['amount'],
                            'from_id'           => $walletid,//wallet id
                            'to_id'             => $request->user()->merchant->wallet->id,//wallet id
                            'type'              => $user['paytype'],
                            'status'            => 'unpaid',
                        ]);
                    }
                }
            }
            $GLOBALS['status'] = true;
        });

        if(!$GLOBALS['status'])
            return $this->setStatusCode(403)->respondWithoutError(false,__('Couldn\'t add order'));

        $row = Order::where('id',$order->id)->with('trans')->with('orderitems')->with('merchant_branch')->first();
        return $this->respondCreated($this->Transformer->transform($row->toArray(),$this->systemLang),__('Order successfully created'));

    }

    public function view($id){
        $merchantStaff = Auth::user();
        $row = Order::wherein('merchant_branch_id',$merchantStaff->staff_branches())->where('id',$id)
            ->with('trans')->with(['orderitems'=>function($sql){
                $sql->with('orderItemAttribute');
            },'merchant_branch'])
            ->get(['id','merchant_branch_id','creatable_id','creatable_type','comment','commission','commission_type','coupon','total','is_paid','created_at'])
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('This Order doesn\'t exist'));
        else
            return $this->respondSuccess($this->Transformer->transform($row->toArray(),$this->systemLang),__('Order Information'));
    }

    public function edit($id,Request $request){
        $merchantStaff = Auth::user();
        $row = Order::whereIn('merchant_branch_id',$merchantStaff->staff_branches())->where('id',$id)
            ->get(['id','merchant_branch_id','creatable_id','creatable_type','comment','commission','commission_type','coupon','total','is_paid','created_at'])
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('Order not found'));

        $RequestData = $this->headerdata(['merchant_branch_id','items','transactions']);


        $validator = Validator::make($RequestData, [
            'merchant_branch_id'        => 'required|numeric|in:'.implode(',',$merchantStaff->staff_branches()),
            /*
            'users.*'                   => 'required|numeric',
            'paytype.*'                 => 'required|in:wallet,cash',
            'useramount.*'              => 'required|numeric',
            'items.*.merchant_product_id'           => 'required|in:'.implode(',',$merchantproducts),
            'items.*.qty'                           => 'required|numeric',
            */
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $RequestData['merchant_staff_id'] = $merchantStaff->id;


        if(!$row->update($RequestData))
            return $this->setStatusCode(403)->respondWithError(false,__('Order couldn\'t be updated'));

        return $this->respondSuccess($this->Transformer->transform(Order::where('id',$row->id)->first()->toArray(),[$this->systemLang]),__('Order successfully updated'));

    }

    public function delete($id){
        $merchantStaff = Auth::user();
        $row = Order::wherein('merchant_branch_id',$merchantStaff->staff_branches())->where('id',$id)->first();
        if(!$row)
            return $this->respondNotFound(false,__('Order not found'));

        if(!$row->delete())
            return $this->setStatusCode(403)->respondWithError(false,__('Order couldn\'t be deleted'));

        return $this->respondSuccess(false,__('Order successfully deleted'));

    }


    public function edit_order_item($order,$id){
        $merchantStaff = Auth::user();

        $row = OrderItem::select(['order_items.*'])
            ->where('order_items.id','=',$id)->where('order_items.order_id',$order)
            ->join('orders','orders.id','=','order_items.order_id')
            ->wherein('orders.merchant_branch_id',$merchantStaff->staff_branches())
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('Order item not found'));

        $RequestData = $this->headerdata(['merchant_product_id','qty']);
        $merchantproducts = $merchantStaff->merchant->merchant_products->pluck('id')->toArray();
        $validator = Validator::make($RequestData, [
            'merchant_product_id'           => 'required|in:'.implode(',',$merchantproducts),
            'qty'                           => 'required|numeric',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $RequestData['price'] = (MerchantProduct::select(['price'])->where('id','=',$RequestData['merchant_product_id'])->first())->product_price();

        if(!$row->update($RequestData))
            return $this->setStatusCode(403)->respondWithError(false,__('Order item couldn\'t be updated'));

        $Order = Order::where('id',$row->order_id)->with(['orderitems','trans'])->first();
        return $this->respondSuccess($this->Transformer->transform($Order->toArray(),[$this->systemLang]),__('Order item successfully updated'));

    }

    public function delete_order_item($order,$id){
        $merchantStaff = Auth::user();
        $row = OrderItem::select(['order_items.*'])
            ->where('order_items.id','=',$id)->where('order_id',$order)
            ->join('orders','orders.id','=','order_items.order_id')
            ->wherein('orders.merchant_branch_id',$merchantStaff->staff_branches())
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('Order item not found'));

        if(!$row->delete())
            return $this->setStatusCode(403)->respondWithError(false,__('Order item Couldn\'t be deleted'));

        return $this->respondSuccess(false,__('Order item has been deleted successfully'));

    }

    public function add_order_item($order,Request $request){
        $merchantStaff = Auth::user();
        $row = Order::wherein('merchant_branch_id',$merchantStaff->staff_branches())->where('id',$order)
            ->get(['id'])->first();
        if(!$row)
            return $this->respondNotFound(false,__('No order found to add items to'));

        $merchantproducts = $merchantStaff->merchant->merchant_products->pluck('id')->toArray();
        $RequestData = $this->headerdata(['merchant_product_id','qty']);
        $validator = Validator::make($RequestData, [
            'merchant_product_id'           => 'required|in:'.implode(',',$merchantproducts),
            'qty'                           => 'required|numeric',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $RequestData['price'] = (MerchantProduct::select(['price'])->where('id','=',$RequestData['merchant_product_id'])->first())->product_price();
        $RequestData['order_id'] = $order;

        if(!$item=$row->orderitems()->create($RequestData))
            return $this->setStatusCode(403)->respondWithoutError(false,__('Order item Couldn\'t be Added'));

        return $this->respondCreated((new OrderitemTransformer())->transform($item,$this->systemLang),__('Order item has been Added successfully'));

    }

    public function add_bulk_items($order){
        $merchantStaff = Auth::user();
        $row = Order::wherein('merchant_branch_id',$merchantStaff->staff_branches())->where('id',$order)
            ->get(['id'])->first();
        if(!$row)
            return $this->respondNotFound(false,__('No order found to add items to'));

        $merchantproducts = $merchantStaff->merchant->merchant_products->pluck('id')->toArray();
        $RequestData = $this->headerdata(['data']);

        $validator = Validator::make($RequestData, [
            'data.*.merchant_product_id'           => 'required|in:'.implode(',',$merchantproducts),
            'data.*.qty'                           => 'required|numeric',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $items = array_map(function($item)use($order){
            return [
                'merchant_product_id'   => $item['merchant_product_id'],
                'qty'                   => $item['qty'],
                'price'                 => (MerchantProduct::select(['price'])->where('id','=',$item['merchant_product_id'])->first())->product_price(),
                'order_id'              => $order
            ];
        },$RequestData['data']);

        $status = false;
        foreach($items as $oneitem){
            if($row->orderitems()->create($oneitem))
                $status = true;
        }

        if(!$status)
            return $this->setStatusCode(403)->respondWithoutError(false,__('Order item Couldn\'t be Added'));

        return $this->respondCreated((new OrderitemTransformer())->transform($row->toArray(),$this->systemLang),__('Order item has been Added successfully'));


    }

    /*
     * Transactions
     */
    public function edit_order_transaction($order,$id,Request $request){
        $merchantStaff = Auth::user();

        $row = Transaction::select(['transactions.*'])
            ->where('transactions.id','=',$id)->where('transactions.model_id',$order)
            ->leftJoin('orders','orders.id','=','transactions.model_id')
            ->wherein('orders.merchant_branch_id',$merchantStaff->staff_branches())
            /*
             * Make sure transaction is not paid already
             */
            ->where('status','unpaid')
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('No Transaction found'));

        $RequestData = $this->headerdata(['amount','from','type','status']);
        $validator = Validator::make($RequestData, [
            'amount'                => 'required',
            'from'                  => 'required|digits:11|exists:users,mobile',
            'type'                  => 'required',
            'status'                => 'required|in:paid,unpaid',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $RequestData['from_id'] = User::where('mobile','=',$RequestData['from'])->first()->wallet->id;
        unset($RequestData['from_id']);

        if(!$row->update($RequestData))
            return $this->setStatusCode(200)->respondWithError(false,__('Order Transaction has been updated'));

        return $this->respondSuccess((new TransactionTransformer())->transform(Transaction::where('id',$row->id)->first(),[$this->systemLang]),__('Order Transaction didn\'t not updated'));
    }

    public function delete_order_transaction($order,$id){
        $merchantStaff = Auth::user();
        $row = Transaction::select(['transactions.*'])
            ->where('transactions.id','=',$id)->where('transactions.model_id',$order)
            ->leftJoin('orders','orders.id','=','transactions.model_id')
            ->wherein('orders.merchant_branch_id',$merchantStaff->staff_branches())
            /*
             * Make sure transaction is not paid already
             */
            ->where('status','unpaid')
            ->first();
        if(!$row)
            return $this->respondNotFound(false,__('No Transaction found'));

        if(!$row->delete())
            return $this->respondWithError(false,__('Transaction couldn\'t be deleted'));

        return $this->respondSuccess(false,__('Transaction successfully deleted'));
    }

    public function add_order_transaction($order,Request $request){
        $merchantStaff = Auth::user();
        $row = Order::wherein('merchant_branch_id',$merchantStaff->staff_branches())->where('id',$order)
            ->get(['id'])->first();
        if(!$row)
            return $this->respondNotFound(false,__('No order found to add transaction to it'));

        $RequestData = $this->headerdata(['amount','from_id','type','status']);
        $validator = Validator::make($RequestData, [
            'amount'                => 'required',
            'from_id'               => 'required|numeric',
            'type'                  => 'required|in:wallet,cash',
            'status'                => 'required|in:paid,unpaid',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        if($Transaction = $row->trans()->create($RequestData))
            return $this->respondCreated((new TransactionTransformer())->transform($Transaction,[$this->systemLang]),__('Order Transaction has been Added successfully'));
        else
            return $this->respondWithError(false,__('Order Transaction Couldn\'t be Added'));
    }

    public function add_bulk_transactions($order,Request $request){
        $merchantStaff = Auth::user();
        $row = Order::wherein('merchant_branch_id',$merchantStaff->staff_branches())->where('id',$order)
            ->get(['id'])->first();
        if(!$row)
            return $this->respondNotFound(false,__('No order found to add transactions to it'));

        $RequestData = $this->headerdata(['data']);
        $validator = Validator::make($RequestData, [
            'data.*.amount'                => 'required',
            'data.*.from_id'               => 'required|numeric|exists:users,id',
            'data.*.type'                  => 'required|in:wallet,cash',
            'data.*.status'                => 'required|in:paid,unpaid',
        ]);
        if($validator->errors()->any()){
            return $this->ValidationError($validator,__('Validation Error'));
        }

        $items = array_map(function($item)use($order){
            return [
                'amount'                    => $item['amount'],
                'from_id'                   => $item['from_id'],
                'type'                      => $item['type'],
                'status'                    => $item['status'],
            ];
        },$RequestData['data']);

        $status = false;
        foreach($items as $oneitem){
            if($row->orderitems()->create($oneitem))
                $status = true;
        }

        try {
            $Transaction = $row->trans()->create($RequestData);
            return $this->respondCreated((new TransactionTransformer())->transform($Transaction,[$this->systemLang]),__('Order Transaction has been Added successfully'));
        } catch (QueryException $e){
            return $this->respondWithError(false,__('Order Transaction Couldn\'t be Added'));
        }

    }



    public function qrcode($order){
        $user_branches = array_flatten(((Auth()->user()->merchant->merchant_staff_group->first()->id != Auth()->user()->merchant_staff_group_id)?array_filter(explode(',',Auth()->user()->branches)):Auth()->user()->merchant->merchant_branch()->get(['id'])->toArray()));

        $Order = Order::where('id','=',$order)
            ->whereIn('merchant_branch_id',$user_branches)
            ->first();
        if($Order->qr_code)
            return $this->respondWithoutError(['qrcode'=>$Order->qr_code],__('qrcode Generated'));
        do {
            $qrcode = UniqueId();
        } while (Order::where('qr_code','=',$qrcode)->where('id','!=',$order)->first());

        try {
            $Order->update(['qr_code' => $qrcode]);
            return $this->respondWithoutError(['qrcode'=>$Order->$qrcode],__('qrcode Generated'));
        } catch (QueryException $e){
            return $this->setCode(101)->respondWithError(['qrcode'=>$Order->$qrcode],__('qrcode Generated'));
        }


    }

}