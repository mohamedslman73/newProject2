<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;


use App\Models\ClientOrderBack;
use App\Models\ClientOrderBackItem;
use App\Models\ClientOrders;
use App\Models\Client;
use App\Models\ClientOrderItems;

use App\Models\Item;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ClientOrdersBackController extends SystemController
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
            $eloquentData = ClientOrderBack::select([
                'id',
                'client_order_id',
                'client_id',
                'date',
                'total_price',
                'staff_id',
                'created_at',
                ]);
            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }
            whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(date)', $request->date1, $request->date2);
            whereBetween($eloquentData, 'total_price', $request->price1, $request->price2);

            if ($request->id) {
                $eloquentData->where('id', '=', $request->id);
            }

            if ($request->client_order_id) {
                $eloquentData->where('client_order_id', '=', $request->client_order_id);
            }

            if ($request->client_id) {
                $eloquentData->where('client_id', '=', $request->client_id);
            }

            if ($request->project_id) {
                $eloquentData->where('project_id', '=', $request->project_id);
            }

            if ($request->staff_id) {
                $eloquentData->where('staff_id', '=', $request->staff_id);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('client_id',function ($data) {
                    if (!empty($data->client_id)) {
                        return "<a target='_blank' href=\"" . route('system.client.show', $data->client->id) . "\">" . $data->client->name . "</a>";
                    }else{
                        return ' -- ';
                    }
                })
                ->addColumn('date', '{{$date}}')

                ->addColumn('total_price', function ($data){
                    return amount($data->total_price,true);
                })
                ->addColumn('staff_id', function($data){
                    return '<a href="'.route("system.staff.show",$data->staff_id).'" target="_blank">'.$data->staff->Fullname.'</a>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.client-order-back.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.client-order-back.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Client'),
                __('Date'),
                __('Total Price'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Client Orders')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Client Orders');
            } else {
                $this->viewData['pageTitle'] = __('Client Orders');
            }



            return $this->view('client-order-back.index', $this->viewData);
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
            'text' => __('Client Order'),
            'url' => route('system.client-orders.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Client Order'),
        ];

        $this->viewData['pageTitle'] = __('Create Client Order');
        return $this->view('client-order-back.create', $this->viewData);

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

           'client_id'                          =>'required|exists:clients,id',
           'client_order_id'                          =>'required|exists:client_orders,id',
           'date'                                 =>'required',
           'item_id'                              =>'array',
           'item_id.*'                            =>'required|exists:items,id',
           'count'                                =>'array',
           'count.*'                              =>'nullable|numeric',
           'price'                                =>'array',
           'price.*'                              =>'nullable|numeric',
        ]);
        $clientOrderData = $request->only(['client_id','date','client_order_id','total']);


        $clientOrderData['staff_id']  = Auth::id();
        $clientOrder = ClientOrderBack::create($clientOrderData);

        if ($clientOrder) {

            $items = [];
            $sumTotal =0;
            foreach ($request->item_id as $key=>$value){
                $items['item_id'] = $value;
                $items['client_order_back_id'] = $clientOrder->id;
                $items['count'] = $request->count[$key];
                $items['price'] = $request->price[$key];
                $sumTotal += $request->count[$key]*$request->price[$key];
                $client_item = ClientOrderBackItem::create($items);

                $client_item->item->update(['count'=>$client_item->item->count + $request->count[$key] ]);
            }
            $clientOrder->update(['total_price'=>$sumTotal]);
            return redirect()
                ->route('system.client-order-back.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        } else {
            return redirect()
                ->route('system.client-order-back.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Client Order Back'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(ClientOrderBack $clientOrderBack, Request $request)
    {
      //  dd($clientOrderBack->client->name);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Client Order Back'),
                'url' => route('system.client-order-back.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];


        if ($request->isItems) {
            $eloquentData = ClientOrderBackItem::where('client_order_back_id','=',$clientOrderBack->id);
            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('item',function ($data){
                    return "<a target='_blank' href=\"" . route('system.item.show', $data->item->id) . "\">" . $data->item->name . "</a>";
                })
                ->addColumn('count', '{{$count}}')
                ->addColumn('price', function ($data){
                    return amount($data->price,true);
                })
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
                __('Price Per Piece'),
                __('Created At')
            ];



            $this->viewData['pageTitle'] = __('Client Order Back');
            $this->viewData['result'] = $clientOrderBack;
            return $this->view('client-order-back.show', $this->viewData);
        }
    }

    public function edit(ClientOrders $clientOrder)
    {
        return back();
        $this->viewData['breadcrumb'][] = [
            'text' => __('Client Order'),
            'url' => route('system.client-orders.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Client Order'),
        ];

        $this->viewData['pageTitle'] = __('Edit Client Order');
        $this->viewData['result'] = $clientOrder;
        $this->viewData['items'] = $clientOrder->client_order_items;
        if (!empty($clientOrder->client_id) )
            $this->viewData['result']['type'] = 'client';
        else
            $this->viewData['result']['type'] = 'project';


        return $this->view('client-orders.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,ClientOrders $clientOrder)
    {
        exit();
        $this->validate($request,[
            $request->type.'_id'                          =>'required|exists:'.$request->type.'s,id',
            'date'                                 =>'required',
            'item_id'                              =>'array',
            'item_id.*'                            =>'required|exists:items,id',
            'count'                                =>'array',
            'count.*'                              =>'nullable|numeric',
            'price'                                =>'array',
            'price.*'                              =>'nullable|numeric',
        ]);
        $theRequest = $request->all();

        $theRequest['staff_id']  = Auth::id();

        if ($clientOrder->update($theRequest)) {

           $var = array_column ($clientOrder->client_order_items()->get()->toArray(),'item_id');

            $sumTotal =0;
            $items = [];
            $clientOrder->client_order_items()->delete();
                foreach ($request->item_id as $key => $value) {
                    $items['item_id'] = $value;
                    $items['supplier_order_id'] = $clientOrder->id;
                    $items['count'] = $request->count[$key];
                    $items['price'] = $request->price[$key];
                    $sumTotal += $request->count[$key] * $request->price[$key];
                    $clientOrder->client_order_items()->create($items);

                }
                if ($request->has('minus')) {
                    $sumTotal -= $request->minus;
                }
                if ($request->has('plus')) {
                    $sumTotal += $request->plus;
                }

            $clientOrder->update(['total_price' => $sumTotal]);


            return redirect()
                ->route('system.client-orders.edit', $clientOrder->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Client Order'));
        }
        else {
            return redirect()
                ->route('system.client-orders.edit', $clientOrder->id)
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Client Order'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,ClientOrderBack $clientOrderBack)
    {
        $clientOrderBack->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Client Order Back has been deleted successfully')];
        } else {
            redirect()
                ->route('system.client-order-back.index')
                ->with('status', 'success')
                ->with('msg', __('This Client Order Back has been deleted'));
        }
    }
}
