<?php

namespace App\Modules\System;

use App\Models\ClientTypes;
use App\Models\Expense;
use App\Models\ExpenseCauses;
use App\Models\ItemCategories;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ExpenseController extends SystemController
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
            $eloquentData = Expense::select([
                'expenses.id',
                'expenses.expense_causes_id',
                'expenses.date',
                'expenses.amount',
                'expenses.description',
                'expenses.staff_id',
                'expenses.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'expenses.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(expenses.created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(expenses.date)', $request->date1, $request->date2);
            whereBetween($eloquentData, 'expenses.amount', $request->amount1, $request->amount2);

            if ($request->id) {
                $eloquentData->where('expenses.id', '=', $request->id);
            }
            if ($request->staff_id) {
                $eloquentData->where('expenses.staff_id', '=', $request->staff_id);
            }

            if ($request->expense_causes_id) {
                $eloquentData->where('expenses.expense_causes_id', '=', $request->expense_causes_id);
            }
            if ($request->description) {
                $eloquentData->where('expenses.description', 'LIKE','%'. $request->description. '%');
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
                    if ($data->expense_causes_id){
                        return str_limit($data->expense_causes->name,25);
                    }
                    return '--';
                })
                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d H:iA');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.expenses.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.expenses.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.expenses.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
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
                __('Expense Causes'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Expense')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Expense');
            } else {
                $this->viewData['pageTitle'] = __('Expense');
            }
            $return = [];
            $data = ExpenseCauses::get(['id', 'name']);
            foreach ($data as $key => $value) {
                $return[$value->id] = $value->name;
            }
            $this->viewData['expense_causes'] = $return;
            return $this->view('expense.index', $this->viewData);
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
            'text' => __(' Expense'),
            'url' => route('system.expenses.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Expense'),
        ];

        $return = [];
        $data = ExpenseCauses::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['expense_causes'] = $return;
        $this->viewData['pageTitle'] = __('Create Expense');
        return $this->view('expense.create', $this->viewData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // dd($request->all());
       $validation = [
           'expense_causes_id' =>'required|exists:expense_causes,id',
           'date' =>'required|date',
           'amount' =>'required|numeric',
       ];
        if ($request->expense_causes_id == 1){
            $validation['supplier_id'] = 'required|exists:suppliers,id';
        }
        $this->validate($request,$validation);

        $theRequest = $request->only([
            'expense_causes_id',
            'date',
            'amount',
            'description',
            'supplier_id'
        ]);


        $theRequest['staff_id'] = Auth::id();
        $expenses = Expense::create($theRequest);
        if ($expenses)
            return redirect()
                ->route('system.expenses.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.expenses.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Expense'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Expense $expense)
    {
//        dd($expense);

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Expense'),
                'url' => route('system.expenses.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Expense';
        $this->viewData['result'] = $expense;
        return $this->view('expense.show', $this->viewData);
    }

    public function edit(Expense $expense)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Expense'),
            'url' => route('system.expenses.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Expense'),
        ];
        $return = [];
        $data = ExpenseCauses::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['expense_causes'] = $return;

        $this->viewData['pageTitle'] = __('Edit Expense');
        $this->viewData['result'] = $expense;

        return $this->view('expense.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Expense $expense)
    {
        $validation = [
            'expense_causes_id' =>'required|exists:expense_causes,id',
            'date' =>'required|date',
            'amount' =>'required|numeric',
        ];
        if ($request->expense_causes_id == 1){
            $validation['supplier_id'] = 'required|exists:suppliers,id';
        }
        $this->validate($request,$validation);

        $theRequest = $request->only([
            'expense_causes_id',
            'date',
            'amount',
            'description',
            'supplier_id'
        ]);
        if ($expense->update($theRequest)) {

            return redirect()
                ->route('system.expenses.edit', $expense->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Expense'));
        }
        else {
            return redirect()
                ->route('system.expenses.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Expense'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Expense $expense)
    {
        $expense->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Expense has been deleted successfully')];
        } else {
            redirect()
                ->route('system.expenses.index')
                ->with('status', 'success')
                ->with('msg', __('This Expense Causes has been deleted'));
        }
    }
}
