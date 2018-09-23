<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;
use App\Models\Item;
use App\Models\ItemCategories;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ItemCategoriesController extends SystemController
{
    public function __construct(){
        parent::__construct();
        $this->viewData['breadcrumb'] = [
            [
                'text'=> __('Home'),
                'url'=> url('system'),
            ]
        ];
    }
    public function index(Request $request)
    {

        if ($request->isDataTable) {
            $eloquentData = ItemCategories::select([
                'item_categories.id',
                'item_categories.name',
                'item_categories.parent_id',
                'item_categories.status',
                'item_categories.staff_id',
                'item_categories.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'item_categories.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

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

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('parent',function ($data){
                    if($data->parent_id)
                    return '<a target="_blank" href="'.route("system.category.show",$data->parent_id).'" target="_blank">'.$data->parent->name.'</a>';
                    else
                        return '--';
                })
                ->addColumn('status', '{{$status}}')
                 ->addColumn('created_at', function ($data) {
                    return date('Y-m-d H:i a',strtotime($data->created_at));
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.category.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.category.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.category.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('Parent'), __('Status'),  __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Item Category')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Item Categories');
            } else {
                $this->viewData['pageTitle'] = __('Item Categories');
            }

            $this->viewData['itemCategory'] = ItemCategories::get();
            return $this->view('item-category.index', $this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text' => __('Item Category'),
            'url' => route('system.category.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Item Category'),
        ];

        $this->viewData['itemCategory']     = ItemCategories::get();
        $this->viewData['pageTitle'] = __('Create Item Category');
        return $this->view('item-category.create', $this->viewData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
           'name' =>'required',
           'description' =>'nullable',
           'status' =>'required',
           'parent_id' =>'nullable|numeric'
        ]);
        $theRequest = [];
        $theRequest = $request->only([
            'name',
            'description',
            'status',
            'parent_id'
        ]);


        $theRequest['staff_id'] = Auth::id();
        $marketingMessage = ItemCategories::create($theRequest);
        if ($marketingMessage)
            return redirect()
                ->route('system.category.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.category.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Item category'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(ItemCategories $category,Request $request)
    {


        // list of items of this item Category.


        if ($request->isDataTable) {
            $eloquentData = Item::where('item_category_id', $category->id)
                ->select([
                    'items.id',
                    'items.name',
                    'items.description',
                    'items.status',
                    'items.staff_id',
                    'items.min_count',
                    'items.item_category_id',
                    'items.created_at',
                ]);

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

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


            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('description', function ($data) {
                    return str_limit($data->description, 25);
                })
//                ->addColumn('status', '{{$status}}')
              //  ->addColumn('count', '{{$count}}')
                ->addColumn('min_count', '{{$min_count}}')
//                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:iA');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.item.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.item.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.item.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->addColumn('status', function ($data) {
                    if ($data->status == 'in-active') {
                        return 'tr-danger';
                    }
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'),
                __('Name'),
                __('Description'),
                __('Min Count'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Items')
            ];


            $this->viewData['breadcrumb'] = [
                [
                    'text' => __('Home'),
                    'url' => url('system'),
                ],
                [
                    'text' => __('Item Category'),
                    'url' => route('system.category.index'),
                ],
                [
                    'text' => 'Show',
                ]
            ];
//
//
            $this->viewData['pageTitle'] = 'Items';
            $this->viewData['result'] = $category;
            $this->viewData['lang'] = \DataLanguage::get();
            $this->viewData['itemCount'] = Item::where('item_category_id', $category->id)->count();

            return $this->view('item-category.show', $this->viewData);
        }
    }

    public function edit(ItemCategories $category)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Marketing Message'),
            'url' => route('system.category.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Item Category'),
        ];

        $this->viewData['itemCategory']     = ItemCategories::get();
        $this->viewData['pageTitle'] = __('Edit Item Category');
        $this->viewData['result'] = $category;

        return $this->view('item-category.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,ItemCategories $category)
    {
        $this->validate($request,[
            'name' =>'required',
            'description' =>'nullable',
            'status' =>'required',
            'parent_id' =>'nullable|numeric'
        ]);
        $theRequest = $request->only([
            'name',
            'description',
            'status',
            'parent_id',
        ]);
        if ($category->update($theRequest)) {
          //  dd($category);
            return redirect()
                ->route('system.category.edit', $category->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Marketing Message'));
        }
        else {
            return redirect()
                ->route('system.category.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Marketing Message'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ItemCategories $category)
    {
        $category->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Item Category has been deleted successfully')];
        } else {
            redirect()
                ->route('system.category.index')
                ->with('status', 'success')
                ->with('msg', __('This Item Category has been deleted'));
        }
    }
}
