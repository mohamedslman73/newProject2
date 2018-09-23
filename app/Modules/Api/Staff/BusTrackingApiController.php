<?php
namespace App\Modules\Api\Staff;
use App\Models\Bus;
use App\Models\BusTracking;
use App\Models\Project;
use App\Models\Staff;
use App\Modules\Api\StaffTransformers\BusTrackingTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class BusTrackingApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function allBusTracking(Request $request)
    {
//        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = BusTraking::select([
            'bus_traking.id',
            'bus_traking.bus_id',
            'bus_traking.project_id',
            'bus_traking.driver_id',
            'bus_traking.number_km',
            'bus_traking.date_from',
            'bus_traking.date_to',
            'bus_traking.destination_from',
            'bus_traking.destination_to',
            'bus_traking.cost_per_km',
            'bus_traking.staff_id',
            'bus_traking.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'bus_traking.staff_id')
                ->with(['bus'=>function($bus){
                    $bus->select(['id','bus_number']);
                }
                ,'project'=>function($bus){
                        $bus->select(['id','name as project_name']);
                    }
                ,'busDriver'=>function($q){
                    $q->select(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name")]);
                }]);

        whereBetween($eloquentData, 'DATE(bus_traking.created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'DATE(bus_traking.date)', $request->date1, $request->date2);
        whereBetween($eloquentData, 'bus_traking.number_km', $request->number_km1, $request->number_km2);

        if ($request->id) {
            $eloquentData->where('bus_traking.id', '=', $request->id);
        }
        if ($request->from) {
            $eloquentData->where('bus_traking.bus_number', 'LIKE', '%'. $request->from.'%');
        }
        if ($request->to) {
            $eloquentData->where('bus_traking.bus_number', 'LIKE', '%'. $request->to.'%');
        }
        if ($request->driver_id) {
            $eloquentData->where('bus_traking.driver_id', '=', $request->driver_id);
        }

        if ($request->bus_id) {
            $eloquentData->where('bus_traking.bus_id', '=', $request->bus_id);
        }
        if ($request->project_id) {
            $eloquentData->where('bus_traking.project_id', '=', $request->project_id);
        }
        if ($request->staff_id) {
            $eloquentData->where('buses.staff_id', '=', $request->staff_id);
        }

        $busTrackingTransformer = new BusTrackingTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Supplier Available'));
            }
                $busTracking = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $bus = Bus::get(['id','bus_number']);
            $projects = Project::get(['id','name']);
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $busTrackingTransformer->staff = $staff;
        $allData = $busTrackingTransformer->transformCollection($busTracking->toArray());
        $allData['staff'] = $staff;
        $allData['bus'] = $bus;
        $allData['projects'] = $projects;
        return $this->json(true, __('Buss'),$allData);

    }
    public function oneBusTracking(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('bus_tracking_id');
        $validator = Validator::make($RequestData, [
            'bus_tracking_id' => 'required|exists:bus_traking,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $eloquentData = BusTraking::select([
            'bus_traking.id',
            'bus_traking.bus_id',
            'bus_traking.project_id',
            'bus_traking.driver_id',
            'bus_traking.number_km',
            'bus_traking.date_from',
            'bus_traking.date_to',
            'bus_traking.destination_from',
            'bus_traking.destination_to',
            'bus_traking.cost_per_km',
            'bus_traking.staff_id',
            'bus_traking.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'bus_traking.staff_id')
            ->with(['bus'=>function($bus){
                $bus->select(['id','bus_number']);
            }
                ,'project'=>function($bus){
                    $bus->select(['id','name as project_name']);
                }
                ,'busDriver'=>function($q){
                    $q->select(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name")]);
                }])
            ->where('bus_traking.id',$request->bus_tracking_id)
            ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $busTrackingTransforrmer = new BusTrackingTransformer();
        return $this->json(true,__('One Bus Tracking'),$busTrackingTransforrmer->transform($eloquentData));

    }
    public function createBusTracking(Request $request)
    {
        // ask for validation of init_credit if it required or not.
        $validator=  Validator::make($request->all(),[
            'bus_id'                              =>'required|exists:buses,id',
            'project_id'                          =>'required|exists:projects,id',
            'driver_id'                           =>'required|exists:staff,id',
            'km_after'                           =>'required',
            'date_from'                           =>'required|date',
            'date_to'                            =>'nullable|date',
            'destination_from'                   =>'required',
            'destination_to'                     =>'required',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $theRequest = $request->all();

      //  $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;

        $busTracking = BusTracking::create($theRequest);

        if ($busTracking) {
            $number_km =   $theRequest['km_after'] - $busTracking->bus->fixed_distance;
            //dd($number_km);
            $busTracking->update(['number_km'=>$number_km]);
            $bus = $busTracking->bus;
            $newFixedDistance = $theRequest['km_after'] ;
            $newVariableDistance = $bus->variable_distance + $number_km;
            $bus->update(['fixed_distance' => $newFixedDistance, 'variable_distance' => $newVariableDistance]);

            return $this->respondCreated($busTracking);
        }
        else {
            return $this->json(false,__('Can\'t Add New Bus Tracking'));
        }
    }
    public function updateBusTracking(Request $request)
    {
        $theRequest = $request->all();
        $validator = Validator::make($theRequest, [
            'bus_tracking_id'    => 'required|exists:bus_traking,id',
            'bus_id'             => 'nullable|exists:buses,id',
            'project_id'         => 'nullable|exists:projects,id',
            'driver_id'          => 'nullable|exists:staff,id',
            'km_after'           => 'required|numeric',
            'date_from'          => 'nullable|date',
            'date_to'            => 'nullable|date',
//            'destination_from'   => 'required',
//            'destination_to'     => 'required',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }


        $tracking = BusTracking::where('id', $request->bus_tracking_id)->first();


        $latest_number_km = $tracking->number_km;
        $theRequest['number_km'] = ($theRequest['km_after'] -  $tracking->bus->fixed_distance)+$latest_number_km;
        // , 'number_km' => $theRequest['number_km']

            $columnToUpdate = array_filter($theRequest);
            $updated = $tracking->update($columnToUpdate);

        if ($updated) {
          //  $tracking->update(['number_km' => $theRequest['number_km']]);
                $bus = $tracking->bus;
                $newFixedDistance = $bus->fixed_distance - $latest_number_km + $theRequest['number_km'];
                $newVariableDistance = $bus->variable_distance - $latest_number_km + $theRequest['number_km'];
                $bus->update(['fixed_distance' => $newFixedDistance, 'variable_distance' => $newVariableDistance]);

                $variable_distance = $tracking->bus->variable_distance;

                $busTrackingTransformer = new BusTrackingTransformer();
                return $this->json(true, __('Update One Bus'), $busTrackingTransformer->transform($tracking));
            } else {
                return $this->json(false, __('Can\'t Update this Bus'));
            }

    }
    public function busProjectsAndDrivers(){
        $bus = Bus::get(['id','bus_number']);
        $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $projects = Project::get(['id','name']);
        $data=[];
        $data['bus'] = $bus;
        $data['projects'] = $projects;
        $data['drivers'] = $staff;
        return $this->json(true,__('Bus,projects and Drivers'),$data);
    }

        public function deleteBusTracking(Request $request){
        $RequestData = $request->only('tracking_bus_id');
        $validator = Validator::make($RequestData, [
            'tracking_bus_id' => 'required|exists:bus_traking,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (BusTracking::where('id',$request->tracking_bus_id)->delete())
            return $this->json(true,__('Bus Tracking Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }

}