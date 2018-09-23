<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;
use App\Models\Brand;
use App\Models\Bus;
use App\Models\BusTraking;
use App\Models\Client;
use App\Models\ClientTypes;
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
use DateTime;

class BusController extends SystemController
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
            $eloquentData = Bus::select([
                'buses.id',
                'buses.bus_brand_id',
                'buses.bus_number',
                'buses.fixed_distance',
                'buses.variable_distance',
                'buses.available',
                'buses.driver',
                'buses.staff_id',
                'buses.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'buses.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(buses.created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData,'buses.fixed_distance',$request->fixed_distance1,$request->fixed_distance2);
            whereBetween($eloquentData,'buses.variable_distance',$request->variable_distance1,$request->variable_distance2);
            if ($request->id) {
                $eloquentData->where('buses.id', '=', $request->id);
            }
            if ($request->available) {
                $eloquentData->where('buses.available', '=', $request->available);
            }
            if ($request->bus_number) {
                $eloquentData->where('buses.bus_number', '=',  $request->bus_number);
            }
            if ($request->driver) {
                $eloquentData->where('buses.driver', '=', $request->driver);
            }

            if ($request->bus_brand_id) {
                $eloquentData->where('buses.bus_brand_id', '=', $request->bus_brand_id);
            }
            if ($request->staff_id) {
                $eloquentData->where('buses.staff_id', '=', $request->staff_id);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('bus_number', '{{$bus_number}}')
                ->addColumn('fixed_distance', '{{$fixed_distance}}')
                ->addColumn('variable_distance', '{{$variable_distance}}')
                ->addColumn('available', '{{$available}}')
                ->addColumn('brand', function ($data){
                    return $data->brand->name;
                })
                ->addColumn('driver_name', function ($data){
                    if ($data->driver){
                        return "<a target='_blank' href=\"" . route('system.staff.show', $data->busDriver->id) . "\">".$data->busDriver->Fullname."</a>";
                    }
                    return '--';
//                    return "<a target='_blank' href=\"" . route('system.types.show', $data->client_types->id) . "\">".$data->client_types->name."</a>";
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
                                <li class=\"dropdown-item\"><a href=\"" . route('system.bus.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.bus.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.bus.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Bus Number'),
                __('distance Km'),
                __('Change Oil Km'),
                __('Availability'),
                __('Brand'),
                __('Driver Name'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Buses')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Buses');
            } else {
                $this->viewData['pageTitle'] = __('Bus');
            }

            $return = [];
            $data = Brand::get(['id', 'name']);
            foreach ($data as $key => $value) {
                $return[$value->id] = $value->name;
            }
            $this->viewData['brand'] = $return;

            return $this->view('bus.index', $this->viewData);
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
            'text' => __('Bus'),
            'url' => route('system.bus.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Bus'),
        ];
        $return = [];
        $data = Brand::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['brand'] = $return;
        $this->viewData['pageTitle'] = __('Create Bus');
        return $this->view('bus.create', $this->viewData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // dd($request->all());
        $this->validate($request,[
           'bus_number'                      =>'required',
           'bus_brand_id'                    =>'required|exists:brands,id',
           'gas'                            =>'required',
           'fixed_distance'            =>'required',
           'variable_distance'                 =>'required',
            'available'                    =>'required|in:available,unavailable',
        ]);
        $theRequest = $request->all();
     $theRequest['staff_id']  = Auth::id();
        $bus = Bus::create($theRequest);
        if ($bus) {

            return redirect()
                ->route('system.bus.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        } else {
            return redirect()
                ->route('system.bus.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Bus'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Bus $bus)
    {
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Bus'),
                'url' => route('system.bus.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];


        if ($bus->maintenance()->whereNotNull('no_of_km_oil')->count() !=0) {
            $firstMaintenance = $bus->maintenance()->whereNotNull('no_of_km_oil')->value('created_at');
            $lastMaintenance = $bus->maintenance()->whereNotNull('no_of_km_oil')->latest()->first()->created_at;

           // dd($firstMaintenance);
            $sumNumberofKmOfmaintenance = $bus->maintenance()->whereNotNull('no_of_km_oil')->sum('no_of_km_oil');
//dd($sumNumberofKmOfmaintenance);
            //->groupBy(DB::raw('DATE(created_at)'))
            $busMaintenance = $bus->maintenance()->whereNotNull('no_of_km_oil')->get();
            $numberOfBussMaintenance = count($busMaintenance);
            // dd($numberOfBussMaintenance);
            $firstMaintenance = new DateTime($firstMaintenance);
            $lastMaintenance = new DateTime($lastMaintenance);
            $diff = $firstMaintenance->diff($lastMaintenance);
//dd($numberOfBussMaintenance);
            $this->viewData['oilChangeRate'] = $diff->days / $numberOfBussMaintenance;
            $this->viewData['quantityOfOilChangeRate'] = round($sumNumberofKmOfmaintenance / $diff->days,2);
        }
        if ($bus->bus_traking()) {
            $busTraking = $bus->bus_traking()->groupBy(DB::raw('DATE(created_at)'))->get();
            $numberOfBusTraking = count($busTraking);
            $sumNumberofKm = $bus->bus_traking()->sum('number_km');
            if ($numberOfBusTraking == 0)
                $numberOfBusTraking = 1;
            $this->viewData['dailyTrafficRate'] = $sumNumberofKm / $numberOfBusTraking;
        }
        $this->viewData['pageTitle'] = 'Bus';
        $this->viewData['result'] = $bus;
        return $this->view('bus.show', $this->viewData);
    }

    public function edit(Bus $bus)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Bus'),
            'url' => route('system.bus.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Bus'),
        ];
        $return = [];
        $data = Brand::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['brand'] = $return;

        $this->viewData['pageTitle'] = __('Edit Bus');
        $this->viewData['result'] = $bus;

        return $this->view('bus.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Bus $bus)
    {
        $this->validate($request,[
            'bus_number'                      =>'required',
            'bus_brand_id'                    =>'required|exists:brands,id',
            'gas'                            =>'required',
            'fixed_distance'            =>'required',
            'variable_distance'                 =>'required',
            'available'                    =>'required|in:available,unavailable',
        ]);
        $theRequest = $request->all();
        if ($bus->update($theRequest)) {
            return redirect()
                ->route('system.bus.edit', $bus->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Bus'));
        }
        else {
            return redirect()
                ->route('system.bus.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Bus'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Bus $bus)
    {
        $bus->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Bus  has been deleted successfully')];
        } else {
            redirect()
                ->route('system.bus.index')
                ->with('status', 'success')
                ->with('msg', __('This Bus  has been deleted'));
        }
    }
    public function changeAvailability(Request $request){
        $update = Bus::where('id',$request->id)->update(['available'=>$request->available]);
        if ($update) {
            return redirect()
                ->route('system.bus.show', $request->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Bus'));
        }
        else {
            return redirect()
                ->route('system.bus.show',$request->id)
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Bus'));
        }
    }
}
