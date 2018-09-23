<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;
use App\Models\Bus;
use App\Models\Client;
use App\Models\ClientTypes;
use App\Models\Item;
use App\Models\ItemCategories;
use App\Models\Maintenance;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class MaintenanceController extends SystemController
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
            $eloquentData = Maintenance::select([
                'maintenances.id',
                'maintenances.bus_id',
                'maintenances.maintenance_date',
                'maintenances.price',
                'maintenances.note',
                'maintenances.created_at',
                'maintenances.staff_id',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'maintenances.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(maintenances.created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(maintenances.maintenance_date)', $request->maintenance_date1, $request->maintenance_date2);
            whereBetween($eloquentData, 'maintenances.price', $request->price1, $request->price2);

            if ($request->id) {
                $eloquentData->where('maintenances.id', '=', $request->id);
            }
            if ($request->bus_id) {
                $eloquentData->where('maintenances.bus_id', '=',  $request->bus_id);
            }
            if ($request->note) {
                $eloquentData->where('maintenances.note', 'LIKE', '%'.$request->note.'%');
            }

            if ($request->staff_id) {
                $eloquentData->where('maintenances.staff_id', '=', $request->staff_id);
            }
            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('bus_id', function ($data){
                  //  return $data->bus->bus_number;
                    return "<a target='_blank' href=\"" . route('system.bus.show', $data->bus->id) . "\">".$data->bus->bus_number."</a>";
                })
                ->addColumn('maintenance_date', function ($data){
                    return $data->maintenance_date;
                })
                ->addColumn('price', function ($data){
                    return amount($data->price,true);
                })
                ->addColumn('note', function ($data){
                    if ($data->note) {
                        return str_limit($data->note, 25);
                    }
                    return '--';
                })
                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d H:ia');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.maintenance.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.maintenance.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.maintenance.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Bus'),
                __('Maintenance Date'),
                __('Price'),
                __('Note'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Maintenance')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Maintenance');
            } else {
                $this->viewData['pageTitle'] = __('Maintenance');
            }

            return $this->view('maintenance.index', $this->viewData);
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
            'text' => __('Maintenance'),
            'url' => route('system.maintenance.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Maintenance'),
        ];
        $this->viewData['pageTitle'] = __('Create Maintenance');
        return $this->view('maintenance.create', $this->viewData);

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
           'maintenance_date'                        =>'required|date',
           'bus_id'                                  =>'required|exists:buses,id',
            'price'                                   =>'required|numeric',
        ]);
        $theRequest = $request->all();
        $theRequest['staff_id']  = Auth::id();
        $maintenance = Maintenance::create($theRequest);
        if ($maintenance) {
            if ($request->has('no_of_km_oil')) {
                $maintenance->bus->update(['variable_distance'=>0]);
            }
            return redirect()
                ->route('system.maintenance.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        } else {
            return redirect()
                ->route('system.maintenance.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add maintenance'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Maintenance $maintenance)
    {

//dd($maintenance);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Client'),
                'url' => route('system.maintenance.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Maintenance';
        $this->viewData['result'] = $maintenance;
        return $this->view('maintenance.show', $this->viewData);
    }

    public function edit(Maintenance $maintenance)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Maintenance'),
            'url' => route('system.maintenance.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Maintenance'),
        ];

        $this->viewData['pageTitle'] = __('Edit Maintenance');
        $this->viewData['result'] = $maintenance;

        return $this->view('maintenance.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Maintenance $maintenance)
    {
        $this->validate($request,[
            'maintenance_date'                        =>'required|date',
            'bus_id'                                  =>'required|exists:buses,id',
        ]);
        $theRequest = $request->all();
        if ($maintenance->update($theRequest)) {
            if ($request->has('no_of_km_oil')) {
                $maintenance->bus->update(['variable_distance'=>0]);
            }
            return redirect()
                ->route('system.maintenance.edit', $maintenance->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Maintenance'));
        }
        else {
            return redirect()
                ->route('system.maintenance.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Maintenance'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Maintenance $maintenance)
    {
        $maintenance->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Item  has been deleted successfully')];
        } else {
            redirect()
                ->route('system.maintenance.index')
                ->with('status', 'success')
                ->with('msg', __('This Maintenance  has been deleted'));
        }
    }
}
