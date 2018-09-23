<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Bus;
use App\Models\Client;
use App\Models\ClientTypes;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use App\Modules\Api\StaffTransformers\BusTransformer;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use App\Modules\Api\StaffTransformers\SupplierReportTransformer;
use App\Modules\Api\StaffTransformers\SupplierTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class BusApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function allBus(Request $request)
    {
//        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

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
            ->join('staff', 'staff.id', '=', 'buses.staff_id')
                ->with(['brand'=>function($brand){
                    $brand->select(['id','name']);
                },'busDriver'=>function($q){
                    $q->select(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name")]);
                }]);

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

        $busTransformer = new BusTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Supplier Available'));
            }
                $bus = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $brand = Brand::get(['id','name']);
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $busTransformer->staff = $staff;
        $allData = $busTransformer->transformCollection($bus->toArray());
        $allData['staff'] = $staff;
        $allData['Brand'] = $brand;
        return $this->json(true, __('Buss'),$allData);

    }
    public function oneBus(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('bus_id');
        $validator = Validator::make($RequestData, [
            'bus_id' => 'required|exists:buses,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
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
            ->join('staff', 'staff.id', '=', 'buses.staff_id')
            ->with(['brand'=>function($brand){
                $brand->select(['id','name']);
            },'busDriver'=>function($q){
                $q->select(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name")]);
            }])
        ->where('buses.id',$request->bus_id)
            ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $busTransforrmer = new BusTransformer();
        return $this->json(true,__('One Bus'),$busTransforrmer->transform($eloquentData));

    }
    public function createBus(Request $request)
    {
        // ask for validation of init_credit if it required or not.
        $validator=  Validator::make($request->all(),[
            'bus_number'                         =>'required',
            'bus_brand_id'                       =>'required|exists:brands,id',
            'gas'                                =>'required',
            'fixed_distance'                     =>'required|numeric',
            'variable_distance'                  =>'required|numeric',
            'available'                          =>'required|in:available,unavailable',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }



        $theRequest = $request->all();

      //  $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $bus = Bus::create($theRequest);
        if ($bus)
            return $this->respondCreated($bus);
        else {
            return $this->json(false,__('Can\'t Add New Bus'));
        }
    }
    public function updateBus(Request $request)
    {
        $theRequest = $request->all();
        $validator=  Validator::make($theRequest,[
            'bus_id'                    =>'required|exists:buses,id',
           // 'bus_number'                      =>'required',
            'bus_brand_id'                    =>'nullable|exists:brands,id',

          //  'gas'                            =>'required',
            'fixed_distance'                =>'nullable|numeric',
            'variable_distance'                 =>'nullable|numeric',
            'available'                    =>'nullable|in:available,unavailable',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }


        $bus = Bus::where('id',$request->bus_id)->first();


            $columnToUpdate =  array_filter($theRequest);
            $updated = $bus->update($columnToUpdate);


        if ($updated) {
            $busTransformer = new BusTransformer();
            return $this->json(true,__('Update One Bus'),$busTransformer->transform($bus));
        }
        else {
            return $this->json(false,__('Can\'t Update this Bus'));
        }
    }

        public function deleteBus(Request $request){
        $RequestData = $request->only('bus_id');
        $validator = Validator::make($RequestData, [
            'bus_id' => 'required|exists:buses,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (Bus::where('id',$request->bus_id)->delete())
            return $this->json(true,__('Bus Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }

}