<?php

namespace App\Modules\System;

use App\Models\ClientTypes;
use App\Models\Expense;
use App\Models\ExpenseCauses;
use App\Models\ItemCategories;
use App\Models\Profit;
use App\Models\ProfitCauses;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ProfitController extends SystemController
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
            $eloquentData = Profit::select([
                'revenues.id',
                'revenues.revenue_causes_id',
                'revenues.date',
                'revenues.amount',
                'revenues.description',
                'revenues.staff_id',
                'revenues.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'revenues.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(revenues.created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(revenues.date)', $request->date1, $request->date2);
            whereBetween($eloquentData, 'revenues.amount', $request->amount1, $request->amount2);

            if ($request->id) {
                $eloquentData->where('revenues.id', '=', $request->id);
            }
            if ($request->staff_id) {
                $eloquentData->where('revenues.staff_id', '=', $request->staff_id);
            }

            if ($request->expense_causes_id) {
                $eloquentData->where('revenues.revenue_causes_id', '=', $request->expense_causes_id);
            }
            if ($request->description) {
                $eloquentData->where('revenues.description', 'LIKE','%'. $request->description. '%');
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('date', '{{$date}}')
                ->addColumn('amount', function ($data){
                    return amount($data->amount,true);
                })
                ->addColumn('description',function ($data){
                    if ($data->description){
                        return str_limit($data->description,25);
                    }
                    return '--';
                })
                ->addColumn('expense_causes_id',function ($data){
                    if ($data->revenue_causes_id){
                        return str_limit($data->revenue_causes->name,25);
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
                                <li class=\"dropdown-item\"><a href=\"" . route('system.profits.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.profits.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.profits.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Date'),
                __('Amount'),
                __('Description'),
                __('Revenue Causes'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Revenue')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Revenue');
            } else {
                $this->viewData['pageTitle'] = __('Revenue');
            }
            $return = [];
            $data = ProfitCauses::get(['id', 'name']);
            foreach ($data as $key => $value) {
                $return[$value->id] = $value->name;
            }
            $this->viewData['expense_causes'] = $return;
            return $this->view('profit.index', $this->viewData);
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
            'text' => __('Revenue'),
            'url' => route('system.profits.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Revenue'),
        ];

        $return = [];
        $data = ProfitCauses::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['expense_causes'] = $return;
        $this->viewData['pageTitle'] = __('Create Revenue');
        return $this->view('profit.create', $this->viewData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $validation = [
            'revenue_causes_id' =>'required|exists:revenue_causes,id',
            'date' =>'required|date',
            'amount' =>'required|numeric',
        ];
        if ($request->revenue_causes_id == 1){
            $validation['client_id'] = 'required|exists:clients,id';
        }
        $this->validate($request,$validation);

        $theRequest = $request->only([
            'revenue_causes_id',
            'date',
            'amount',
            'description',
            'client_id'
        ]);


        $theRequest['staff_id'] = Auth::id();
      //  dd($theRequest);
        $expenses = Profit::create($theRequest);
        if ($expenses)
            return redirect()
                ->route('system.profits.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.profits.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Revenue'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Profit $profit)
    {
//        dd($expense);

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Revenue'),
                'url' => route('system.profits.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Revenue';
        $this->viewData['result'] = $profit;
        return $this->view('profit.show', $this->viewData);
    }

    public function edit(Profit $profit)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Revenue'),
            'url' => route('system.profits.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Revenue'),
        ];
        $return = [];
        $data = ProfitCauses::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['expense_causes'] = $return;

        $this->viewData['pageTitle'] = __('Edit Revenue');
        $this->viewData['result'] = $profit;

        return $this->view('profit.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Profit $profit)
    {
        $validation = [
            'revenue_causes_id' =>'required|exists:revenue_causes,id',
            'date' =>'required|date',
            'amount' =>'required|numeric',
        ];
        if ($request->revenue_causes_id == 1){
            $validation['client_id'] = 'required|exists:clients,id';
        }
        $this->validate($request,$validation);

        $theRequest = $request->only([
            'revenue_causes_id',
            'date',
            'amount',
            'description',
            'client_id'
        ]);

        if ($profit->update($theRequest)) {

            return redirect()
                ->route('system.profits.edit', $profit->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Revenue'));
        }
        else {
            return redirect()
                ->route('system.profits.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Revenue'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Profit $profit)
    {
        $profit->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Revenue has been deleted successfully')];
        } else {
            redirect()
                ->route('system.profits.index')
                ->with('status', 'success')
                ->with('msg', __('This Revenue has been deleted'));
        }
    }
}
