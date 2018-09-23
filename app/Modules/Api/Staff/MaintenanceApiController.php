<?php
namespace App\Modules\Api\Staff;

use App\Models\Brand;
use App\Models\Bus;
use App\Models\Maintenance;
use App\Models\Staff;
use App\Modules\Api\StaffTransformers\BusTransformer;
use App\Modules\Api\StaffTransformers\MaintenanceTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class MaintenanceApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function allMaintenance(Request $request)
    {
//        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

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
            ->join('staff', 'staff.id', '=', 'maintenances.staff_id')
                ->with(['bus'=>function($bus){
                    $bus->select(['id','bus_number']);
                }]);

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

        $maintenanceTransformer = new MaintenanceTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Supplier Available'));
            }
                $maintenance = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $bus = Bus::get(['id','bus_number']);
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $maintenanceTransformer->staff = $staff;
        $allData = $maintenanceTransformer->transformCollection($maintenance->toArray());
        $allData['staff'] = $staff;
        $allData['bus'] = $bus;
        return $this->json(true, __('Buss Maintenance'),$allData);

    }
    public function oneMaintenance(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('maintenance_id');
        $validator = Validator::make($RequestData, [
            'maintenance_id' => 'required|exists:maintenances,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
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
            ->join('staff', 'staff.id', '=', 'maintenances.staff_id')
            ->with(['bus'=>function($bus){
                $bus->select(['id','bus_number']);
            }])
        ->where('maintenances.id',$request->maintenance_id)
            ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $maintenanceTransformer = new MaintenanceTransformer();
        return $this->json(true,__('One Maintenance'),$maintenanceTransformer->transform($eloquentData));

    }
    public function createMaintenance(Request $request)
    {
        // ask for validation of init_credit if it required or not.
        $validator=  Validator::make($request->all(),[
            'maintenance_date'                        =>'required|date',
            'price'                                   =>'required|numeric',
            'bus_id'                                  =>'required|exists:buses,id',
            //'no_of_km_oil'                                  =>'nullable|numeric',
            'no_of_km_oil'                                  =>'required_with:no_km_moving|numeric|nullable',
            'no_km_moving'                                  =>'required_with:no_of_km_oil|numeric|nullable',
        ]);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $theRequest = $request->all();

      //  $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $maintenance = Maintenance::create($theRequest);
        if ($maintenance)
            return $this->respondCreated($maintenance);
        else {
            return $this->json(false,__('Can\'t Add New Maintenance'));
        }
    }
    public function updateMaintenance(Request $request)
    {
        $theRequest = $request->all();
        $validator=  Validator::make($theRequest,[
            'maintenance_id'                          =>'required|exists:maintenances,id',
            'maintenance_date'                        =>'nullable|date',
            'price'                                   =>'nullable|numeric',
            'bus_id'                                  =>'nullable|exists:buses,id',
            'no_of_km_oil'                            =>'required_with:no_km_moving|numeric|nullable',
            'no_km_moving'                            =>'required_with:no_of_km_oil|numeric|nullable',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }


        $maintenance = Maintenance::where('id','=',$request->maintenance_id)->first();
            $columnToUpdate =  array_filter($theRequest);
            $updated = $maintenance->update($columnToUpdate);

        if ($updated) {
            $busTransformer = new MaintenanceTransformer();
            return $this->json(true,__('Update One Maintenance'),$busTransformer->transform($maintenance));
        }
        else {
            return $this->json(false,__('Can\'t Update this Maintenance'));
        }
    }

        public function deleteMaintenance(Request $request){
        $RequestData = $request->only('maintenance_id');
        $validator = Validator::make($RequestData, [
            'maintenance_id' => 'required|exists:maintenances,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (Maintenance::where('id',$request->maintenance_id)->delete())
            return $this->json(true,__('Maintenance Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }
    // this is for creating bus maintenance .
    public function bus(){
            $bus = Bus::get(['id','bus_number']);
            return $this->json(true,__('Bus for creating Maintenance'),$bus);
    }

}