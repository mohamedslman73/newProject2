<?php
namespace App\Modules\Api\Staff;

use App\Models\Brand;
use App\Models\Bus;
use App\Models\Client;
use App\Models\Department;
use App\Models\Item;
use App\Models\Maintenance;
use App\Models\Quotations;
use App\Models\Staff;
use App\Modules\Api\StaffTransformers\BusTransformer;
use App\Modules\Api\StaffTransformers\MaintenanceTransformer;
use App\Modules\Api\StaffTransformers\QuotationTransformer;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class QuotationApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function allQuotation(Request $request)
    {
//        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = Quotations::select('id','client_id','total_price','staff_id','created_at')
        ->with(['client'=>function($client){
                    $client->select(['id','name']);
                },'staff'=>function($staff){
            $staff->select(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name")]);
        }]);

        whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'total_price', $request->price1, $request->price2);

        if ($request->id) {
            $eloquentData->where('id', '=', $request->id);
        }
        if ($request->client_id) {
            $eloquentData->where('client_id', '=', $request->client_id);
        }

        if ($request->status) {
            $eloquentData->where('status', '=', $request->status);
        }
        if ($request->staff_id) {
            $eloquentData->where('staff_id', '=', $request->staff_id);
        }

        $quotationTransformer = new QuotationTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Quotations Available'));
            }
                $maintenance = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $clients = Client::get(['id','name']);
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $quotationTransformer->staff = $staff;
        $allData = $quotationTransformer->transformCollection($maintenance->toArray());
        $allData['staff'] = $staff;
        $allData['clients'] = $clients;
        return $this->json(true, __('Quotations'),$allData);

    }
    public function oneQuotation(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('quotation_id');
        $validator = Validator::make($RequestData, [
            'quotation_id' => 'required|exists:quotations,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $quotation = Quotations::select('id','client_id','price_per_cleaner','total_price','staff_id','items','cleaners','created_at')
            ->with(['client'=>function($client){
                $client->select(['id','name']);
            },'staff'=>function($staff){
                $staff->select(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name")]);
            }])
        ->where('id',$request->quotation_id)
            ->first();



        $names = [];
        $itemNames = [];
        $coutBoys = 0;
        $coutGirls = 0;
        $coutCounts = 0;
        $coutPrice = 0;
        $countItems = 0;

        if(empty($quotation))
            return $this->json(false,__('No Results'));
        $quotationTransformer = new QuotationTransformer();

        $items = unserialize($quotation->items);
        //dd($quotation->toArray());
       $quotation->item_id = $items['item_id'];
        $quotation->count = $items['count'];
        $quotation->price = $items['price'];

        if (!empty($quotation->item_id)) {
            foreach ($quotation->item_id as $key => $row) {
                $coutCounts += $quotation->count[$key];

                $coutPrice +=$quotation->price[$key];
                $countItems +=$quotation->item_id[$key];
                $item = Item::find($row);
                $itemNames[$key] = $item->name;
            }
        }
     //   dd($coutCounts,$coutPrice,$countItems);

        $cleaners = unserialize($quotation->cleaners);
        $quotation->department_id = $cleaners['department_id'];
        $quotation->girles = $cleaners['girles'];
        $quotation->boys = $cleaners['boys'];
        $quotation->client_name = $quotation->client->name;

//dd($quotation->count[0]);
        if (!empty($quotation->department_id)) {
            foreach ($quotation->department_id as $key => $row) {
                $coutBoys += $quotation->boys[$key];
                $coutGirls += $quotation->girles[$key];

                $item = Department::find($row);
                $names[$key] = $item->name;
            }
        }

        $quotation->names =$names ;
        $quotation->itemNames=$itemNames ;

        $itemsTotalPrice =   $quotation->total_price -(($coutBoys + $coutGirls)* $quotation->price_per_cleaner);
        $cleanerTotalPrice =  $quotation->total_price -$itemsTotalPrice;
        $quotation->Total_Boys = $coutBoys;
        $quotation->Total_Girls = $coutGirls;
        $quotation->Total_Items_Price = $itemsTotalPrice;
        $quotation->Total_Cleaners_Price = $cleanerTotalPrice;

        return $this->json(true,__('One Quotation'),$quotationTransformer->transform($quotation));

    }
    public function createQuotation(Request $request)
    {
        $theRequest = $request->only(['client_id', 'description', 'price_per_cleaner', 'name','phone','address', 'department_id', 'girles', 'boys', 'item_id', 'count', 'price']);
        $validation = [

            'description'                    => 'required',
            'price_per_cleaner'              => 'required|numeric',
            // 'status'                         =>'required',
            'department_id'                  => 'array',
            'department_id.*'                => 'required',
            'girles'                         => 'array',
            //  'girles.*'                       => 'nullable|numeric',
            'boys'                           => 'array',
            //   'boys.*'                         => 'numeric|required_if:girls,==,',
        ];

        if ($request->client_type == 'existClient') {
            $validation['client_id'] = 'required|exists:clients,id';
        }
        elseif ($request->client_type == 'newClient') {
            $validation['name'] = 'required|min:5';
            $validation['phone'] = 'required|numeric|min:8';
            $validation['address'] = 'required|min:5';
        }
        if ($request->girles){
            foreach ($request->girles as $key => $value) {
                if ($request->girles[$key] == 0 && $request->boys[$key] == 0) {
                    $validation['boys.*'] = 'required|numeric';
                    $validation['girles.*'] = 'nullable|numeric';
                }
            }
    }

        if($request->item){
            $validation['price.*'] = 'required|numeric';
            $validation['count.*'] = 'required|numeric';
        }

       // $this->validate($request,$validation);

        $validator=  Validator::make($theRequest,$validation);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        if($request->file){
            $theRequest['file'] = $request->file->store('quotations/'.date('y').'/'.date('m'));
        }
      //  $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;

        $cleaners = [
            'department_id'=>$theRequest['department_id'],
            'girles'=>$theRequest['girles'],
            'boys'=>$theRequest['boys']
        ];

        $items = [
            'item_id'=>$theRequest['item_id'],
            'count'=>$theRequest['count'],
            'price'=>$theRequest['price']
        ];


        $sumItems = [];
            foreach ($request->count as $key => $value) {
                $sumItems[] = $request->price[$key] * $value;
            }
            $theRequest['total_price'] = ($theRequest['price_per_cleaner'] * (array_sum($theRequest['boys']) + array_sum($theRequest['girles']))) + array_sum($sumItems);

            $theRequest['items'] = $items;
        $theRequest['cleaners'] = $cleaners;
        $quotation = Quotations::create($theRequest);


        if ($quotation)
            return $this->respondCreated($quotation);
        else {
            return $this->json(false,__('Can\'t Add New Quotation'));
        }
    }
    public function updateQuotation(Request $request)
    {        $theRequest = $request->only(['quotation_id','client_id', 'description', 'price_per_cleaner', 'name','phone','address', 'department_id', 'girles', 'boys', 'item_id', 'count', 'price']);
        $validation = [
            'quotation_id'                       =>'required|exists:quotations,id',
            'description'                        => 'nullable|min:5',
            'price_per_cleaner'                  => 'nullable|numeric',
            // 'status'                          =>'required',
            'department_id'                      => 'nullable|array',
            'department_id.*'                    => 'nullable|exists:departments,id',
            'girles'                             => 'nullable|array',
            //  'girles.*'                       => 'nullable|numeric',
            'boys'                               => 'nullable|array',
            //   'boys.*'                        => 'numeric|required_if:girls,==,',
        ];

        if ($request->client_type == 'existClient') {
            $validation['client_id'] = 'required|exists:clients,id';
        }
        elseif ($request->client_type == 'newClient') {
            $validation['name'] = 'required|min:5';
            $validation['phone'] = 'required|numeric|min:8';
            $validation['address'] = 'required|min:5';
        }
        if ($request->girles){
            foreach ($request->girles as $key => $value) {
                if ($request->girles[$key] == 0 && $request->boys[$key] == 0) {
                    $validation['boys.*'] = 'required|numeric';
                    $validation['girles.*'] = 'nullable|numeric';
                }
            }
        }

        if($request->item){
            $validation['price.*'] = 'required|numeric';
            $validation['count.*'] = 'required|numeric';
        }

        // $this->validate($request,$validation);

        $validator=  Validator::make($theRequest,$validation);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        if($request->file){
            $theRequest['file'] = $request->file->store('quotations/'.date('y').'/'.date('m'));
        }

        $theRequest = $request->all();
        if($request->file){
            $theRequest['file'] = $request->file->store('quotations/'.date('y').'/'.date('m'));
        }



       // $theRequest['staff_id']  = Auth::id();
        if ($request->has('department_id')) {
            $cleaners = [
                'department_id' => $theRequest['department_id'],
                'girles' => $theRequest['girles'],
                'boys' => $theRequest['boys']
            ];
            $items = [
                'item_id' => $theRequest['item_id'],
                'count' => $theRequest['count'],
                'price' => $theRequest['price']
            ];
            $sumItems = [];
            foreach ($request->count as $key => $value) {
                $sumItems[] = $request->price[$key] * $value;
            }
            //  dd(array_sum($sumItems));
            $theRequest['total_price'] = ($theRequest['price_per_cleaner'] * (array_sum($theRequest['boys']) + array_sum($theRequest['girles'])) + array_sum($sumItems));
            $theRequest['cleaners'] = $cleaners;
            $theRequest['items'] = $items;


        }

        $quotation = Quotations::where('id','=',$request->quotation_id)->first();
            $columnToUpdate =  array_filter($theRequest);
            $updated = $quotation->update($columnToUpdate);


        if ($updated) {
            $quotationTransformer = new QuotationTransformer();
            return $this->json(true,__('Update One Quotation'),$quotationTransformer->transform($quotation));
        }
        else {
            return $this->json(false,__('Can\'t Update this Quotation'));
        }
    }

        public function deleteQuotation(Request $request){
        $RequestData = $request->only('quotation_id');
        $validator = Validator::make($RequestData, [
            'quotation_id' => 'required|exists:quotations,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (Quotations::where('id',$request->quotation_id)->delete())
            return $this->json(true,__('Quotation Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }
    // this is for creating Quotation .
    public function clientsAndDepartmentsAndItems(){
            $item = Item::get(['id','name']);
            $department = Department::get(['id','name']);
            $clients = Client::get(['id','name']);
            $data = [];
            $data['items'] = $item;
            $data['departments'] = $department;
            $data['clients'] = $clients;
            return $this->json(true,__('Items,Clients and Departments for creating Quotation'),$data);
    }

}