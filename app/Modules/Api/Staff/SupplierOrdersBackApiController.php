<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientOrderBack;
use App\Models\ClientOrderBackItem;
use App\Models\ClientOrderItems;
use App\Models\ClientOrders;
use App\Models\ClientTypes;
use App\Models\Item;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierOrderBack;
use App\Models\SupplierOrderBackItem;
use App\Models\SupplierOrders;
use App\Modules\Api\StaffTransformers\ClientOrdersBackTransformer;
use App\Modules\Api\StaffTransformers\ClientOrdersTransformer;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use App\Modules\Api\StaffTransformers\SupplierOrdersBackTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



class SupplierOrdersBackApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function supplierOrderBack(Request $request)
    {
//        if (!staffCan('system.client.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = SupplierOrderBack::select([
            'id',
            'supplier_id',
            'supplier_order_id',
            'date',
            'total_price',
            'notes',
            'staff_id',
            'created_at',
        ])
        ->with(['supplier'=>function($client){
            $client->select(['id','name as supplier_name']);
        },'staff'=>function($staff){
            $staff->select(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name")]);
        }]);


        whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'DATE(date)', $request->date1, $request->date2);
        whereBetween($eloquentData, 'total_price', $request->total_price1, $request->total_price2);

        if ($request->id) {
            $eloquentData->where('supplier_order_back.id', '=', $request->id);
        }
        if ($request->supplier_id) {
            $eloquentData->where('supplier_order.supplier_id', '=', $request->supplier_id);
        }
        if ($request->minus) {
            $eloquentData->where('supplier_order.minus', '=', $request->minus);
        }
        if ($request->plus) {
            $eloquentData->where('supplier_order.plus', '=', $request->plus);
        }
        if ($request->staff_id) {
            $eloquentData->where('staff_id', '=', $request->staff_id);
        }
        $supplierOrderBackTransformer = new SupplierOrdersBackTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No suppliers Orders Back Available'));
            }
                $supplierOrderBack = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $suppliers = Supplier::get(['id','name']);
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $supplierOrderBackTransformer->staff = $staff;
        $allData = $supplierOrderBackTransformer->transformCollection($supplierOrderBack->toArray());
        $allData['staff'] = $staff;
        $allData['suppliers'] = $suppliers;
        return $this->json(true, __('Suppliers Orders Back'),$allData);

    }
    public function oneSupplierOrderBack(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $RequestData = $request->only('supplier_order_back_id');
        $validator = Validator::make($RequestData, [
            'supplier_order_back_id' => 'required|exists:supplier_order_back,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $eloquentData = SupplierOrderBack::select([
            'id',
            'supplier_id',
            'supplier_order_id',
            'date',
            'total_price',
            'notes',
            'staff_id',
            'created_at',
        ])
            ->with(['supplier'=>function($client){
                $client->select(['id','name as supplier_name']);
            },'staff'=>function($staff){
                $staff->select(['id',DB::Raw("CONCAT(firstname,'',lastname) as staff_name")]);
            }])
            ->where('id',$request->supplier_order_back_id)
            ->first();
       // dd($eloquentData);
        $orderItems = SupplierOrderBackItem::where('supplier_order_back_id',$request->supplier_order_back_id)->get();
        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $supplierOrdersBackTransformer = new SupplierOrdersBackTransformer();
        $allData = $supplierOrdersBackTransformer->transform($eloquentData);
        $allData['orderItems'] = $orderItems;
        return $this->json(true,__('One Client Order Back'),$allData);
    }
    public function deleletSupplierOrderBack(Request $request){
        $RequestData = $request->only('supplier_order_back_id');
        $validator = Validator::make($RequestData, [
            'supplier_order_back_id' => 'required|exists:supplier_order_back,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        if (SupplierOrderBack::where('id','=',$request->supplier_order_back_id)->delete())
            return $this->json(true,__('Supplier Order Back Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }

    public function suppliersAndOrderIdItems()
    {
        $suppliers = Supplier::get(['id','name']);
        $supplier_order_ids = SupplierOrders::get(['id']);
        $items = Item::get(['id','name']);
        $data=[];
        $data['suppliers'] = $suppliers;
        $data['items'] = $items;
        $data['supplier_order_ids'] = $supplier_order_ids;
        return $this->json(true,__('suppliers,Order IDs and Items'),$data);
    }

    public function createSupplierOrderBack(Request $request)
    {
        //dd($request->all());
        $supplierOrderData = $request->only(['supplier_id','total','date','supplier_order_id','total','note']);

        $validator=  Validator::make($supplierOrderData,[
            'supplier_id'                          =>'required|exists:suppliers,id',
            'supplier_order_id'                    =>'required|exists:supplier_order,id',
            'date'                                 =>'required',
            'item_id'                              =>'array',
            'item_id.*'                            =>'required|exists:items,id',
            'count'                                =>'array',
            'count.*'                              =>'nullable|numeric',
            'price'                                =>'array',
            'price.*'                              =>'nullable|numeric',
        ]);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

       // $clientOrderData['staff_id']  = Auth::id();
        $supplierOrderData['staff_id']  = 1;
        $handleItemsCount = [];
        if ($request->has('item_id')) {
            foreach ($request->item_id as $key => $row) {
                $handleItemsCount[$row] = $request->count[$key];
            }
            foreach ($request->item_id as $key => $row) {
                $item = Item::find($row);
                if ($item->count < $handleItemsCount[$row]) {
                    return $this->json(false, __('Sorry Count Of ' . $item->name . ' Not Enough'));
                }
            }
        }

        $supplierOrder = SupplierOrderBack::create($supplierOrderData);

        if ($supplierOrder) {
            $sumTotal =0;
            $items = [];
            if ($request->has('item_id')) {
                foreach ($request->item_id as $key => $value) {
                    $items['item_id'] = $value;
                    $items['supplier_order_back_id'] = $supplierOrder->id;
                    $items['count'] = $request->count[$key];
                    $items['price'] = $request->price[$key];
                    $sumTotal += $request->count[$key] * $request->price[$key];
                    $order_item = SupplierOrderBackItem::create($items);

                    $order_item->item->update(['count' => $order_item->item->count - $request->count[$key]]);
                }
                $supplierOrder->update(['total_price' => $sumTotal]);
            }
            return $this->respondCreated($supplierOrder);
        } else {
            return $this->json(false,__('can\'t Create Supplier Order Back'));
        }
    }

}