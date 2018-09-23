<?php

namespace App\Modules\System;

use App\Models\Brand;
use App\Models\ClientTypes;
use App\Models\ItemCategories;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class BrandController extends SystemController
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
            $eloquentData = Brand::select([
                'brands.id',
                'brands.name',
                'brands.description',
                'brands.staff_id',
                'brands.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'brands.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(brands.created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('brands.id', '=', $request->id);
            }
            if ($request->staff_id) {
                $eloquentData->where('brands.staff_id', '=', $request->staff_id);
            }

            if ($request->name) {
                $eloquentData->where('brands.name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->description) {
                $eloquentData->where('brands.description', 'LIKE','%'. $request->description. '%');
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('description',function ($data){
                    if ($data->description){
                        return str_limit($data->description,25);
                    }
                    return '--';
                })
                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.brand.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.brand.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.brand.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('Description'),  __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Brands')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Brands');
            } else {
                $this->viewData['pageTitle'] = __('Brand');
            }

            return $this->view('brand.index', $this->viewData);
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
            'text' => __('Brand'),
            'url' => route('system.brand.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Brand'),
        ];

        $this->viewData['pageTitle'] = __('Create Brand');
        return $this->view('brand.create', $this->viewData);

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
        ]);
        $theRequest = [];
        $theRequest = $request->only([
            'name',
            'description',
        ]);


        $theRequest['staff_id'] = Auth::id();
        $brand = Brand::create($theRequest);
        if ($brand)
            return redirect()
                ->route('system.brand.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.brand.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Brand'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Brand $brand)
    {
        //dd($supplier_category);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Brand'),
                'url' => route('system.brand.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Brand';
        $this->viewData['result'] = $brand;
        return $this->view('brand.show', $this->viewData);
    }

    public function edit(Brand $brand)
    {

        $this->viewData['breadcrumb'][] = [
            'text' => __('Brand'),
            'url' => route('system.brand.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Brand'),
        ];


        $this->viewData['pageTitle'] = __('Edit Brand ');
        $this->viewData['result'] = $brand;

        return $this->view('brand.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Brand $brand)
    {
        ;
        $this->validate($request,[
            'name' =>'required',
        ]);
        $theRequest = $request->only([
            'name',
            'description',
        ]);
        if ($brand->update($theRequest)) {

            return redirect()
                ->route('system.brand.edit', $brand->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Brand'));
        }
        else {
            return redirect()
                ->route('system.types.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Brand'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Brand $brand)
    {
        $brand->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Brand has been deleted successfully')];
        } else {
            redirect()
                ->route('system.brand.index')
                ->with('status', 'success')
                ->with('msg', __('This Brand has been deleted'));
        }
    }
}
