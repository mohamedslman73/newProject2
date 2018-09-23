<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientTypes;
use App\Models\Item;
use App\Models\ItemCategories;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use App\Modules\Api\StaffTransformers\ItemCategoriesTransformer;
use App\Modules\Api\StaffTransformers\ItemTransformer;
use App\Modules\Api\StaffTransformers\QuotationTransformer;
use App\Modules\Api\StaffTransformers\SupplierReportTransformer;
use App\Modules\Api\StaffTransformers\SupplierTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ItemsApiController extends StaffApiController
{

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
        // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function items(Request $request)
    {
//        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $eloquentData = Item::select([
            'items.id',
            'items.name',
            'items.description',
            'items.status',
            'items.staff_id',
            'items.count',
            'items.min_count',
            'items.item_category_id',
            'items.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'items.staff_id')
            ->with(['item_categories' => function ($q) {
                $q->select(['id', 'name']);
            }]);

        whereBetween($eloquentData, 'DATE(items.created_at)', $request->created_at1, $request->created_at2);

        if ($request->id) {
            $eloquentData->where('items.id', '=', $request->id);
        }

        if ($request->name) {
            $eloquentData->where('items.name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->staff_id) {
            $eloquentData->where('items.staff_id', '=', $request->staff_id);
        }
        if ($request->item_category_id) {
            $eloquentData->where('items.item_category_id', '=', $request->item_category_id);
        }
        if ($request->status) {
            $eloquentData->where('items.status', '=', $request->status);
        }
        if ($request->description) {
            $eloquentData->where('items.description', 'LIKE', '%' . $request->description . '%');
        }
        $Transformer = new ItemTransformer();

        if (empty($eloquentData->first())) {
            return $this->json(false, __('No Item Categories Available'));
        }
        $items = $eloquentData->orderBy('created_at', 'DESC')->jsonPaginate();

        $staff = Staff::select(['id', \DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $item_categories = ItemCategories::get(['id', 'name']);

        $allData = $Transformer->transformCollection($items->toArray());
        $allData['staff'] = $staff;
        $allData['item_categories'] = $item_categories;

        return $this->json(true, __('Item Categories'), $allData);

    }

    public function oneItem(Request $request)
    {
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('item_id');
        $validator = Validator::make($RequestData, [
            'item_id' => 'required|exists:items,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $eloquentData = Item::select([
            'items.id',
            'items.name',
            'items.description',
            'items.status',
            'items.staff_id',
            'items.count',
            'items.min_count',
            'items.item_category_id',
            'items.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'items.staff_id')
            ->with(['item_categories' => function ($q) {
                $q->select(['id', 'name']);
            }])
            ->where('items.id', $request->item_id)
            ->first();

        if (empty($eloquentData))
            return $this->json(false, __('No Results'));
        $Transforrmer = new ItemTransformer();
        return $this->json(true, __('One Item'), $Transforrmer->transform($eloquentData));

    }

    public function createItem(Request $request)
    {
        $theRequest = $request->only([
            'name',
            'description',
            'status',
            'code',
            'item_category_id',
            'unite',
            'image',
            'price',
            'count',
            'min_count',
        ]);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            // 'description'             =>'required',
            'status' => 'required|in:active,in-active',
            'code' => 'required|unique:items,code',
            'item_category_id' => 'required|exists:item_categories,id',
            'unite' => 'required',
            'image' => 'nullable|image',
            'price' => 'required',
            //  'count'                   =>'required',
            'min_count' => 'required',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        if ($request->hasFile('image')) {
            $theRequest['image'] = $request->file('image')
                ->store('items/' . date('y') . '/' . date('m'));
        }

        //  $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $item = Item::create($theRequest);
        if ($item)
            return $this->respondCreated($item);
        else {
            return $this->json(false, __('Can\'t Add New Item'));
        }
    }

    public function updateItem(Request $request)
    {
        $theRequest = $request->only([
            'name',
            'description',
            'status',
            'code',
            'item_category_id',
            'unite',
            'image',
            'price',
            'count',
            'min_count',
        ]);
        $validator = Validator::make($request->all(), [
            'item_id'                        => 'required|exists:items,id',
            'status'                         => 'nullable|in:active,in-active',
            'code'                           => 'nullable|unique:items,code',
            'item_category_id'               => 'nullable|exists:item_categories,id',
            'image'                          => 'nullable|image',
            'price'                          =>'nullable|numeric',
             'count'                         =>'nullable|numeric',
             'min_count'                     =>'nullable|numeric',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        if ($request->hasFile('image')) {
            $theRequest['image'] = $request->file('image')
                ->store('items/' . date('y') . '/' . date('m'));
        }
        $item = Item::where('id', $request->item_id)->first();
        if ($item)
                $columnToUpdate = array_filter($theRequest);
                $updated = $item->update($columnToUpdate);

        if ($updated) {
            $Transformer = new ItemTransformer();
            return $this->json(true, __('Update Item Category'), $Transformer->transform($item));
        } else {
            return $this->json(false, __('Can\'t Update this Item'));
        }
    }

    public function deleteItem(Request $request)
    {
        $RequestData = $request->only('item_id');
        $validator = Validator::make($RequestData, [
            'item_id' => 'required|exists:items,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (Item::where('id', $request->item_id)->delete())
            return $this->json(true, __('Item Deleted Successfully'));
        return $this->json(false, __('No Results'));
    }

    public function itemCategories()
    {


    return  $this->AllCategoriesWithItemsdd();
        return $this->json(true, __('Item Categories For Creating Items'), ItemCategories::get(['id', 'name']));
    }


    function AllCategoriesWithItemsdd($parent_id = 0)
    {
        $out = [];
        $categories = ItemCategories::select(['id', 'name', 'parent_id'])->where(['parent_id' => $parent_id])->get();

        if (!empty($categories)) {
            foreach ($categories as $key => $category) {
                $out['c_name'] = $category->name;
                $items['items'] = $category->items;
                if (!empty($items)) {
                    $categories[$key]['items'] = $items;

                    foreach ($items['items']as $row) {

                        $out['name'] = $row->name;
                        $out['cost'] = $row->cost;
                        $out['count'] = $row->count;
                        $out['total'] = $row->cost * $row->count;
                        $out['price'] = $row->price;
                        $out['total_price'] = $row->price * $row->count;
                        $out['status'] = $row->status;
                    }

                }
                //dd($category);
                $categories[$key]['sub'] = $this->AllCategoriesWithItemsdd($category->id);
               // dd(  $categories[$key]['sub']);
                if (!empty($categories[$key]['sub'])) {

                    $out['sub'] = $categories[$key]['sub'];
                }
            }
        }
return $out;

    }
}