<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientOrderItems;
use App\Models\ClientOrders;
use App\Models\ClientTypes;
use App\Models\Item;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierOrderItems;
use App\Models\SupplierOrders;
use App\Modules\Api\StaffTransformers\ClientOrdersTransformer;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use App\Modules\Api\StaffTransformers\SupplierOrdersTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



class SupplierOrdersApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function supplierOrders(Request $request)
    {
//        if (!staffCan('system.client.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = SupplierOrders::select([
            'supplier_order.id',
            'supplier_order.supplier_id',
            'supplier_order.date',
            'supplier_order.plus',
            'supplier_order.minus',
            'supplier_order.total_price',
            'supplier_order.note',
            'supplier_order.staff_id',
            'supplier_order.created_at',
        ])
        ->with(['supplier'=>function($supplier){
            $supplier->select(['id','name as supplier_name']);
        }]);


        whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'DATE(date)', $request->date1, $request->date2);
        whereBetween($eloquentData, 'total_price', $request->total_price1, $request->total_price2);

        if ($request->id) {
            $eloquentData->where('supplier_order.id', '=', $request->id);
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


        $supplierTransformer = new SupplierOrdersTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Supplier Orders Available'));
            }
                $clients = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

        $supplier = Supplier::get(['id','name']);
        $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $supplierTransformer->staff = $staff;
        $allData = $supplierTransformer->transformCollection($clients->toArray());
        $allData['staff'] = $staff;
        $allData['suppliers'] = $supplier;
        return $this->json(true, __('suppliers'),$allData);

    }
    public function oneSupplierOrder(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $RequestData = $request->only('order_id');
        $validator = Validator::make($RequestData, [
            'order_id' => 'required|exists:supplier_order,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }



        $eloquentData = SupplierOrders::select([
            'supplier_order.id',
            'supplier_order.supplier_id',
            'supplier_order.date',
            'supplier_order.plus',
            'supplier_order.minus',
            'supplier_order.total_price',
            'supplier_order.note',
            'supplier_order.staff_id',
            'supplier_order.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as created_by"),
        ])
            ->join('staff', 'staff.id', '=', 'supplier_order.staff_id')

        ->where('supplier_order.id',$request->order_id)
            ->with(['supplier'=>function($supplier){
                $supplier->select(['id','name as supplier_name']);
            },'supplier_order_items.item'=>function($item) {
                $item->select(['id', 'name']);
            }])
        ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $supplierTransforrmer = new SupplierOrdersTransformer();
        return $this->json(true,__('One Supplier Order'),$supplierTransforrmer->transform($eloquentData));

    }
    public function supplierAndItems()
    {
        $suppliers = Supplier::get(['id','name']);
        $items = Item::get(['id','name']);
        $data=[];
        $data['suppliers'] = $suppliers;
        $data['items'] = $items;
        return $this->json(true,__('Suppliers and Items'),$data);
    }

    public function createSupplierOrder(Request $request)
    {
        $supplierOrderData = $request->only(['supplier_id','date','minus','plus','total','note']);

      $validator=  Validator::make($supplierOrderData,[
          'supplier_id'                          =>'required|exists:suppliers,id',
          'date'                                 =>'required|date',
          'item_id'                              =>'array',
          'item_id.*'                            =>'required|exists:items,id',
          'count'                                =>'array',
          'count.*'                              =>'nullable|numeric',
          'price'                                =>'array',
          'price.*'                              =>'nullable|numeric',
          'plus'                                 =>'nullable|numeric',
          'minus'                                =>'nullable|numeric'
      ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $supplierOrderData['staff_id'] = 1;

        $supplierOrder = SupplierOrders::create($supplierOrderData);

        if ($supplierOrder) {
if ($request->has('item_id')) {
    $items = [];
    $sumTotal = 0;
    foreach ($request->item_id as $key => $value) {
        $item = Item::find($value);
        $count = $item->count;
        $price = $item->price;
        $newCoutAndPriceForItems = $request->count[$key] * $request->price[$key];
        $allCountAndPriceForThisItem = $newCoutAndPriceForItems + ($count * $price);
        $newCount = $count + $request->count[$key];
        $cost = $allCountAndPriceForThisItem / $newCount;

        $item->update(['cost' => round($cost, 2)]);
        $items['item_id'] = $value;
        $items['supplier_order_id'] = $supplierOrder->id;
        $items['count'] = $request->count[$key];
        $items['price'] = $request->price[$key];
        $sumTotal += $request->count[$key] * $request->price[$key];
        $order_item = SupplierOrderItems::create($items);

// $order_item->item->update(['count' => $order_item->item->count + $request->count[$key]]);
    }
    if ($request->has('minus')) {
        $sumTotal -= $request->minus;
    }
    if ($request->has('plus')) {
        $sumTotal += $request->plus;
    }

  // $supplierOrder->update(['total_price' => $sumTotal]);
}

            return $this->respondCreated($supplierOrder);
        } else {
            return $this->json(false,__('can\'t Create Supplier Order'));
        }
    }
    public function updateSupplierOrder(Request $request )
    {

        $theRequest  = $request->only(['supplier_order_id','supplier_id','date','minus','plus','total','note']);


        $validator=  Validator::make($theRequest,[
            'supplier_order_id'                    =>'required|exists:supplier_order,id',
            'supplier_id'                          =>'nullable|exists:suppliers,id',
            'date'                                 =>'nullable|date',
            'item_id'                              =>'array',
            'item_id.*'                            =>'nullable|exists:items,id',
            'count'                                =>'array',
            'count.*'                              =>'nullable|numeric',
            'price'                                =>'array',
            'price.*'                              =>'nullable|numeric',
            'plus'                                 =>'nullable|numeric',
            'minus'                                =>'nullable|numeric'
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $supplierOrder = SupplierOrders::where('id',$request->supplier_order_id)->first();


        $items_count = [];
        if ($request->has('item_id')) {
            foreach ($request->item_id as $key => $row) {
                $items_count[$row] = $request->count[$key];
            }

            $new_items_count = [];  // to update items table
            foreach ($supplierOrder->supplier_order_items as $row) {
                $new_count = ($row->item->count - $row->count) + $items_count[$row->item_id];
                if ($new_count < 0) {
                    return $this->json(false, __('Sorry ' . $row->item->name . ' Can not be minus '));

                } else {
                    $new_items_count[$row->item_id] = $new_count;
                }
            }
        }

        $columnToUpdate =  array_filter($theRequest);
         $updated = $supplierOrder->update($columnToUpdate);

        if ($updated) {
            if ($request->has('item_id')) {

                $var = array_column($supplierOrder->supplier_order_items()->get()->toArray(), 'item_id');
                $sumTotal = 0;
                $items = [];
                $supplierOrder->supplier_order_items()->delete();
                foreach ($request->item_id as $key => $value) {
                    $items['item_id'] = $value;
                    $items['supplier_order_id'] = $supplierOrder->id;
                    $items['count'] = $request->count[$key];
                    $items['price'] = $request->price[$key];
                    $sumTotal += $request->count[$key] * $request->price[$key];
                    $order_item = $supplierOrder->supplier_order_items()->create($items);
                    //->whereIn('item_id', $var)

                    $order_item->item->update(['count' => $new_items_count[$value]]);

                }
                if ($request->has('minus')) {
                    $sumTotal -= $request->minus;
                }
                if ($request->has('plus')) {
                    $sumTotal += $request->plus;
                }

                $supplierOrder->update(['total_price' => $sumTotal]);
            }
            $supplierOrderTransforrmer = new SupplierOrdersTransformer();
            return $this->json(true,__('One Supplier Order Updated'),$supplierOrderTransforrmer->transform($supplierOrder));
        }

        else {
            return $this->json(false,__('Can\'t Update this Supplier Order'));
        }
    }
    public function deleletSuppliertOrder(Request $request){
        $RequestData = $request->only('order_id');
        $validator = Validator::make($RequestData, [
            'order_id' => 'required|exists:supplier_order,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (SupplierOrders::where('id',$request->order_id)->delete())
            return $this->json(true,__('Supplier Order Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }

}