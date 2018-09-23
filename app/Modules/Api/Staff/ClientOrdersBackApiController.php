<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientOrderBack;
use App\Models\ClientOrderBackItem;
use App\Models\ClientOrderItems;
use App\Models\ClientOrders;
use App\Models\ClientTypes;
use App\Models\Item;
use App\Models\Project;
use App\Models\Staff;
use App\Modules\Api\StaffTransformers\ClientOrdersBackTransformer;
use App\Modules\Api\StaffTransformers\ClientOrdersTransformer;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



class ClientOrdersBackApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function clientOrdersBack(Request $request)
    {
//        if (!staffCan('system.client.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = ClientOrderBack::select([
            'id',
            'client_order_id',
            'client_id',
            'date',
            'total_price',
            'staff_id',
            'created_at',
        ])
        ->with(['client'=>function($client){
            $client->select(['id','name as client_name']);
        },'staff'=>function($staff){
            $staff->select(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name")]);
        }]);



        whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'DATE(date)', $request->date1, $request->date2);
        whereBetween($eloquentData, 'total_price', $request->price1, $request->price2);

        if ($request->id) {
            $eloquentData->where('id', '=', $request->id);
        }

        if ($request->client_order_id) {
            $eloquentData->where('client_order_id', '=', $request->client_order_id);
        }

        if ($request->client_id) {
            $eloquentData->where('client_id', '=', $request->client_id);
        }

        if ($request->staff_id) {
            $eloquentData->where('staff_id', '=', $request->staff_id);
        }
        $clienttTransformer = new ClientOrdersBackTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Client Orders Back Available'));
            }
                $clients = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $client = Client::get(['id','name']);
            $ClientOrdersIds = ClientOrders::get(['id']);
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $clienttTransformer->staff = $staff;
        $allData = $clienttTransformer->transformCollection($clients->toArray());
        $allData['staff'] = $staff;
        $allData['clients'] = $client;
        $allData['clientOrdersIds'] = $ClientOrdersIds;
        return $this->json(true, __('Clients Orders Back'),$allData);

    }
    public function oneClientOrderBack(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $RequestData = $request->only('client_order_back_id');
        $validator = Validator::make($RequestData, [
            'client_order_back_id' => 'required|exists:client_order_back,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $eloquentData = ClientOrderBack::select([
            'id',
            'client_order_id',
            'client_id',
            'date',
            'total_price',
            'staff_id',
            'created_at',
        ])
            ->with(['client'=>function($client){
                $client->select(['id','name as client_name']);
            },'staff'=>function($staff){
                $staff->select(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name")]);
            }])
            ->where('id',$request->client_order_back_id)
            ->first();


        $items = ClientOrderBackItem::where('client_order_back_id','=',$request->client_order_back_id)->get();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $clientTransforrmer = new ClientTransformer();


        $allData = $clientTransforrmer->transform($eloquentData);
        $allData['order_back_items'] = $items;

        return $this->json(true,__('One Client Order Back'),$allData);

    }
    public function deleteClientOrderBack(Request $request){
        $RequestData = $request->only('client_order_back_id');
        $validator = Validator::make($RequestData, [
            'client_order_back_id' => 'required|exists:client_order_back,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        //dd(ClientOrderBack::where('id','=',$request->client_order_back)->first()->toArray());

        if (ClientOrderBack::where('id','=',$request->client_order_back_id)->delete())
            return $this->json(true,__('Client Order Back Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }

    public function clientsAndClientIdsItems()
    {
        $clients = Client::get(['id','name']);
        $client_order_ids = ClientOrders::get(['id']);
        $items = Item::get(['id','name']);
        $data=[];
        $data['clients'] = $clients;
        $data['items'] = $items;
        $data['client_order_ids'] = $client_order_ids;
        return $this->json(true,__('Projects,clients and Items'),$data);
    }

//    public function createClientOrder(Request $request)
//    {
//        $clientOrderData = $request->only(['client_id','project_id','type','date','minus','plus','total']);
//
//      $validator=  Validator::make($clientOrderData,[
//
//          $request->type.'_id'     =>'required|exists:'.$request->type.'s,id',
//            // 'project_id'                          =>'required|exists:projects,id',
//            'date'                                 =>'required',
//            'item_id'                              =>'array',
//            'item_id.*'                            =>'required|exists:items,id',
//            'count'                                =>'array',
//            'count.*'                              =>'nullable|numeric',
//            'price'                                =>'array',
//            'price.*'                              =>'nullable|numeric',
//        ]);
//        if ($validator->errors()->any()) {
//            return $this->ValidationError($validator, __('Validation Error'));
//        }
//
//
//        $handleItemsCount = [];
//        foreach ($request->item_id as $key=>$row){
//            $handleItemsCount[$row] = $request->count[$key];
//        }
//        foreach ($request->item_id as $key=>$row){
//            $item = Item::find($row);
//            if($item->count < $handleItemsCount[$row]){
//                return $this->json(false,__('Sorry Count Of '.$item->name.' Not Enough'));
//            }
//        }
//        $clientOrderData['staff_id']  = 1;
//        //$clientOrderData['staff_id']  = Auth::id();
//        $clientOrder = ClientOrders::create($clientOrderData);
//        if ($clientOrder) {
//
//            $items = [];
//            $sumTotal =0;
//            foreach ($request->item_id as $key=>$value){
//                $items['item_id'] = $value;
//                $items['client_order_id'] = $clientOrder->id;
//                $items['count'] = $request->count[$key];
//                $items['price'] = $request->price[$key];
//                $sumTotal += $request->count[$key]*$request->price[$key];
//                $client_item = ClientOrderItems::create($items);
//                $client_item->item->update(['count'=>$client_item->item->count - $request->count[$key] ]);
//            }
//            if ($request->has('minus')){
//                $sumTotal -= $request->minus;
//            }
//            if ($request->has('plus')){
//                $sumTotal += $request->plus;
//            }
//
//            $clientOrder->update(['total_price'=>$sumTotal]);
//            return $this->respondCreated($clientOrder);
//        }else{
//            return $this->json(false,__('can\'t Create Client Order'));
//
//        }
//    }


    public function createClientOrderBack(Request $request)
    {
        //dd($request->all());
        $clientOrderData = $request->only(['client_id','date','client_order_id','total']);

        $validator=  Validator::make($clientOrderData,[

            'client_id'                          =>'required|exists:clients,id',
            'client_order_id'                          =>'required|exists:client_orders,id',
            'date'                                 =>'required',
            'item_id'                              =>'array',
            'item_id.*'                            =>'required|exists:items,id',
            'count'                                =>'array',
            'count.*'                              =>'nullable|numeric',
            'price'                                =>'array',
            'price.*'                              =>'nullable|numeric',
        ]);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

       // $clientOrderData['staff_id']  = Auth::id();
        $clientOrderData['staff_id']  = 1;
        $clientOrder = ClientOrderBack::create($clientOrderData);

        if ($clientOrder) {

            $items = [];
            $sumTotal =0;
            foreach ($request->item_id as $key=>$value){
                $items['item_id'] = $value;
                $items['client_order_back_id'] = $clientOrder->id;
                $items['count'] = $request->count[$key];
                $items['price'] = $request->price[$key];
                $sumTotal += $request->count[$key]*$request->price[$key];
                $client_item = ClientOrderBackItem::create($items);

                $client_item->item->update(['count'=>$client_item->item->count + $request->count[$key] ]);
            }
            $clientOrder->update(['total_price'=>$sumTotal]);
            return $this->respondCreated($clientOrder);
        } else {
            return $this->json(false,__('can\'t Create Client Order Back'));
        }
    }

}