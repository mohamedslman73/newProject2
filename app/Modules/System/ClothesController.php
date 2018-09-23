<?php

namespace App\Modules\System;

use App\Models\ClientTypes;
use App\Models\Clothe;
use App\Models\ItemCategories;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ClothesController extends SystemController
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
            $eloquentData = Clothe::select([
                'id',
                'name',
                'staff_id',
                'created_at'
            ]);
            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }


            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('staff_id', function($data){
                    return '<a href="'.route("system.staff.show",$data->staff_id).'" target="_blank">'.$data->staff->Fullname.'</a>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.clothes.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.clothes.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.clothes.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Clothes')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Clothes');
            } else {
                $this->viewData['pageTitle'] = __('Clothes');
            }

            return $this->view('clothes.index', $this->viewData);
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
            'text' => __('Clothes'),
            'url' => route('system.clothes.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Clothe'),
        ];

        $this->viewData['pageTitle'] = __('Create Clothe');
        return $this->view('clothes.create', $this->viewData);

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
        ]);
        $theRequest = [];
        $theRequest = $request->only([
            'name',
        ]);


        $theRequest['staff_id'] = Auth::id();
        $clientTypes = Clothe::create($theRequest);
        if ($clientTypes)
            return redirect()
                ->route('system.clothes.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.clothes.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add clothe'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Clothe $clothes)
    {
        //dd($supplier_category);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Clothe'),
                'url' => route('system.clothes.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Clothe';
        $this->viewData['result'] = $clothes;
        return $this->view('clothes.show', $this->viewData);
    }

    public function edit(Clothe $clothes)
    {

        $this->viewData['breadcrumb'][] = [
            'text' => __('Clothes'),
            'url' => route('system.clothes.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Clothes'),
        ];


        $this->viewData['pageTitle'] = __('Edit Clothes');
        $this->viewData['result'] = $clothes;

        return $this->view('clothes.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Clothe $clothes)
    {
        ;
        $this->validate($request,[
            'name' =>'required',
        ]);
        $theRequest = $request->only([
            'name',
        ]);
        if ($clothes->update($theRequest)) {

            return redirect()
                ->route('system.clothes.edit', $clothes->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Clothe'));
        }
        else {
            return redirect()
                ->route('system.clothes.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Clothes'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Clothe $clothes)
    {
        $clothes->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('This clothe has been deleted successfully')];
        } else {
            redirect()
                ->route('system.clothes.index')
                ->with('status', 'success')
                ->with('msg', __('This clothe has been deleted'));
        }
    }
}
