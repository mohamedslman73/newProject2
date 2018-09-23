<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientTypes;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use App\Modules\Api\StaffTransformers\SupplierReportTransformer;
use App\Modules\Api\StaffTransformers\SupplierTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function Symfony\Component\Debug\Tests\FatalErrorHandler\test_namespaced_function;


class SupplierApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function suppliers(Request $request)
    {
//        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = Supplier::select([
            'suppliers.id',
            'suppliers.name',
            'suppliers.email',
            'suppliers.description',
            'suppliers.status',
            'suppliers.staff_id',
            'suppliers.phone1',
            'suppliers.phone2',
            'suppliers.phone3',
            'suppliers.mobile1',
            'suppliers.mobile2',
            'suppliers.mobile3',
            'suppliers.address',
            'suppliers.company_name',
            'suppliers.created_at',
            'suppliers.supplier_category_id',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'suppliers.staff_id')
                ->with(['supplier_categories'=>function($type){
                    $type->select(['id','name']);
                }]);



        whereBetween($eloquentData, 'DATE(suppliers.created_at)', $request->created_at1, $request->created_at2);

        if ($request->id) {
            $eloquentData->where('suppliers.id', '=', $request->id);
        }

        if ($request->name) {
            $eloquentData->where('suppliers.name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->status) {
            $eloquentData->where('suppliers.status', '=', $request->status);
        }
        if ($request->staff_id) {
            $eloquentData->where('suppliers.staff_id', '=', $request->staff_id);
        }
        if ($request->supplier_category_id) {
            //dd($request->supplier_category_id);
            $eloquentData->where('suppliers.supplier_category_id', '=', $request->supplier_category_id);
        }
        if ($request->description) {
            $eloquentData->where('suppliers.description', 'LIKE', '%' . $request->description . '%');
        }
        if ($request->mobile) {
            $eloquentData->where('suppliers.mobile1', '=', $request->mobile)
                ->orWhere('suppliers.mobile2', '=', $request->mobile)
                ->orWhere('suppliers.mobile3', '=', $request->mobile);
        }
        if ($request->phone) {
            $eloquentData->where('suppliers.phone1', '=', $request->phone)
                ->orWhere('suppliers.phone2', '=', $request->phone)
                ->orWhere('suppliers.phone3', '=', $request->phone);
        }

        $supplierTransformer = new SupplierTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Supplier Available'));
            }
                $clients = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $supplier_category = SupplierCategories::get(['id','name']);
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $supplierTransformer->staff = $staff;
        $allData = $supplierTransformer->transformCollection($clients->toArray());
        $allData['staff'] = $staff;
        $allData['supplier_category'] = $supplier_category;
        return $this->json(true, __('Suppliers'),$allData);

    }
    public function oneSupplier(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('supplier_id');
        $validator = Validator::make($RequestData, [
            'supplier_id' => 'required|exists:suppliers,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $eloquentData = Supplier::select([
            'suppliers.id',
            'suppliers.name',
            'suppliers.email',
            'suppliers.description',
            'suppliers.status',
            'suppliers.staff_id',
            'suppliers.init_credit',
            'suppliers.phone1',
            'suppliers.phone2',
            'suppliers.phone3',
            'suppliers.mobile1',
            'suppliers.mobile2',
            'suppliers.mobile3',
            'suppliers.address',
            'suppliers.company_name',
            'suppliers.created_at',
            'suppliers.supplier_category_id',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'suppliers.staff_id')
            ->with(['supplier_categories'=>function($type){
                $type->select(['id','name']);
            }])
        ->where('suppliers.id',$request->supplier_id)
            ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));

        $eloquentData->total_orders = $eloquentData->supplier_order()->sum('total_price');

        $eloquentData->Total_Orders_Amount = $eloquentData->supplier_order()->sum('total_price');
        $eloquentData->Total_expence= $eloquentData->supplier_expence()->sum('amount');
        $eloquentData->Num_Orders = $eloquentData->supplier_order()->count('id');
        $eloquentData->Num_Orders_back = $eloquentData->supplier_order_back()->count('id');
        $eloquentData->Rest = ($eloquentData->Total_Orders_Amount +$eloquentData->init_credit) - $eloquentData->Total_expence;

        $supplierTransforrmer = new SupplierTransformer();
        return $this->json(true,__('One Supplier'),$supplierTransforrmer->transform($eloquentData));

    }
    public function createSupplier(Request $request)
    {
        // ask for validation of init_credit if it required or not.
        $validator=  Validator::make($request->all(),[
            'name'                        =>'required',
            // 'description'                 =>'required',
            'status'                      =>'required|in:active,in-active',
            'supplier_category_id'        =>'required|exists:supplier_categories,id',
            'company_name'                =>'required',
            'email'                      =>'required|email',
            'address'                     =>'required|min:5',
            'phone1'                      =>'required|numeric',
            'phone2'                      =>'nullable|numeric',
            'phone3'                      =>'nullable|numeric',
            'mobile1'                     =>'required|numeric',
            'mobile2'                     =>'nullable|numeric',
            'mobile3'                     =>'nullable|numeric',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }



        $theRequest = $request->all();
        $theRequest['mobile1'] = $request->mobile1;
        if (!empty($request->mobile2)){
            $theRequest['mobile2'] = $request->mobile2;
        }
        if (!empty($request->mobile3)){
            $theRequest['mobile3'] = $request->mobile3;
        }

        $theRequest['phone1'] = $request->phone1;
        if (!empty($request->phone2)){
            $theRequest['phone2'] = $request->phone2;
        }
        if (!empty($request->phone3)){
            $theRequest['phone3'] = $request->phone3;
        }

      //  $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $supplier = Supplier::create($theRequest);
        if ($supplier)
            return $this->respondCreated($supplier);
        else {
            return $this->json(false,__('Can\'t Add New Supplier'));
        }
    }
    public function updateSupplier(Request $request)
    {
        $theRequest = $request->all();
        $validator=  Validator::make($theRequest,[
            'supplier_id' =>'required|exists:suppliers,id',
         //   'name'                        =>'required',
            'status'                      =>'nullable|in:active,in-active',
            'supplier_category_id'        =>'nullable|exists:supplier_categories,id',
            'company_name'                =>'nullable|min:5',
            'email'                      =>'nullable|email',
            'address'                     =>'nullable|min:5',
            'phone1'                      =>'nullable|numeric',
            'mobile1'                     =>'nullable|numeric',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }


        $supplier = Supplier::where('id',$request->supplier_id)->first();
            $columnToUpdate =  array_filter($theRequest);
            $updated = $supplier->update($columnToUpdate);

        if ($updated) {
            $supplierTransformer = new SupplierTransformer();
            return $this->json(true,__('One Supplier Updated Successfully'),$supplierTransformer->transform($supplier));
        }
        else {
            return $this->json(false,__('Can\'t Update this Row'));
        }
    }

        public function deleteSupplier(Request $request){
        $RequestData = $request->only('supplier_id');
        $validator = Validator::make($RequestData, [
            'supplier_id' => 'required|exists:suppliers,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (Supplier::where('id',$request->supplier_id)->delete())
            return $this->json(true,__('Supplier Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }
    public function supplierCategories()
    {
        return $this->json(true,__('Supplier Categories'),SupplierCategories::get(['id','name']));
    }
    public function supplierReport(Request $request){



            $eloquentData = Supplier::select([
                'suppliers.id',
                'suppliers.name',
                'suppliers.init_credit',
                'suppliers.description',
                'suppliers.company_name',

            ])->with([
                'supplier_order'=>function($q) use ($request){
                    $q->selectRaw("id,supplier_id, SUM(total_price) as sum_total_order")->groupBy('supplier_id');
                    whereBetween($q, 'DATE(supplier_order.created_at)', $request->date1, $request->date2);
                },'supplier_expence'=>function($q) use ($request){
                    $q->selectRaw("id,supplier_id, SUM(amount) as sum_total_supplier_expence")->groupBy('supplier_id');
                    whereBetween($q, 'DATE(expenses.created_at)', $request->date1, $request->date2);

                }
                ,'supplier_order_back'=>function($q) use ($request){
                    $q->selectRaw("id,supplier_id, SUM(total_price) as sum_total_supplier_order_back")->groupBy('supplier_id');
                    whereBetween($q, 'DATE(supplier_order_back.created_at)', $request->date1, $request->date2);

                }
            ] );
//            whereBetween($eloquentData, 'total_price', $request->total_order_price1, $request->total_order_price2);
            if ($request->name) {
                $eloquentData->where('suppliers.name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->supplier_id) {
                $eloquentData->where('suppliers.id', '=',  $request->supplier_id);
            }



            $supplierTransformer = new SupplierReportTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Client Available'));
            }
            $clients = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();


            $allData = $supplierTransformer->transformCollection($clients->toArray());
            $suppliers = Supplier::get(['id','name']);
            $allData['suppliers'] = $suppliers;

            return $this->json(true, __('Suppliers Report'),$allData);
    }
//    public function suppliersForFilter()
//    {
//        return $this->json(true,__('Suppliers'),Supplier::get(['id','name']));
//    }

}