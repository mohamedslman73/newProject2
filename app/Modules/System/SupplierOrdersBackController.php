<?php

namespace App\Modules\System;

use App\Models\Item;
use App\Models\SupplierOrderBack;
use App\Models\SupplierOrderBackItem;
use App\Models\SupplierOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class SupplierOrdersBackController extends SystemController
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
            $eloquentData = SupplierOrderBack::select([
                'id',
                'supplier_id',
                'supplier_order_id',
                'date',
                'total_price',
                'notes',
                'staff_id',
                'created_at',
            ]);


            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(date)', $request->date1, $request->date2);
            whereBetween($eloquentData, 'total_price', $request->total_price1, $request->total_price2);

            if ($request->id) {
                $eloquentData->where('supplier_order_back.id', '=', $request->id);
            }
            if ($request->supplier_id) {
                $eloquentData->where('supplier_order_back.supplier_id', '=', $request->supplier_id);
            }

            if ($request->staff_id) {
                $eloquentData->where('staff_id', '=', $request->staff_id);
            }
            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('supplier_id',function ($data){
                   // return $data->supplier->name;
                    return "<a target='_blank' href=\"" . route('system.supplier.show', $data->supplier->id) . "\">".$data->supplier->name."</a>";
                })
                ->addColumn('date', '{{$date}}')
                ->addColumn('total_price', function ($data){
                    return amount($data->total_price,true);
                })
                ->addColumn('staff_id', function($data){
                    return '<a href="'.route("system.staff.show",$data->staff_id).'" target="_blank">'.$data->staff->Fullname.'</a>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:ia');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.supplier-order-back.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.supplier-order-back.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
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
                __('Total Price'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Supplier Orders Back')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Supplier Orders Back');
            } else {
                $this->viewData['pageTitle'] = __('Supplier Orders Back');
            }



            return $this->view('supplier-order-back.index', $this->viewData);
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
        return $this->view('supplier-order-back.create', $this->viewData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

      //  dd($request->all());
        $this->validate($request,[
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
        $supplierOrderData = $request->only(['supplier_id','total','date','supplier_order_id','total','note']);

        $handleItemsCount = [];
        foreach ($request->item_id as $key=>$row){
            $handleItemsCount[$row] = $request->count[$key];
        }
        foreach ($request->item_id as $key=>$row){
            $item = Item::find($row);
            if($item->count < $handleItemsCount[$row]){
                return redirect()
                    ->route('system.client-orders.create')
                    ->with('status', 'danger')
                    ->with('msg', __('Sorry Count Of '.$item->name.' Not Enough'));
            }
        }
        $supplierOrderData['staff_id']  = Auth::id();
        $supplierOrder = SupplierOrderBack::create($supplierOrderData);

        if ($supplierOrder) {
            $sumTotal =0;
            $items = [];
            foreach ($request->item_id as $key=>$value){
                $items['item_id'] = $value;
                $items['supplier_order_back_id'] = $supplierOrder->id;
                $items['count'] = $request->count[$key];
                $items['price'] = $request->price[$key];
                $sumTotal += $request->count[$key] *$request->price[$key];
             $order_item =   SupplierOrderBackItem::create($items);

               $order_item->item->update(['count'=>$order_item->item->count - $request->count[$key]]);
            }
        $supplierOrder->update(['total_price'=>$sumTotal]);
            return redirect()
                ->route('system.supplier-order-back.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        } else {
            return redirect()
                ->route('system.supplier-order-back.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Supplier Order Back'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(SupplierOrderBack $supplierOrderBack , Request $request)
    {
      //  dd($supplierOrderBack);

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Supplier Order Back'),
                'url' => route('system.supplier-order-back.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];


        if ($request->isDataTable) {
            $eloquentData = SupplierOrderBackItem::select([
                'id',
                'supplier_order_back_id',
                'item_id',
                'count',
                'price',
                'created_at',
            ])->where('supplier_order_back_id','=',$supplierOrderBack->id);
            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('item',function ($data){
                 //   return $data->item->name;
                    return "<a target='_blank' href=\"" . route('system.item.show', $data->item->id) . "\">" . $data->item->name . "</a>";
                })
                ->addColumn('count', '{{$count}}')

                ->addColumn('price', '{{$price}}')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:iA');
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



            $this->viewData['pageTitle'] = __('Supplier Order Back');
            $this->viewData['result'] = $supplierOrderBack;
            return $this->view('supplier-order-back.show', $this->viewData);
        }
    }

    public function edit(SupplierOrders $order)
    {
        return back();
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
        exit();
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
    public function destroy(Request $request,SupplierOrderBack $supplierOrderBack)
    {
        $supplierOrderBack->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Supplier Order Back has been deleted successfully')];
        } else {
            redirect()
                ->route('system.supplier-order-back.index')
                ->with('status', 'success')
                ->with('msg', __('This Supplier Order Back has been deleted'));
        }
    }
}
