<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientTypes;
use App\Models\Profit;
use App\Models\ProfitCauses;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use App\Modules\Api\StaffTransformers\RevenueCausesTransformer;
use App\Modules\Api\StaffTransformers\RevenueTransformer;
use App\Modules\Api\StaffTransformers\SupplierReportTransformer;
use App\Modules\Api\StaffTransformers\SupplierTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class RevenueApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function revenue(Request $request)
    {
//        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = Profit::select([
            'revenues.id',
            'revenues.revenue_causes_id',
            'revenues.date',
            'revenues.amount',
            'revenues.description',
            'revenues.staff_id',
            'revenues.client_id',
            'revenues.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'revenues.staff_id')
            ->with(['revenue_causes'=>function($q){
                $q->select(['id','name']);

            },'client'=>function($client){
                $client->select(['id','name']);
            }]);


        whereBetween($eloquentData, 'DATE(revenues.created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'DATE(revenues.date)', $request->date1, $request->date2);
        whereBetween($eloquentData, 'revenues.amount', $request->amount1, $request->amount2);

        if ($request->id) {
            $eloquentData->where('revenues.id', '=', $request->id);
        }
        if ($request->staff_id) {
            $eloquentData->where('revenues.staff_id', '=', $request->staff_id);
        }

        if ($request->expense_causes_id) {
            $eloquentData->where('revenues.revenue_causes_id', '=', $request->expense_causes_id);
        }
        if ($request->description) {
            $eloquentData->where('revenues.description', 'LIKE','%'. $request->description. '%');
        }

        $Transformer = new RevenueTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Revenues Available'));
            }
                $revenue = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();


            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
            $revenueCauses = ProfitCauses::get(['id','name']);
        $Transformer->staff = $staff;
        $allData = $Transformer->transformCollection($revenue->toArray());
        $allData['staff'] = $staff;
        $allData['revenue_causes'] = $revenueCauses;
        return $this->json(true, __('Revenues'),$allData);

    }
    public function oneRevenue(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('revenue_id');
        $validator = Validator::make($RequestData, [
            'revenue_id' => 'required|exists:revenues,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $eloquentData = Profit::select([
            'revenues.id',
            'revenues.revenue_causes_id',
            'revenues.date',
            'revenues.amount',
            'revenues.description',
            'revenues.staff_id',
            'revenues.client_id',
            'revenues.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'revenues.staff_id')
            ->with(['revenue_causes'=>function($q){
                $q->select(['id','name']);

            },'client'=>function($client){
                $client->select(['id','name']);
            }])
        ->where('revenues.id',$request->revenue_id)
            ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $Transforrmer = new RevenueTransformer();
        return $this->json(true,__('One Revenue'),$Transforrmer->transform($eloquentData));

    }
    public function createRevenue(Request $request)
    {

        $validation = [
            'revenue_causes_id' =>'required|exists:revenue_causes,id',
            'date' =>'required|date',
            'amount' =>'required|numeric',
        ];
        if ($request->revenue_causes_id == 1){
            $validation['client_id'] = 'required|exists:clients,id';
        }
        $theRequest = $request->only([
            'revenue_causes_id',
            'date',
            'amount',
            'description',
            'client_id'
        ]);

        $validator=  Validator::make($theRequest,$validation);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }


      //  $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $revenue = Profit::create($theRequest);
        if ($revenue)
            return $this->respondCreated($revenue);
        else {
            return $this->json(false,__('Can\'t Add New Revenue'));
        }
    }
    public function updateRevenue(Request $request)
    {
        $validation = [
            'revenue_id' =>'required|exists:revenues,id',
            'revenue_causes_id' =>'nullable|exists:revenue_causes,id',
            'date' =>'nullable|date',
            'amount' =>'nullable|numeric',
        ];
        if ($request->revenue_causes_id == 1){
            $validation['client_id'] = 'nullable|exists:clients,id';
        }
        $theRequest = $request->only([
            'revenue_causes_id',
            'revenue_id',
            'date',
            'amount',
            'description',
            'client_id'
        ]);

        $validator=  Validator::make($theRequest,$validation);



        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $revenue = Profit::where('id',$request->revenue_id)->first();
            $columnToUpdate =  array_filter($theRequest);
            $updated = $revenue->update($columnToUpdate);
        if ($updated) {
            $Transformer = new RevenueTransformer();
            return $this->json(true,__('One Revenue Updated'),$Transformer->transform($revenue));
        }
        else {
            return $this->json(false,__('Can\'t Update this Revenue'));
        }
    }

    public function deleteRevenue(Request $request){
        $RequestData = $request->only('revenue_id');
       // dd($request->all());
        $validator = Validator::make($RequestData, [
            'revenue_id' => 'required|exists:revenues,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (Profit::where('id',$request->revenue_id)->delete())
            return $this->json(true,__('Revenue Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }

    public function revenueCauses(){
        return $this->json(true,__('Revenue Causes'),ProfitCauses::get(['id','name']));
    }

}