<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;
use App\Models\Brand;
use App\Models\Bus;
use App\Models\BusTracking;
use App\Models\Client;
use App\Models\ClientTypes;
use App\Models\Item;
use App\Models\ItemCategories;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use App\Notifications\UserNotification;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class BusTrackingController extends SystemController
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
            $eloquentData = BusTracking::select([
                'bus_tracking.id',
                'bus_tracking.bus_id',
                'bus_tracking.project_id',
                'bus_tracking.driver_id',
                'bus_tracking.number_km',
                'bus_tracking.date_from',
                'bus_tracking.date_to',
                'bus_tracking.destination_from',
                'bus_tracking.destination_to',
                'bus_tracking.cost_per_km',
                'bus_tracking.staff_id',
                'bus_tracking.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'bus_tracking.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(bus_tracking.created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(bus_tracking.date)', $request->date1, $request->date2);
            whereBetween($eloquentData, 'bus_tracking.number_km', $request->number_km1, $request->number_km2);

            if ($request->id) {
                $eloquentData->where('bus_tracking.id', '=', $request->id);
            }
            if ($request->from) {
                $eloquentData->where('bus_tracking.bus_number', 'LIKE', '%'. $request->from.'%');
            }
            if ($request->to) {
                $eloquentData->where('bus_tracking.bus_number', 'LIKE', '%'. $request->to.'%');
            }
            if ($request->driver_id) {
                $eloquentData->where('bus_tracking.driver_id', '=', $request->driver_id);
            }

            if ($request->bus_id) {
                $eloquentData->where('bus_tracking.bus_id', '=', $request->bus_id);
            }
            if ($request->project_id) {
                $eloquentData->where('bus_tracking.project_id', '=', $request->project_id);
            }
            if ($request->staff_id) {
                $eloquentData->where('buses.staff_id', '=', $request->staff_id);
            }
            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('bus_id', function ($data){
                    return $data->bus->bus_number;
                })
                ->addColumn('project_id', function ($data){
                    return $data->project->name;
                })
                ->addColumn('driver_id', function ($data){
                      return "<a target='_blank' href=\"" . route('system.staff.show', $data->busDriver->id) . "\">".$data->busDriver->Fullname."</a>";
                })
                ->addColumn('number_km', '{{$number_km}}')

                ->addColumn('destination_from','{{$destination_from}}')
                ->addColumn('destination_to','{{$destination_to}}')
                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.tracking.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.tracking.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.tracking.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Bus Number'),
                __('Project name'),
                __('Driver Name'),
                __('Number Of Km'),
                __('From'),
                __('To'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Bus Tracking')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Bus Tracking');
            } else {
                $this->viewData['pageTitle'] = __('Bus Tracking');
            }

            return $this->view('bus-tracking.index', $this->viewData);
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
            'text' => __('Bus Tracking'),
            'url' => route('system.tracking.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Bus Tracking'),
        ];

        $this->viewData['pageTitle'] = __('Create Bus Tracking');
        return $this->view('bus-tracking.create', $this->viewData);

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
           'bus_id'                              =>'required|exists:buses,id',
           'project_id'                          =>'required|exists:projects,id',
           'driver_id'                           =>'required|exists:staff,id',
         //  'km_before'                           =>'required',
           'km_after'                           =>'required',
           'date_from'                           =>'required|date',
          //  'date_to'                            =>'required|date',
            'destination_from'                   =>'required',
            'destination_to'                     =>'required',
         //   'cost_per_km'                    =>'required',
        ]);

        $theRequest = $request->all();

      //  $theRequest['number_km'] = $theRequest['km_after'] - $theRequest['km_before'];
        $theRequest['staff_id']  = Auth::id();
        $busTracking = BusTracking::create($theRequest);

        if ($busTracking) {

           $number_km =   $theRequest['km_after'] - $busTracking->bus->fixed_distance;
           //dd($number_km);
            $busTracking->update(['number_km'=>$number_km]);
            $bus = $busTracking->bus;
            $newFixedDistance = $theRequest['km_after'] ;
            $newVariableDistance = $bus->variable_distance + $number_km;
            $bus->update(['fixed_distance' => $newFixedDistance, 'variable_distance' => $newVariableDistance]);


            // Notify Staff For Changing the oil for $busTracking->bus Bus.

            $variable_distance = $busTracking->bus->variable_distance;

            $no_km_moving = $busTracking->bus->maintenance()->latest()->first()->no_km_moving;
            if ($no_km_moving >= $variable_distance) {
                if (!empty(setting('monitor_staff'))) {
                    $monitorStaff = Staff::whereIn('id', explode("\n", setting('monitor_staff')))
                        ->get();

                    foreach ($monitorStaff as $key => $value) {
                        $value->notify(
                            (new UserNotification([
                                'title' => 'Bus Should Change Oil',
                                'description' => 'Bus Number ' .$busTracking->bus->bus_number .' Should Change Oil',
                                'url' => route('system.maintenance.show', $busTracking->bus->maintenance()->latest()->first()->id)
                            ]))
                                ->delay(5)
                        );
                    }
                }
            }

            return redirect()
                ->route('system.tracking.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        } else {
            return redirect()
                ->route('system.tracking.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Bus Tracking'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(BusTracking $tracking)
    {
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Bus Tracking'),
                'url' => route('system.tracking.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Bus Tracking';
        $this->viewData['result'] = $tracking;
        return $this->view('bus-tracking.show', $this->viewData);
    }

    public function edit(BusTracking $tracking)
    {
//        dd($tracking->toArray());
        $this->viewData['breadcrumb'][] = [
            'text' => __('Bus Tracking'),
            'url' => route('system.tracking.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Bus Tracking'),
        ];

        $this->viewData['pageTitle'] = __('Edit Bus Tracking');
        $this->viewData['result'] = $tracking;

        return $this->view('bus-tracking.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,BusTracking $tracking)
    {
        if (!empty($tracking->date_to)){
            return redirect()
                ->route('system.tracking.edit', $tracking->id)
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit This Bus Tracking Again'));
        }
        $this->validate($request,[
            'bus_id'                              =>'nullable|exists:buses,id',
            'project_id'                          =>'required|exists:projects,id',
            'driver_id'                           =>'nullable|exists:staff,id',
          //  'km_before'                           =>'required',
            'km_after'                           =>'required',
            'date_from'                           =>'required|date',
           // 'date_to'                            =>'required|date',
            'destination_from'                   =>'required',
            'destination_to'                     =>'required',
            //   'cost_per_km'                    =>'required',
        ]);

        $theRequest = $request->all();

        if ($request->has('bus_id')){
            $theRequest['bus_id'] = $request->bus_id;
        }else{
            unset($theRequest['bus_id']);
        }
        if ($request->has('driver_id')){
            $theRequest['driver_id'] = $request->driver_id;
        }else{
            unset($theRequest['driver_id']);
        }

        $theRequest['number_km'] = $theRequest['km_after'] -  $tracking->bus->fixed_distance;

        $latest_number_km = $tracking->number_km;

        if ($tracking->update($theRequest)) {
            $bus = $tracking->bus;
            $newFixedDistance = $bus->fixed_distance - $latest_number_km + $theRequest['number_km'];
            $newVariableDistance = $bus->variable_distance - $latest_number_km + $theRequest['number_km'];
           $bus->update(['fixed_distance' => $newFixedDistance, 'variable_distance' => $newVariableDistance]);


            $variable_distance = $tracking->bus->variable_distance;

            $no_km_moving = $tracking->bus->maintenance()->latest()->first()->no_km_moving;
            if ($no_km_moving >= $variable_distance) {
                if (!empty(setting('monitor_staff'))) {
                    $monitorStaff = Staff::whereIn('id', explode("\n", setting('monitor_staff')))
                        ->get();

                    foreach ($monitorStaff as $key => $value) {
                        $value->notify(
                            (new UserNotification([
                                'title' => 'Bus Should Change Oil',
                                'description' => 'Bus Number' .$tracking->bus->bus_number .'Should Change Oil',
                                'url' => route('merchant.maintenance.show', $tracking->bus->maintenance()->latest()->first()->id)
                            ]))
                                ->delay(5)
                        );
                    }
                }
            }

            return redirect()
                ->route('system.tracking.edit', $tracking->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Bus Tracking'));
        }
        else {
            return redirect()
                ->route('system.tracking.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Bus Tracking'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,BusTracking $tracking)
    {
        $tracking->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Bus Tracking has been deleted successfully')];
        } else {
            redirect()
                ->route('system.tracking.index')
                ->with('status', 'success')
                ->with('msg', __('This Bus Tracking  has been deleted'));
        }
    }
}
