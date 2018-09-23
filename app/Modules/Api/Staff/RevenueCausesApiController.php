<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientTypes;
use App\Models\ProfitCauses;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use App\Modules\Api\StaffTransformers\RevenueCausesTransformer;
use App\Modules\Api\StaffTransformers\SupplierReportTransformer;
use App\Modules\Api\StaffTransformers\SupplierTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class RevenueCausesApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function revenueCauses(Request $request)
    {
//        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = ProfitCauses::select([
            'revenue_causes.id',
            'revenue_causes.name',
            'revenue_causes.description',
            'revenue_causes.staff_id',
            'revenue_causes.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'revenue_causes.staff_id');


        whereBetween($eloquentData, 'DATE(revenue_causes.created_at)', $request->created_at1, $request->created_at2);

        if ($request->id) {
            $eloquentData->where('revenue_causes.id', '=', $request->id);
        }
        if ($request->staff_id) {
            $eloquentData->where('revenue_causes.staff_id', '=', $request->staff_id);
        }

        if ($request->name) {
            $eloquentData->where('revenue_causes.name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->description) {
            $eloquentData->where('revenue_causes.description', 'LIKE','%'. $request->description. '%');
        }

        $Transformer = new RevenueCausesTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Revenue Causes Available'));
            }
                $revenueCauses = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();


            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $Transformer->staff = $staff;
        $allData = $Transformer->transformCollection($revenueCauses->toArray());
        $allData['staff'] = $staff;
        return $this->json(true, __('Revenue Causes'),$allData);

    }
    public function oneRevenueCauses(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('revenue_cause_id');
        $validator = Validator::make($RequestData, [
            'revenue_cause_id' => 'required|exists:revenue_causes,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $eloquentData = ProfitCauses::select([
            'revenue_causes.id',
            'revenue_causes.name',
            'revenue_causes.description',
            'revenue_causes.staff_id',
            'revenue_causes.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'revenue_causes.staff_id')
        ->where('revenue_causes.id',$request->revenue_cause_id)
            ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $Transforrmer = new RevenueCausesTransformer();
        return $this->json(true,__('One Revenue Causes'),$Transforrmer->transform($eloquentData));

    }
    public function createRevenueCauses(Request $request)
    {
        // ask for validation of init_credit if it required or not.
        $theRequest = $request->only([
            'name',
            'description',
        ]);
        $validator=  Validator::make($theRequest,[
            'name' =>'required',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }


      //  $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $revenue_cause = ProfitCauses::create($theRequest);
        if ($revenue_cause)
            return $this->respondCreated($revenue_cause);
        else {
            return $this->json(false,__('Can\'t Add New Revenue Cause'));
        }
    }
    public function updateRevenueCauses(Request $request)
    {
        $theRequest = $request->all();
        $validator=  Validator::make($theRequest,[
            'revenue_cause_id' => 'required|exists:revenue_causes,id',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }


        $revenueCause = ProfitCauses::where('id',$request->revenue_cause_id)->first();
            $columnToUpdate =  array_filter($theRequest);
            $updated = $revenueCause->update($columnToUpdate);

        if ($updated) {
            $Transformer = new RevenueCausesTransformer();
            return $this->json(true,__('One Revenue Cause Updated'),$Transformer->transform($revenueCause));
        }
        else {
            return $this->json(false,__('Can\'t Update this Revenue Cause'));
        }
    }

    public function deleteRevenueCauses(Request $request){
        $RequestData = $request->only('revenue_cause_id');
       // dd($request->all());
        $validator = Validator::make($RequestData, [
            'revenue_cause_id' => 'required|exists:revenue_causes,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if ($request->revenue_cause_id == 1){
            return $this->json(false,__('Sorry,You can\'t Delete This Revenue Cause'));
        }
        if (ProfitCauses::where('id',$request->revenue_cause_id)->delete())
            return $this->json(true,__('Revenue Cause Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }

}