<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientOrderItems;
use App\Models\ClientOrders;
use App\Models\ClientTypes;
use App\Models\Item;
use App\Models\Project;
use App\Models\Staff;
use App\Modules\Api\StaffTransformers\ClientOrdersTransformer;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



class ClientOrdersApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function clientOrders(Request $request)
    {
//        if (!staffCan('system.client.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = ClientOrders::select([
            'id',
            'client_id',
            'project_id',
            'date',
            'total_price',
            'staff_id',
            'description',
            'created_at',
        ])
        ->with(['client'=>function($client){
            $client->select(['id','name as client_name']);
        },'project'=>function($project){
            $project->select(['id','name as project_name']);
        }]);



        whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'DATE(date)', $request->date1, $request->date2);
        whereBetween($eloquentData, 'total_price', $request->price1, $request->price2);

        if ($request->id) {
            $eloquentData->where('id', '=', $request->id);
        }

        if ($request->type) {
            $eloquentData->where($request->type, '!=', $request->type);
        }

        if ($request->client_id) {
            $eloquentData->where('client_id', '=', $request->client_id);
        }

        if ($request->project_id) {
            $eloquentData->where('project_id', '=', $request->project_id);
        }

        if ($request->staff_id) {
            $eloquentData->where('staff_id', '=', $request->staff_id);
        }

        $clienttTransformer = new ClientOrdersTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Client Orders Available'));
            }
                $clients = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $client = Client::get(['id','name']);
            $prohects = Project::get(['id','name']);
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $clienttTransformer->staff = $staff;
        $allData = $clienttTransformer->transformCollection($clients->toArray());
        $allData['staff'] = $staff;
        $allData['clients'] = $client;
        $allData['projects'] = $prohects;
        return $this->json(true, __('Clients'),$allData);

    }
    public function oneClientOrder(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $RequestData = $request->only('order_id');
        $validator = Validator::make($RequestData, [
            'order_id' => 'required|exists:client_orders,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $eloquentData = ClientOrders::select([
            'client_orders.id',
            'client_orders.client_id',
            'client_orders.project_id',
            'client_orders.date',
            'client_orders.plus',
            'client_orders.minus',
            'client_orders.total_price',
            'client_orders.description',
            'client_orders.staff_id',
            'client_orders.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as created_by"),
        ])
            ->join('staff', 'staff.id', '=', 'client_orders.staff_id')

        ->where('client_orders.id',$request->order_id)
            ->with(['client'=>function($client){
                $client->select(['id','name as client_name']);
            },'project'=>function($project){
                $project->select(['id','name as project_name']);
            },'client_order_items.item'=>function($item){
                $item->select(['id','name']);
            }])
            ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $clientTransforrmer = new ClientOrdersTransformer();
        return $this->json(true,__('One Client Order'),$clientTransforrmer->transform($eloquentData));

    }
    public function deleletClientOrder(Request $request){
        $RequestData = $request->only('order_id');
        $validator = Validator::make($RequestData, [
            'order_id' => 'required|exists:client_orders,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (ClientOrders::where('id',$request->order_id)->delete())
            return $this->json(true,__('Client Order Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }

    public function clientsProjectsItems()
    {
        $projects = Project::get(['id','name']);
        $clients = Client::get(['id','name']);
        $items = Item::get(['id','name']);
        $data=[];
        $data['projects'] = $projects;
        $data['clients'] = $clients;
        $data['items'] = $items;
        return $this->json(true,__('Projects,clients and Items'),$data);
    }

    public function createClientOrder(Request $request)
    {
        $clientOrderData = $request->only(['client_id','project_id','type','date','minus','plus','total']);

      $validator=  Validator::make($clientOrderData,[

          $request->type.'_id'     =>'required|exists:'.$request->type.'s,id',
            // 'project_id'                          =>'required|exists:projects,id',
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


        $handleItemsCount = [];
        foreach ($request->item_id as $key=>$row){
            $handleItemsCount[$row] = $request->count[$key];
        }
        foreach ($request->item_id as $key=>$row){
            $item = Item::find($row);
            if($item->count < $handleItemsCount[$row]){
                return $this->json(false,__('Sorry Count Of '.$item->name.' Not Enough'));
            }
        }
        $clientOrderData['staff_id']  = 1;
        //$clientOrderData['staff_id']  = Auth::id();
        $clientOrder = ClientOrders::create($clientOrderData);
        if ($clientOrder) {

            $items = [];
            $sumTotal =0;
            foreach ($request->item_id as $key=>$value){
                $items['item_id'] = $value;
                $items['client_order_id'] = $clientOrder->id;
                $items['count'] = $request->count[$key];
                $items['price'] = $request->price[$key];
                $sumTotal += $request->count[$key]*$request->price[$key];
                $client_item = ClientOrderItems::create($items);
                $client_item->item->update(['count'=>$client_item->item->count - $request->count[$key] ]);
            }
            if ($request->has('minus')){
                $sumTotal -= $request->minus;
            }
            if ($request->has('plus')){
                $sumTotal += $request->plus;
            }

            $clientOrder->update(['total_price'=>$sumTotal]);
            return $this->respondCreated($clientOrder);
        }else{
            return $this->json(false,__('can\'t Create Client Order'));

        }
    }
    public function updateClientOrder(Request $request )
    {

        $theRequest  = $request->only(['client_order_id','client_id','project_id','type','date','minus','plus','total']);

        $validator=  Validator::make($theRequest,[
            'client_order_id' =>'required|exists:client_orders,id',
            $request->type.'_id'     =>'nullable|exists:'.$request->type.'s,id',
            // 'project_id'                          =>'required|exists:projects,id',
            'date'                                 =>'nullable|date',
            'item_id'                              =>'array',
            'item_id.*'                            =>'nullable|exists:items,id',
            'count'                                =>'array',
            'count.*'                              =>'nullable|numeric',
            'price'                                =>'array',
            'price.*'                              =>'nullable|numeric',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $clientOrder = ClientOrders::where('id',$request->client_order_id)->first();

    foreach ($theRequest as $key=>$value){
       $columnToUpdate =  array_filter($theRequest);
         $updated = $clientOrder->update($columnToUpdate);
    }

    if ($updated) {
        if ($request->has('item_id')) {
        $var = array_column($clientOrder->client_order_items()->get()->toArray(), 'item_id');

        $sumTotal = 0;
        $items = [];
        $clientOrder->client_order_items()->delete();
        foreach ($request->item_id as $key => $value) {
            $items['item_id'] = $value;
            $items['supplier_order_id'] = $clientOrder->id;
            $items['count'] = $request->count[$key];
            $items['price'] = $request->price[$key];
            $sumTotal += $request->count[$key] * $request->price[$key];
            $clientOrder->client_order_items()->create($items);

        }
        if ($request->has('minus')) {
            $sumTotal -= $request->minus;
        }
        if ($request->has('plus')) {
            $sumTotal += $request->plus;
        }

        $clientOrder->update(['total_price' => $sumTotal]);
    }
        $clientTransforrmer = new ClientOrdersTransformer();
        return $this->json(true,__('One Client Order Updated'),$clientTransforrmer->transform($clientOrder));
}
        else {
            return $this->json(false,__('Can\'t Update this Row'));
        }
    }

}