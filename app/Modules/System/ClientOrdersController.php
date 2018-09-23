<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;


use App\Models\ClientOrders;
use App\Models\Client;
use App\Models\ClientOrderItems;

use App\Models\Item;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ClientOrdersController extends SystemController
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
            $eloquentData = ClientOrders::select([
                'id',
                'client_id',
                'project_id',
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

            if ($request->type) {
                $eloquentData->where($request->type, '!=', $request->type);
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
                ->addColumn('project_id',function ($data) {
                    if (!empty($data->project_id)) {
                        return "<a target='_blank' href=\"" . route('system.project.show', $data->project->id) . "\">" . $data->project->name . "</a>";
                    }else{
                        return ' -- ';
                    }
                })
                ->addColumn('client_id',function ($data) {
                    if (!empty($data->client_id)) {
                        return "<a target='_blank' href=\"" . route('system.client.show', $data->client->id) . "\">" . $data->client->name . "</a>";
                    }else{
                        return ' -- ';
                    }
                })
                ->addColumn('date', '{{$date}}')
                ->addColumn('items_num',function ($data){
                    return $data->client_order_items->count();
                })

                ->addColumn('total_price', '{{$total_price}}')
                ->addColumn('staff_id', function($data){
                    return '<a href="'.route("system.staff.show",$data->staff_id).'" target="_blank">'.$data->staff->Fullname.'</a>';
                }) ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.client-orders.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.client-orders.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.client-orders.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Project'),
                __('Client'),
                __('Date'),
                __('No OF Items'),
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



            return $this->view('client-orders.index', $this->viewData);
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
        return $this->view('client-orders.create', $this->viewData);

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

            $request->type.'_id'                          =>'required|exists:'.$request->type.'s,id',
            // 'project_id'                          =>'required|exists:projects,id',
            'date'                                 =>'required',
            'item_id'                              =>'array',
            'item_id.*'                            =>'required|exists:items,id',
            'count'                                =>'array',
            'count.*'                              =>'nullable|numeric',
            'price'                                =>'array',
            'price.*'                              =>'nullable|numeric',
        ]);
        $clientOrderData = $request->only(['client_id','project_id','date','minus','plus','total']);


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

        $clientOrderData['staff_id']  = Auth::id();
        $clientOrder = ClientOrders::create($clientOrderData);

        if ($clientOrder) {

            $items = [];
            $sumTotal =0;
            foreach ($request->item_id as $key=>$value){
                $items['item_id'] = $value;
                $items['client_order_id'] = $clientOrder->id;
                $items['count'] = $request->count[$key];
                $items['price'] = $request->price[$key];
                $sumTotal += $request->count[$key]*$request->price[$key];
                $client_item = ClientOrderItems::create($items);

                $client_item->item->update(['count'=>$client_item->item->count - $request->count[$key] ]);
            }
            if ($request->has('minus')){
                $sumTotal -= $request->minus;
            }
            if ($request->has('plus')){
                $sumTotal += $request->plus;
            }

            $clientOrder->update(['total_price'=>$sumTotal]);

            return redirect()
                ->route('system.client-orders.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        } else {
            return redirect()
                ->route('system.client-orders.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Order'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(ClientOrders $clientOrder, Request $request)
    {
        // dd($order);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Client Order'),
                'url' => route('system.client-orders.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];


        if ($request->isItems) {
            $eloquentData = ClientOrderItems::
            where('client_order_id','=',$clientOrder->id);
            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('item',function ($data){
                    return "<a target='_blank' href=\"" . route('system.item.show', $data->item->id) . "\">" . $data->item->name . "</a>";
                })
                ->addColumn('count', '{{$count}}')
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
                __('Price Per Piece'),
                __('Created At')
            ];


            $this->viewData['pageTitle'] = __('Client Order');
            $this->viewData['result'] = $clientOrder;
            return $this->view('client-orders.show', $this->viewData);
        }
    }

    public function edit(ClientOrders $clientOrder)
    {
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
    public function destroy(Request $request,ClientOrders $clientOrder)
    {
        $clientOrder->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Client Order  has been deleted successfully')];
        } else {
            redirect()
                ->route('system.client-orders.index')
                ->with('status', 'success')
                ->with('msg', __('This Client Order  has been deleted'));
        }
    }
}
