<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;
use App\Models\Item;
use App\Models\ItemCategories;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Pagination\LengthAwarePaginator;

class SuppliersController extends SystemController
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
            $eloquentData = Supplier::select([
                'suppliers.id',
                'suppliers.name',
                'suppliers.email',
                'suppliers.description',
                'suppliers.status',
                'suppliers.staff_id',
                'suppliers.phone1',
                'suppliers.phone2',
                'suppliers.phone3',
                'suppliers.mobile1',
                'suppliers.mobile2',
                'suppliers.mobile3',
                'suppliers.address',
                'suppliers.company_name',
                'suppliers.created_at',
                'suppliers.supplier_category_id',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'suppliers.staff_id');
           // $eloquentData = Supplier::select('*');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(suppliers.created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('suppliers.id', '=', $request->id);
            }

            if ($request->name) {
                $eloquentData->where('suppliers.name', 'LIKE', '%' . $request->name . '%');
            }

            if ($request->status) {
                $eloquentData->where('suppliers.status', '=', $request->status);
            }
            if ($request->staff_id) {
                $eloquentData->where('suppliers.staff_id', '=', $request->staff_id);
            }
            if ($request->supplier_category_id) {
                //dd($request->supplier_category_id);
                $eloquentData->where('suppliers.supplier_category_id', '=', $request->supplier_category_id);
            }
            if ($request->description) {
                $eloquentData->where('suppliers.description', 'LIKE', '%' . $request->description . '%');
            }
            if ($request->mobile) {
                $eloquentData->where('suppliers.mobile1', '=', $request->mobile)
               ->orWhere('suppliers.mobile2', '=', $request->mobile)
               ->orWhere('suppliers.mobile3', '=', $request->mobile);
            }
            if ($request->phone) {
                $eloquentData->where('suppliers.phone1', '=', $request->phone)
                    ->orWhere('suppliers.phone2', '=', $request->phone)
                    ->orWhere('suppliers.phone3', '=', $request->phone);
            }
            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('supplier_category',function ($data){
                    return '<a target="_blank" href="' . route("system.supplier-category.show", $data->supplier_category_id) . '">' .$data->supplier_categories->name . '</a>';
                })

                ->addColumn('company_name', '{{$company_name}}')
                ->addColumn('email', ' <a href="mailto:{{$email}}">{{$email}}</a>')
                ->addColumn('mobile', '  <a href="tel:{{$mobile1}}">{{$mobile1}}</a>')

                 ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:ia');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.supplier.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.supplier.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.supplier.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
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
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Category'),
               // __('Status'),
                __('Company Name'),
                __('E-mail'),
                __('Mobile'),

            //    __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Suppliers')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Suppliers');
            } else {
                $this->viewData['pageTitle'] = __('Suppliers');
            }
            $return = [];
            $data = SupplierCategories::get(['id', 'name']);
            foreach ($data as $key => $value) {
                $return[$value->id] = $value->name;
            }
          //  dd($return);
            $this->viewData['supplier_categories'] = $return;
            return $this->view('suppliers.index', $this->viewData);
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
            'text' => __('Suppliers'),
            'url' => route('system.supplier.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Supplier'),
        ];
        $return = [];
        $data = SupplierCategories::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }

        $this->viewData['supplier_categories'] = $return;
        $this->viewData['pageTitle'] = __('Create Supplier');
        return $this->view('suppliers.create', $this->viewData);

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
           'name'                        =>'required',
          // 'description'                 =>'required',
           'status'                      =>'required|in:active,in-active',
           'supplier_category_id'        =>'required|exists:supplier_categories,id',
           'company_name'                =>'required',
           'email'                      =>'required|email',
           'address'                     =>'required|min:5',
            'phone1'                      =>'required|numeric',
            'phone2'                      =>'nullable|numeric',
            'phone3'                      =>'nullable|numeric',
            'mobile1'                     =>'required|numeric',
            'mobile2'                     =>'nullable|numeric',
            'mobile3'                     =>'nullable|numeric',
        ]);
        $theRequest = $request->all();
        $theRequest['mobile1'] = $request->mobile1;
        if (!empty($request->mobile2)){
            $theRequest['mobile2'] = $request->mobile2;
        }
        if (!empty($request->mobile3)){
            $theRequest['mobile3'] = $request->mobile3;
        }

        $theRequest['phone1'] = $request->phone1;
        if (!empty($request->phone2)){
            $theRequest['phone2'] = $request->phone2;
        }
        if (!empty($request->phone3)){
            $theRequest['phone3'] = $request->phone3;
        }
            //dd($theRequest);'
        $theRequest['staff_id'] = Auth::id();
        $supplier = Supplier::create($theRequest);
        if ($supplier)
            return redirect()
                ->route('system.supplier.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.supplier.create')
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

    public function show(Supplier $supplier)
    {
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Supplier'),
                'url' => route('system.supplier.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Supplier';
        $this->viewData['result'] = $supplier;
        $this->viewData['total_orders'] = $supplier->supplier_order()->sum('total_price');
        $this->viewData['total_expence'] = $supplier->supplier_expence()->sum('amount');
        $this->viewData['order_count'] = $supplier->supplier_order()->count('id');

        $this->viewData['tableColumns'] = [
            __('ID'),
            __('Name'),
            __('Init Credit'),
            __('Total Order Prices'),
            __('Total Order back Prices'),
            __('Total Slices'),
            __('Credit'),
            __('Action')
        ];

        $this->viewData['orderTableColumns'] = [
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

        $this->viewData['orderBackTableColumns'] = [
            __('ID'),
            __('Supplier'),
            __('Date'),
            __('Total Price'),
            __('Created By'),
            __('Created At'),
            __('Action')];

        $this->viewData['depositsTableColumns'] = [
            __('ID'),
            __('Date'),
            __('Amount'),
            __('Description'),
            __('Expense Causes'),
            __('Created By'),
            __('Created At'),
            __('Action')];


        return $this->view('suppliers.show', $this->viewData);
    }

    public function edit(Supplier $supplier)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Supplier'),
            'url' => route('system.supplier.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Supplier'),
        ];
        $return = [];
        $data = SupplierCategories::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['supplier_categories'] = $return;

        $this->viewData['pageTitle'] = __('Edit Supplier');
        $this->viewData['result'] = $supplier;

        return $this->view('suppliers.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Supplier $supplier)
    {

        $this->validate($request,[
            'name'                        =>'required',
          //  'description'                 =>'required',
            'status'                      =>'required|in:active,in-active',
            'supplier_category_id'        =>'required|exists:supplier_categories,id',
            'company_name'                =>'required',
            'email'                      =>'required|email',
            'address'                     =>'required|min:5',
            'phone1'                      =>'required|numeric',
          //  'phone2'                      =>'numeric',
          //  'phone3'                      =>'numeric',
            'mobile1'                     =>'required|numeric',
          //  'mobile2'                     =>'numeric',
         //   'mobile3'                     =>'numeric',
        ]);
        $theRequest = $request->all();
        $theRequest['mobile1'] = $request->mobile1;
        if (!empty($request->mobile2)){
            $theRequest['mobile2'] = $request->mobile2;
        }
        if (!empty($request->mobile3)){
            $theRequest['mobile3'] = $request->mobile3;
        }

        $theRequest['phone1'] = $request->phone1;
        if (!empty($request->phone2)){
            $theRequest['phone2'] = $request->phone2;
        }
        if (!empty($request->phone3)){
            $theRequest['phone3'] = $request->phone3;
        }

        $theRequest['staff_id'] = Auth::id();
        if ($supplier->update($theRequest)) {
            return redirect()
                ->route('system.supplier.edit', $supplier->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Supplier'));
        }
        else {
            return redirect()
                ->route('system.supplier.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Supplier'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Supplier $supplier)
    {
        $supplier->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Item  has been deleted successfully')];
        } else {
            redirect()
                ->route('system.supplier.index')
                ->with('status', 'success')
                ->with('msg', __('This Supplier  has been deleted'));
        }
    }

    public function supplierReport(Request $request){



        if ($request->isDataTable) {

            $eloquentData = Supplier::select([
                'suppliers.id',
                'suppliers.name',
                'suppliers.init_credit',
                'suppliers.description',
                'suppliers.company_name',

                ])->with([
                    'supplier_order'=>function($q) use ($request){

                    whereBetween($q, 'DATE(supplier_order.created_at)', $request->date1, $request->date2);
                },'supplier_expence'=>function($q) use ($request){
                    whereBetween($q, 'DATE(expenses.created_at)', $request->date1, $request->date2);

                }
               ,'supplier_order_back'=>function($q) use ($request){
                    whereBetween($q, 'DATE(supplier_order_back.created_at)', $request->date1, $request->date2);

                }
                ] );
                //  ->groupBy('suppliers.id');
            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

//            whereBetween($eloquentData, 'total_price', $request->total_order_price1, $request->total_order_price2);
            if ($request->name) {
                $eloquentData->where('suppliers.name', 'LIKE', '%' . $request->name . '%');
            }
          if ($request->supplier_id) {
                $eloquentData->where('suppliers.id', '=',  $request->supplier_id);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', function ($data){
                    return '<a target="_blank" href="' . route("system.supplier.show", $data->id) .'">'.$data->name.'</a>';
                })
                ->addColumn('init_credit', '{{$init_credit}}')
                ->addColumn('total_price', function ($data){
                    return $data->supplier_order()->sum('total_price');
                })

                ->addColumn('description', function ($data){
                    return $data->supplier_order_back()->sum('total_price');
                })
                ->addColumn('total_expenses', function($data){
                    return $data->supplier_expence()->sum('amount');
                })
                ->addColumn('total_e',function ($data){
                    $sum_expence = $data->supplier_expence()->sum('amount');
                    $sum_order = $data->supplier_order()->sum('total_price');
                    $sum_order_back = $data->supplier_order_back()->sum('total_price');
                    $difference = ($sum_order + $data->init_credit ) - ($sum_expence + $sum_order_back);
                    if ($difference >0){
                        return $difference .' For him';
                    }
                    return $difference .' On him';
                })

                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.credit-details', $data->id) . "\">" . __('View') . "</a></li>

                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Init Credit'),
                __('Total Order Prices'),
                __('Total Order back Prices'),
                __('Total Slices'),
                __('Credit'),
                __('Action')
            ];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Suppliers Report')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Suppliers Report');
            } else {
                $this->viewData['pageTitle'] = __('Suppliers Report');
            }
            return $this->view('suppliers.supplier-report', $this->viewData);
        }
    }

    public function supplierCreditDetails(Request $request){

        whereBetween($eloquentData, 'DATE(supplier_order.created_at)', $request->supplier_order_created_at1, $request->supplier_order_created_at2);
        whereBetween($eloquentData, 'DATE(expenses.created)', $request->expense_created_at1, $request->expense_created_at2);

        $eloquentData = Supplier::where('id',$request->id)
            ->with([
                'supplier_order'=>function($q){
            $q->selectRaw("*,'supplier_order' as type ");
        },'supplier_expence'=>function($q){
             $q->selectRaw("*,'supplier_expence' as type ");
        },'supplier_order_back'=>function($q){
                    $q->selectRaw("*,'supplier_order_back' as type ");
                } ])->first()->toArray();

        $array = array_merge($eloquentData['supplier_order'],$eloquentData['supplier_expence']);

        $all = array_merge($array,$eloquentData['supplier_order_back']);



        foreach ($all as $key => $row) {
            $orderByDate[$key] = strtotime($row ['created_at'] );
        }

       array_multisort($orderByDate, SORT_ASC, $all);


        $this->viewData['tableColumns'] = $all;
        $this->viewData['supplier'] = $eloquentData;
        $this->viewData['breadcrumb'][] = [
            'text' => __('Supplier Credit Details')
        ];
        $this->viewData['pageTitle'] = __('Supplier Credit Details');
        return $this->view('suppliers.supplier-credit-details', $this->viewData);


    }


}
