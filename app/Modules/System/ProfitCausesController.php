<?php

namespace App\Modules\System;

use App\Models\ClientTypes;
use App\Models\ExpenseCauses;
use App\Models\ItemCategories;
use App\Models\ProfitCauses;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ProfitCausesController extends SystemController
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
            $eloquentData = ProfitCauses::select([
                'revenue_causes.id',
                'revenue_causes.name',
                'revenue_causes.description',
                'revenue_causes.staff_id',
                'revenue_causes.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'revenue_causes.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(revenue_causes.created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('revenue_causes.id', '=', $request->id);
            }
            if ($request->staff_id) {
                $eloquentData->where('revenue_causes.staff_id', '=', $request->staff_id);
            }

            if ($request->name) {
                $eloquentData->where('revenue_causes.name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->description) {
                $eloquentData->where('revenue_causes.description', 'LIKE','%'. $request->description. '%');
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('description',function ($data){
                    if ($data->description){
                        return str_limit($data->description,25);
                    }
                    return '--';
                })
                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:ia');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.profit.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.profit.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.profit.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('Description'),  __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Revenue Causes')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Revenue Causes');
            } else {
                $this->viewData['pageTitle'] = __('Revenue Causes');
            }

            return $this->view('profit-causes.index', $this->viewData);
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
            'text' => __('Revenue Causes'),
            'url' => route('system.profit.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Revenue Causes'),
        ];

        $this->viewData['pageTitle'] = __('Create Revenue Causes');
        return $this->view('profit-causes.create', $this->viewData);

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
            'description',
        ]);


        $theRequest['staff_id'] = Auth::id();
        $ProfitCauses = ProfitCauses::create($theRequest);
        if ($ProfitCauses)
            return redirect()
                ->route('system.profit.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.profit.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Revenue Causes'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(ProfitCauses $profit)
    {
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Revenue Causes'),
                'url' => route('system.profit.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Revenue Causes';
        $this->viewData['result'] = $profit;
        return $this->view('profit-causes.show', $this->viewData);
    }

    public function edit(ProfitCauses $profit)
    {

        $this->viewData['breadcrumb'][] = [
            'text' => __('Revenue Causes'),
            'url' => route('system.profit.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Revenue Causes'),
        ];


        $this->viewData['pageTitle'] = __('Edit Revenue Causes');
        $this->viewData['result'] = $profit;

        return $this->view('profit-causes.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,ProfitCauses $profit)
    {
        ;
        $this->validate($request,[
            'name' =>'required',
        ]);
        $theRequest = $request->only([
            'name',
            'description'
        ]);
        if ($profit->update($theRequest)) {

            return redirect()
                ->route('system.profit.edit', $profit->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Profit Causes'));
        }
        else {
            return redirect()
                ->route('system.profit.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Revenue Causes'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,ProfitCauses $profit)
    {
        $profit->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Profit Causes has been deleted successfully')];
        } else {
            redirect()
                ->route('system.profit.index')
                ->with('status', 'success')
                ->with('msg', __('This Revenue Causes has been deleted'));
        }
    }
}
