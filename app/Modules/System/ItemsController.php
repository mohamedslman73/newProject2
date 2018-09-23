<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;
use App\Models\ClientOrderItems;
use App\Models\Item;
use App\Models\ItemCategories;
use App\Models\Supplier;
use App\Models\SupplierOrderItems;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;

class ItemsController extends SystemController
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
            $eloquentData = Item::select([
                'items.id',
                'items.name',
                'items.description',
                'items.status',
                'items.staff_id',
                'items.count',
                'items.min_count',
                'items.item_category_id',
                'items.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'items.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(items.created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('items.id', '=', $request->id);
            }

            if ($request->name) {
                $eloquentData->where('items.name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->staff_id) {
                $eloquentData->where('items.staff_id', '=', $request->staff_id);
            }if ($request->item_category_id) {
                $eloquentData->where('items.item_category_id', '=', $request->item_category_id);
            }
            if ($request->status) {
                $eloquentData->where('items.status', '=', $request->status);
            }
            if ($request->description) {
                $eloquentData->where('items.description', 'LIKE', '%' . $request->description . '%');
            }


            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('category',function ($data){
                   return $data->item_categories->name;
                })
//                ->addColumn('status', '{{$status}}')
                ->addColumn('count', '{{$count}}')
                ->addColumn('min_count', '{{$min_count}}')
//                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:iA');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.item.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.item.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.item.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->addColumn('status',function($data){
                    if($data->status == 'in-active'){
                        return 'tr-danger';
                    }
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'),
                __('Name'),
                __('Category'),
                __('Count'),
                __('Min Count'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Item Category')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Items');
            } else {
                $this->viewData['pageTitle'] = __('Items');
            }

            return $this->view('items.index', $this->viewData);
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
            'text' => __('Item'),
            'url' => route('system.item.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Item'),
        ];
        $return = [];
        $data = ItemCategories::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['item_categories'] = $return;
        $this->viewData['pageTitle'] = __('Create Item');
        return $this->view('items.create', $this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request,[
           'name'                    =>'required',
          // 'description'             =>'required',
          'status'                   =>  'required|in:active,in-active',
           'code'                    =>'required|unique:items,code',
           'item_category_id'        =>'required|exists:item_categories,id',
           'unite'                   =>'required',
            'image'                  => 'nullable|image',
           'price'                   =>'required',
         //  'count'                   =>'required',
           'min_count'               =>'required',
        ]);

        $theRequest = $request->only([
            'name',
            'description',
            'status',
            'code',
            'item_category_id',
            'unite',
            'image',
            'price',
            'count',
            'min_count',
        ]);
        if ($request->hasFile('image')) {
            $theRequest['image'] = $request->file('image')
                ->store('items/'.date('y').'/'.date('m'));
        }

        $theRequest['staff_id'] = Auth::id();
        $item = Item::create($theRequest);
        if ($item)
            return redirect()
                ->route('system.item.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.item.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Item category'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Item $item)
    {
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Items'),
                'url' => route('system.item.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//

        $this->viewData['supplierOrdersCount'] = count(SupplierOrderItems::where('item_id',$item->id)->groupBy('supplier_order_id')->get());
        $this->viewData['clientOrderCount'] = count(ClientOrderItems::where('item_id',$item->id)->groupBy('client_order_id')->get());
        $this->viewData['supplierCount'] =     Item::where('items.id',$item->id)
           ->join('supplier_order_items','supplier_order_items.item_id','=','items.id')
           ->join('supplier_order','supplier_order.id','=','supplier_order_items.supplier_order_id')
           ->join('suppliers','suppliers.id','=','supplier_order.supplier_id')
           ->distinct()->count('suppliers.id');

        $this->viewData['projectCount'] = Item::where('items.id',$item->id)
            ->join('client_order_items','client_order_items.item_id','=','items.id')
            ->join('client_orders','client_orders.id','=','client_order_items.client_order_id')
            ->join('projects','projects.id','=','client_orders.project_id')
            ->distinct()->count('projects.id');
        $this->viewData['pageTitle'] = 'Items';
        $this->viewData['result'] = $item;
        return $this->view('items.show', $this->viewData);
    }

    public function edit(Item $item)
    {
       // dd($item->item_categories->id);
        $this->viewData['breadcrumb'][] = [
            'text' => __('Item'),
            'url' => route('system.category.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Item Category'),
        ];
        $return = [];
        $data = ItemCategories::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['item_categories'] = $return;

        $this->viewData['pageTitle'] = __('Edit Item');
        $this->viewData['result'] = $item;

        return $this->view('items.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Item $item)
    {

        $this->validate($request,[
            'name'                    =>'required',
           // 'description'             =>'required',
            'status'                   =>  'required|in:active,in-active',
            'code'                   => 'unique:items,code' . iif($request->id, ',' . $item->id) ,
            'item_category_id'        =>'required|exists:item_categories,id',
            'unite'                   =>'required',
            'image'                  => 'nullable|image',
            'price'                   =>'required',
         //   'count'                   =>'required',
            'min_count'               =>'required',
        ]);

        $theRequest = $request->only([
            'name',
            'description',
            'status',
            'code',
            'item_category_id',
            'unite',
            'image',
            'price',
            'count',
            'min_count',
        ]);

        if ($request->hasFile('image')) {
            $theRequest['image'] = $request->file('image')
                ->store('items/'.date('y').'/'.date('m'));
        }else{
            unset($theRequest['image']);
        }

        if ($item->update($theRequest)) {
          //  dd($category);
            return redirect()
                ->route('system.item.edit', $item->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Marketing Message'));
        }
        else {
            return redirect()
                ->route('system.item.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Marketing Message'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Item $item)
    {
        $item->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Item  has been deleted successfully')];
        } else {
            redirect()
                ->route('system.category.index')
                ->with('status', 'success')
                ->with('msg', __('This Item  has been deleted'));
        }
    }



    public function report()
    {
        $this->viewData['pageTitle'] = 'Summery Report Of Items';
        $this->viewData['breadcrumb'][] = [
            'text' => __('Report'),
            'url' => route('system.item.report')
        ];

        $this->viewData['result'] = AllCategoriesWithItems();
        return $this->view('items.report', $this->viewData);
    }




}
