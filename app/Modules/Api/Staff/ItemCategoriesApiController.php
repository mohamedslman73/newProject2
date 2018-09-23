<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientTypes;
use App\Models\ItemCategories;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use App\Modules\Api\StaffTransformers\ItemCategoriesTransformer;
use App\Modules\Api\StaffTransformers\QuotationTransformer;
use App\Modules\Api\StaffTransformers\SupplierReportTransformer;
use App\Modules\Api\StaffTransformers\SupplierTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ItemCategoriesApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function itemCategories(Request $request)
    {
//        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = ItemCategories::select([
            'item_categories.id',
            'item_categories.name',
            'item_categories.parent_id',
            'item_categories.status',
            'item_categories.staff_id',
            'item_categories.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'item_categories.staff_id')
        ->with(['parent'=>function($q){
            $q->select(['id','name']);
        }]);

        whereBetween($eloquentData, 'DATE(item_categories.created_at)', $request->created_at1, $request->created_at2);

        if ($request->id) {
            $eloquentData->where('item_categories.id', '=', $request->id);
        }

        if ($request->parent_id) {
            $eloquentData->where('item_categories.parent_id', '=', $request->parent_id);
        }

        if ($request->name) {
            $eloquentData->where('item_categories.name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->staff_id) {
            $eloquentData->where('item_categories.staff_id', '=', $request->staff_id);
        }

        if ($request->status) {
            $eloquentData->where('item_categories.status', '=', $request->status);
        }
        if ($request->description) {
            $eloquentData->where('item_categories.description', 'LIKE', '%' . $request->description . '%');
        }

        $Transformer = new ItemCategoriesTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Item Categories Available'));
            }
                $item_categories = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $Transformer->staff = $staff;
        $allData = $Transformer->transformCollection($item_categories->toArray());
        $allData['staff'] = $staff;

        return $this->json(true, __('Item Categories'),$allData);

    }
    public function oneItemCategories(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('item_category_id');
        $validator = Validator::make($RequestData, [
            'item_category_id' => 'required|exists:item_categories,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $eloquentData = ItemCategories::select([
            'item_categories.id',
            'item_categories.name',
            'item_categories.parent_id',
            'item_categories.status',
            'item_categories.staff_id',
            'item_categories.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'item_categories.staff_id')
            ->with(['parent'=>function($q){
                $q->select(['id','name']);
            }])
        ->where('item_categories.id',$request->item_category_id)
            ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $Transforrmer = new QuotationTransformer();
        return $this->json(true,__('One Item Category'),$Transforrmer->transform($eloquentData));

    }
    public function createItemCategories(Request $request)
    {
        $theRequest = $request->only([
            'name',
            'description',
            'status',
            'parent_id'
        ]);

        $validator=  Validator::make($request->all(),[
            'name' =>'required',
            'description' =>'nullable',
            'status' =>'required|in:active,in-active',
            'parent_id' =>'nullable||exists:item_categories,id'
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $itemCategories = ItemCategories::create($theRequest);
      //  $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        if ($itemCategories)
            return $this->respondCreated($itemCategories);
        else {
            return $this->json(false,__('Can\'t Add New Item Category'));
        }
    }
    public function updateItemCategories(Request $request)
    {
        $theRequest = $request->all();
        $validator=  Validator::make($theRequest,[
            'item_category_id' =>'required|exists:item_categories,id',
           // 'name' =>'required',
            'description' =>'nullable',
            'status' =>'nullable|in:active,in-active',
            'parent_id' =>'nullable||exists:item_categories,id'
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $updated = false;
        $itemCategories = ItemCategories::where('id',$request->item_category_id)->first();
        if ($itemCategories)
            $columnToUpdate =  array_filter($theRequest);
            $updated = $itemCategories->update($columnToUpdate);

        if ($updated) {
            $supplierTransformer = new ItemCategoriesTransformer();
            return $this->json(true,__('Update Item Category'),$supplierTransformer->transform($itemCategories));
        }
        else {
            return $this->json(false,__('Can\'t Update this Item Category'));
        }
    }

    public function deleteItemCategories(Request $request){
    $RequestData = $request->only('item_category_id');
    $validator = Validator::make($RequestData, [
        'item_category_id' => 'required|exists:item_categories,id',
    ]);
    if ($validator->errors()->any()) {
        return $this->ValidationError($validator, __('Validation Error'));
    }
    if (ItemCategories::where('id',$request->item_category_id)->delete())
        return $this->json(true,__('Item Category Deleted Successfully'));
    return $this->json(false,__('No Results'));
}


}