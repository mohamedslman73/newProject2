<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;


use App\Models\Item;
use App\Models\Quotations;
use App\Models\Supplier;
use App\Models\SupplierOrderItems;
use App\Models\SupplierOrders;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class SupplierOrdersController extends SystemController
{
    public function __construct(){
        parent::__construct();
        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Home'),
                'url'=> url('system'),
            ]
        ];
    }
    public function index(Request $request)
    {

        if ($request->isDataTable) {
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
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
            ])
                ->join('staff', 'staff.id', '=', 'supplier_order.staff_id');


            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

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

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('supplier_id',function ($data){
                   // return $data->supplier->name;
                    if ($data->supplier_id) {
                        return "<a target='_blank' href=\"" . route('system.supplier.show', $data->supplier->id) . "\">" . $data->supplier->name . "</a>";
                    }
                    return '--';
                })
                ->addColumn('date', '{{$date}}')
                ->addColumn('items_num',function ($data){
                    return $data->supplier_order_items->count();
                })
                ->addColumn('plus', function ($data){
                    return amount($data->plus,true);
                })
                ->addColumn('minus', function($data){
                    return amount($data->minus,true);
                })
                ->addColumn('total_price', function ($data){
                    return amount($data->total_price,true);
                })
                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:ia');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.order.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.order.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.order.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Supplier'),
                __('Date'),
                __('No OF Items'),
                __('Plus'),
                __('Minus'),
                __('Total Price'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Supplier Orders')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Supplier Orders');
            } else {
                $this->viewData['pageTitle'] = __('Supplier Orders');
            }



            return $this->view('supplier-order.index', $this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text' => __('Supplier Order'),
            'url' => route('system.order.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Supplier Order'),
        ];

        $this->viewData['pageTitle'] = __('Create Supplier Order');
        return $this->view('supplier-order.create', $this->viewData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $this->validate($request,[
           'supplier_id'                          =>'required|exists:suppliers,id',
           'date'                                 =>'required',
           'item_id'                              =>'array',
           'item_id.*'                            =>'required|exists:items,id',
           'count'                                =>'array',
           'count.*'                              =>'nullable|numeric',
           'price'                                =>'array',
           'price.*'                              =>'nullable|numeric',
        ]);
        $supplierOrderData = $request->only(['supplier_id','date','minus','plus','total','note']);

        $supplierOrderData['staff_id']  = Auth::id();
        $supplierOrder = SupplierOrders::create($supplierOrderData);

        if ($supplierOrder) {

            $items = [];
            $sumTotal =0;
            foreach ($request->item_id as $key=>$value){
                $item = Item::find($value);
                $count = $item->count;
                $price = $item->price;
                $newCoutAndPriceForItems = $request->count[$key] *$request->price[$key];
                $allCountAndPriceForThisItem =  $newCoutAndPriceForItems +($count*$price);
                $newCount = $count + $request->count[$key];
                $cost = $allCountAndPriceForThisItem/$newCount;

                $item->update(['cost'=>round($cost,2)]);
                $items['item_id'] = $value;
                $items['supplier_order_id'] = $supplierOrder->id;
                $items['count'] = $request->count[$key];
                $items['price'] = $request->price[$key];
                $sumTotal += $request->count[$key]*$request->price[$key];
             $order_item =   SupplierOrderItems::create($items);

                $order_item->item->update(['count'=>$order_item->item->count+ $request->count[$key]]);
            }
            if ($request->has('minus')){
                $sumTotal -= $request->minus;
            }
            if ($request->has('plus')){
                $sumTotal += $request->plus;
            }

            $supplierOrder->update(['total_price'=>$sumTotal]);

            return redirect()
                ->route('system.order.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        } else {
            return redirect()
                ->route('system.quotations.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add quotations'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(SupplierOrders $order, Request $request)
    {
       // dd($order);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Supplier Order'),
                'url' => route('system.order.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];


        if ($request->isDataTable) {
            $eloquentData = SupplierOrderItems::select([
                'supplier_order_items.id',
                'supplier_order_items.supplier_order_id',
                'supplier_order_items.item_id',
                'supplier_order_items.count',
                'supplier_order_items.price',
                'supplier_order_items.created_at',
            ])->where('supplier_order_id','=',$order->id);
            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('item',function ($data){
                 //   return $data->item->name;
                    return "<a target='_blank' href=\"" . route('system.item.show', $data->item->id) . "\">" . $data->item->name . "</a>";
                })

                //                ->addColumn('supplier_id', function ($data) {
//                    // return $data->supplier->name;
//                    return "<a target='_blank' href=\"" . route('system.supplier.show', $data->supplier->id) . "\">" . $data->supplier->name . "</a>";
//                })
                ->addColumn('count', '{{$count}}')
//                ->addColumn('items_num', function ($data) {
//                    return $data->supplier_order_items->count();
//                })
                ->addColumn('price', '{{$price}}')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->make(true);
        } else {
            // View Data

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Item'),
                __('Count'),
                __('Price For One Piece'),
                __('Created At')
            ];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Supplier Order Items')
            ];


            $this->viewData['pageTitle'] = __('Supplier Order Items');
            $this->viewData['result'] = $order;
            return $this->view('supplier-order.show', $this->viewData);
        }
    }

    public function edit(SupplierOrders $order)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Supplier Order'),
            'url' => route('system.order.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Supplier Order'),
        ];

        $this->viewData['pageTitle'] = __('Edit Supplier Order');
        $this->viewData['result'] = $order;
        $this->viewData['items'] = $order->supplier_order_items;
       // dd( $this->viewData['items']);
       // dd($order->supplier_order_items);
       // dd($this->viewData);
        return $this->view('supplier-order.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,SupplierOrders $order)
    {
        $this->validate($request,[
            'supplier_id'                          =>'required|exists:suppliers,id',
            'date'                                 =>'required',
            'item_id'                              =>'array',
            'item_id.*'                            =>'required|exists:items,id',
            'count'                                =>'array',
            'count.*'                              =>'required|numeric',
            'price'                                =>'array',
            'price.*'                              =>'required|numeric',
        ]);
        $theRequest = $request->all();


            $items_count = [];
        foreach ($request->item_id as $key=>$row){
            $items_count[$row] = $request->count[$key];
        }

        $new_items_count = [];  // to update items table
        foreach ($order->supplier_order_items as $row)
        {
            $new_count = ($row->item->count - $row->count  )  + $items_count[$row->item_id];
            if($new_count < 0 ){
                return redirect()
                    ->route('system.order.edit',$order->id)
                    ->with('status', 'danger')
                    ->with('msg', __('Sorry '.$row->item->name .' Cannot be minus '));

            }else {
                $new_items_count[$row->item_id] = $new_count;
            }
        }






        $theRequest['staff_id']  = Auth::id();

        if ($order->update($theRequest)) {

           $var = array_column ($order->supplier_order_items()->get()->toArray(),'item_id');
            $sumTotal =0;
            $items = [];
                $order->supplier_order_items()->delete();
                foreach ($request->item_id as $key => $value) {
                    $items['item_id'] = $value;
                    $items['supplier_order_id'] = $order->id;
                    $items['count'] = $request->count[$key];
                    $items['price'] = $request->price[$key];
                    $sumTotal += $request->count[$key] * $request->price[$key];
                    $order_item = $order->supplier_order_items()->create($items);
                    //->whereIn('item_id', $var)

                    $order_item->item->update(['count'=>$new_items_count[$value]]);

                }
                if ($request->has('minus')) {
                    $sumTotal -= $request->minus;
                }
                if ($request->has('plus')) {
                    $sumTotal += $request->plus;
                }

                $order->update(['total_price' => $sumTotal]);


            return redirect()
                ->route('system.order.edit', $order->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Supplier Order'));
        }
        else {
            return redirect()
                ->route('system.order.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Supplier Order'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,SupplierOrders $order)
    {
        $order->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Supplier Order  has been deleted successfully')];
        } else {
            redirect()
                ->route('system.order.index')
                ->with('status', 'success')
                ->with('msg', __('This Supplier Order  has been deleted'));
        }
    }
}
