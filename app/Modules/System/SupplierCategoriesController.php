<?php

namespace App\Modules\System;

use App\Models\ItemCategories;
use App\Models\SupplierCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class SupplierCategoriesController extends SystemController
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
            $eloquentData = SupplierCategories::select([
                'supplier_categories.id',
                'supplier_categories.name',
                'supplier_categories.description',
                'supplier_categories.status',
                'supplier_categories.staff_id',
                'supplier_categories.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'supplier_categories.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(item_categories.created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('supplier_categories.id', '=', $request->id);
            }
            if ($request->staff_id) {
                $eloquentData->where('supplier_categories.staff_id', '=', $request->staff_id);
            }

            if ($request->name) {
                $eloquentData->where('supplier_categories.name', 'LIKE', '%' . $request->name . '%');
            }

            if ($request->status) {
                $eloquentData->where('supplier_categories.status', '=', $request->status);
            }
            if ($request->description) {
                $eloquentData->where('supplier_categories.description', 'LIKE', '%' . $request->description . '%');
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('description',function ($data){
                    return str_limit($data->description,25);
                })
                ->addColumn('status', '{{$status}}')
                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:ia');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.supplier-category.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.supplier-category.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.supplier-category.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('Description'), __('Status'), __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Supplier Category')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Supplier Categories');
            } else {
                $this->viewData['pageTitle'] = __('Supplier Categories');
            }

            return $this->view('supplier-category.index', $this->viewData);
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
            'text' => __('Supplier Category'),
            'url' => route('system.supplier-category.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Supplier Category'),
        ];

        $this->viewData['pageTitle'] = __('Create Supplier Category');
        return $this->view('supplier-category.create', $this->viewData);

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
           'name' =>'required',
          // 'description' =>'required',
           'status' =>'required',
        ]);
        $theRequest = [];
        $theRequest = $request->only([
            'name',
            'description',
            'status',
        ]);


        $theRequest['staff_id'] = Auth::id();
        $marketingMessage = SupplierCategories::create($theRequest);
        if ($marketingMessage)
            return redirect()
                ->route('system.supplier-category.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.supplier-category.create')
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

    public function show(SupplierCategories $supplier_category)
    {
        //dd($supplier_category);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Supplier Category'),
                'url' => route('system.supplier-category.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Supplier Category';
        $this->viewData['result'] = $supplier_category;

        return $this->view('supplier-category.show', $this->viewData);
    }

    public function edit(SupplierCategories $supplier_category)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Supplier Category'),
            'url' => route('system.supplier-category.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Supplier Category'),
        ];


        $this->viewData['pageTitle'] = __('Edit Supplier Category');
        $this->viewData['result'] = $supplier_category;

        return $this->view('supplier-category.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,SupplierCategories $supplier_category)
    {
        $this->validate($request,[
            'name' =>'required',
         //   'description' =>'required',
            'status' =>'required',
        ]);
        $theRequest = $request->only([
            'name',
            'description',
            'status',
        ]);
        if ($supplier_category->update($theRequest)) {
          //  dd($category);
            return redirect()
                ->route('system.supplier-category.edit', $supplier_category->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Marketing Message'));
        }
        else {
            return redirect()
                ->route('system.supplier-category.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Marketing Message'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,SupplierCategories $supplier_category)
    {
        $supplier_category->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Item Category has been deleted successfully')];
        } else {
            redirect()
                ->route('system.supplier-category.index')
                ->with('status', 'success')
                ->with('msg', __('This Item Category has been deleted'));
        }
    }
}
