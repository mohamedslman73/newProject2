<?php

namespace App\Modules\System;

use App\Models\ClientTypes;
use App\Models\ItemCategories;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ClientTypesController extends SystemController
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
            $eloquentData = ClientTypes::select([
                'client_types.id',
                'client_types.name',
                'client_types.description',
                'client_types.staff_id',
                'client_types.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'client_types.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(client_types.created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('client_types.id', '=', $request->id);
            }
            if ($request->staff_id) {
                $eloquentData->where('client_types.staff_id', '=', $request->staff_id);
            }

            if ($request->name) {
                $eloquentData->where('client_types.name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->description) {
                $eloquentData->where('client_types.description', 'LIKE','%'. $request->description. '%');
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
                                <li class=\"dropdown-item\"><a href=\"" . route('system.types.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.types.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.types.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('Description'),  __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Client Types')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Client Types');
            } else {
                $this->viewData['pageTitle'] = __('Client Types');
            }

            return $this->view('client-types.index', $this->viewData);
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
            'text' => __('Client Types'),
            'url' => route('system.types.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Client Types'),
        ];

        $this->viewData['pageTitle'] = __('Create Client Types');
        return $this->view('client-types.create', $this->viewData);

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
        $clientTypes = ClientTypes::create($theRequest);
        if ($clientTypes)
            return redirect()
                ->route('system.types.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.types.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Client Type'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(ClientTypes $type)
    {
        //dd($supplier_category);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Client Type'),
                'url' => route('system.types.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Client Type';
        $this->viewData['result'] = $type;
        return $this->view('client-types.show', $this->viewData);
    }

    public function edit(ClientTypes $type)
    {

        $this->viewData['breadcrumb'][] = [
            'text' => __('Client type'),
            'url' => route('system.type.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Client Type'),
        ];


        $this->viewData['pageTitle'] = __('Edit Client Type');
        $this->viewData['result'] = $type;

        return $this->view('client-types.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,ClientTypes $type)
    {
        ;
        $this->validate($request,[
            'name' =>'required',
        ]);
        $theRequest = $request->only([
            'name',
            'description',
        ]);
        if ($type->update($theRequest)) {

            return redirect()
                ->route('system.types.edit', $type->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Client Type'));
        }
        else {
            return redirect()
                ->route('system.types.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Client Type'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,ClientTypes $type)
    {
        $type->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Item Category has been deleted successfully')];
        } else {
            redirect()
                ->route('system.types.index')
                ->with('status', 'success')
                ->with('msg', __('This Client Type has been deleted'));
        }
    }
}
