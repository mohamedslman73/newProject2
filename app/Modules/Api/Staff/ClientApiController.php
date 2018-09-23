<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientTypes;
use App\Models\Staff;
use App\Modules\Api\StaffTransformers\ClientReportTransformer;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ClientApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function clients(Request $request)
    {
//        if (!staffCan('system.client.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

            $eloquentData = Client::select([
                'clients.id',
                'clients.name',
                'clients.email',
                'clients.status',
                'clients.staff_id',
                'clients.phone',
                'clients.mobile',
                'clients.address',
                'clients.organization_name',
                'clients.created_at',
                'clients.client_type_id',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
            ])

                ->join('staff', 'staff.id', '=', 'clients.staff_id')
                ->with(['client_types'=>function($type){
                    $type->select(['id','name']);
                }]);

            whereBetween($eloquentData, 'DATE(clients.created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('clients.id', '=', $request->id);
            }
            if ($request->name) {
                $eloquentData->where('clients.name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->organization_name) {
                $eloquentData->where('clients.organization_name', 'LIKE', '%'.$request->organization_name.'%');
            }

            if ($request->status) {
                $eloquentData->where('clients.status', '=', $request->status);
            }
            if ($request->staff_id) {
                $eloquentData->where('clients.staff_id', '=', $request->staff_id);
            }
            if ($request->client_type_id) {
                $eloquentData->where('clients.client_type_id', '=', $request->client_type_id );
            }
            if ($request->mobile) {
                $eloquentData->where('clients.mobile', '=', $request->mobile);
            }
            if ($request->phone) {
                $eloquentData->where('clients.phone', '=', $request->phone);
            }

        $clienttTransformer = new ClientTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Client Available'));
            }
                $clients = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $client_types = ClientTypes::get(['id','name']);
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $clienttTransformer->staff = $staff;
        $allData = $clienttTransformer->transformCollection($clients->toArray());
        $allData['staff'] = $staff;
        $allData['client_types'] = $client_types;
        return $this->json(true, __('Clients'),$allData);

    }
    public function oneClient(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('client_id');
        $validator = Validator::make($RequestData, [
            'client_id' => 'required|exists:clients,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $eloquentData = Client::select([
            'clients.id',
            'clients.name',
            'clients.email',
            'clients.status',
            'clients.staff_id',
            'clients.phone',
            'clients.mobile',
            'clients.address',
            'clients.organization_name',
            'clients.created_at',
            'clients.client_type_id',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
            'clients.init_credit',
        ])
            ->join('staff', 'staff.id', '=', 'clients.staff_id')

        ->where('clients.id',$request->client_id)
            ->with(['client_types'=>function($type){
                $type->select(['id','name']);
            }])
            ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        //$eloquentData->total_orders = $eloquentData->client_order()->sum('total_price');

        $eloquentData->Total_Orders_Amount = $eloquentData->client_order()->sum('total_price');
        $eloquentData->Total_Paid = $eloquentData->client_revenue()->sum('amount');
        $eloquentData->Num_Orders = $eloquentData->client_order()->count('id');
        $eloquentData->Num_Orders_back = $eloquentData->client_order_back()->count('id');
        $eloquentData->Rest = ($eloquentData->init_credit +$eloquentData->total_orders) - $eloquentData->total_revenue;

        $clientTransforrmer = new ClientTransformer();
        return $this->json(true,__('One Client'),$clientTransforrmer->transform($eloquentData));

    }
    public function deleletClient(Request $request){
        $RequestData = $request->only('client_id');
        $validator = Validator::make($RequestData, [
            'client_id' => 'required|exists:clients,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (Client::where('id',$request->client_id)->delete())
            return $this->json(true,__('Client Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }

    public function clientReport(Request $request){

            $eloquentData = Client::select([
                'clients.id',
                'clients.name',
                'clients.init_credit',
                'clients.phone',
                'clients.mobile',

            ])->with([
                'client_order'=>function($q) use ($request){

                    $q->selectRaw("id,client_id, SUM(total_price) as sum_total_order")->groupBy('client_id');
                    whereBetween($q, 'DATE(client_order.created_at)', $request->date1, $request->date2);
                },'client_revenue'=>function($q) use ($request){
//                    $q->select(['id','client_id',DB::raw("SUM(amount) as sum_total_order_back")]);
                    $q->selectRaw("id,client_id, SUM(amount) as sum_total_client_revenue")->groupBy('client_id');
                    whereBetween($q, 'DATE(revenues.created_at)', $request->date1, $request->date2);

                }
                ,'client_order_back'=>function($q) use ($request){
//                    $q->select(['id',DB::raw("SUM(total_price) as sum_total_order_back")]);
                    $q->selectRaw("id,client_id, SUM(total_price) as sum_total_client_order_back")->groupBy('client_id');
                    whereBetween($q, 'DATE(client_order_back.created_at)', $request->date1, $request->date2);

                }
            ]);
            if ($request->name) {
                $eloquentData->where('clients.name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->client_id) {
                $eloquentData->where('clients.id', '=',  $request->client_id);
            }

        $clienttTransformer = new ClientReportTransformer();

        if (empty($eloquentData->first())){
            return $this->json(false,__('No Client Available'));
        }
        $clients = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();


        $allData = $clienttTransformer->transformCollection($clients->toArray());
        $clients = Client::get(['id','name']);
        $allData['clients'] = $clients;
        return $this->json(true, __('Clients'),$allData);

    }
//    public function clientCreditDetails(Request $request){
//
//        whereBetween($eloquentData, 'DATE(clients_orders.created_at)', $request->client_order_created_at1, $request->client_order_created_at2);
//        whereBetween($eloquentData, 'DATE(revenues.created_at)', $request->revenue_created_at1, $request->revenue_created_at2);
//
//        $eloquentData = Client::where('id',$request->client_id)
//            ->select('id')
//            ->with([
//                'client_order'=>function($q){
//                    $q->selectRaw("*,'client_order' as type ");
//                },'client_order_back'=>function($q){
//                    $q->selectRaw("*,'client_order_back' as type ");
//                },'client_revenue'=>function($q){
//                    $q->selectRaw("*,'client_revenue' as type ");
//                } ])->first()->toArray();
//        $array = array_merge($eloquentData['client_order'],$eloquentData['client_order_back']);
//        $all = array_merge($array,$eloquentData['client_revenue']);
//
//        foreach ($all as $key => $row) {
//            $orderByDate[$key] = strtotime($row ['created_at'] );
//        }
//
//        array_multisort($orderByDate, SORT_ASC, $all);
//        dd($eloquentData);
//
////        $this->viewData['tableColumns'] = $all;
////        $this->viewData['client'] = $eloquentData;
////        $this->viewData['breadcrumb'][] = [
////            'text' => __('Client Credit Details')
////        ];
//
//     //   return $this->view('clients.client-credit-details', $this->viewData);
//    }

}