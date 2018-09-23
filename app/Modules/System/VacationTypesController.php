<?php

namespace App\Modules\System;

use App\Models\ItemCategories;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class VacationTypesController extends SystemController
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
            $eloquentData = VacationTypes::select([
                'vacation_types.id',
                'vacation_types.name',
                'vacation_types.after_months',
                'vacation_types.staff_id',
                'vacation_types.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'vacation_types.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(vacation_types.created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('vacation_types.id', '=', $request->id);
            }
            if ($request->staff_id) {
                $eloquentData->where('vacation_types.staff_id', '=', $request->staff_id);
            }

            if ($request->name) {
                $eloquentData->where('vacation_types.name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->after_months) {
                $eloquentData->where('vacation_types.after_months', '=', $request->after_months);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('after_months','{{$after_months}}')
                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:ia');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.type.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.type.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.type.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('After X of Months'),  __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Supplier Category')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Supplier Categories');
            } else {
                $this->viewData['pageTitle'] = __('Supplier Categories');
            }

            return $this->view('vacation-types.index', $this->viewData);
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
            'text' => __('Vacation Types'),
            'url' => route('system.type.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Vacation Type'),
        ];

        $this->viewData['pageTitle'] = __('Create Vacation Type');
        return $this->view('vacation-types.create', $this->viewData);

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
           'after_months' =>'required|numeric',
        ]);
        $theRequest = [];
        $theRequest = $request->only([
            'name',
            'after_months',
        ]);


        $theRequest['staff_id'] = Auth::id();
        $marketingMessage = VacationTypes::create($theRequest);
        if ($marketingMessage)
            return redirect()
                ->route('system.type.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.type.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Vacation Type'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(VacationTypes $type)
    {
        //dd($supplier_category);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Vacation Type'),
                'url' => route('system.type.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Vacation Type';
        $this->viewData['result'] = $type;
        return $this->view('vacation-types.show', $this->viewData);
    }

    public function edit(VacationTypes $type)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Vacation type'),
            'url' => route('system.type.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Vacation Type'),
        ];


        $this->viewData['pageTitle'] = __('Edit Vacation Type');
        $this->viewData['result'] = $type;

        return $this->view('vacation-types.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,VacationTypes $type)
    {
        $this->validate($request,[
            'name' =>'required',
            'after_months' =>'required',
        ]);
        $theRequest = $request->only([
            'name',
            'after_months',
        ]);
        if ($type->update($theRequest)) {
          //  dd($category);
            return redirect()
                ->route('system.type.edit', $type->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Vacation Type'));
        }
        else {
            return redirect()
                ->route('system.type.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Vacation Type'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,VacationTypes $type)
    {
        $type->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Item Category has been deleted successfully')];
        } else {
            redirect()
                ->route('system.type.index')
                ->with('status', 'success')
                ->with('msg', __('This Item Category has been deleted'));
        }
    }
}
