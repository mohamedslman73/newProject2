<?php

namespace App\Modules\System;

use App\Models\ClientTypes;
use App\Models\ExpenseCauses;
use App\Models\ItemCategories;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ExpenseCausesController extends SystemController
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
            $eloquentData = ExpenseCauses::select([
                'expense_causes.id',
                'expense_causes.name',
                'expense_causes.description',
                'expense_causes.staff_id',
                'expense_causes.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'expense_causes.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(expense_causes.created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('expense_causes.id', '=', $request->id);
            }
            if ($request->staff_id) {
                $eloquentData->where('expense_causes.staff_id', '=', $request->staff_id);
            }

            if ($request->name) {
                $eloquentData->where('expense_causes.name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->description) {
                $eloquentData->where('expense_causes.description', 'LIKE','%'. $request->description. '%');
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
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.expense.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.expense.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.expense.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('Description'),  __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Expense Causes')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Expense Causes');
            } else {
                $this->viewData['pageTitle'] = __('Expense Causes');
            }

            return $this->view('expense-causes.index', $this->viewData);
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
            'text' => __(' Expense Causes'),
            'url' => route('system.expense.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Expense Causes'),
        ];

        $this->viewData['pageTitle'] = __('Create Expense Causes');
        return $this->view('expense-causes.create', $this->viewData);

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
        $expenseCauses = ExpenseCauses::create($theRequest);
        if ($expenseCauses)
            return redirect()
                ->route('system.expense.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.expense.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Expense Causes'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(ExpenseCauses $expense)
    {
     //  dd($expense);
        //dd($supplier_category);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Expense Causes'),
                'url' => route('system.expense.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Expense Causes';
        $this->viewData['result'] = $expense;
        return $this->view('expense-causes.show', $this->viewData);
    }

    public function edit(ExpenseCauses $expense)
    {

        $this->viewData['breadcrumb'][] = [
            'text' => __('Expense Causes'),
            'url' => route('system.expense.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Expense Causes'),
        ];


        $this->viewData['pageTitle'] = __('Edit Expense Causes');
        $this->viewData['result'] = $expense;

        return $this->view('expense-causes.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,ExpenseCauses $expense )
    {
        ;
        $this->validate($request,[
            'name' =>'required',
        ]);
        $theRequest = $request->only([
            'name',
            'description'
        ]);
        if ($expense->update($theRequest)) {

            return redirect()
                ->route('system.expense.edit', $expense->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Expense Causes'));
        }
        else {
            return redirect()
                ->route('system.expense.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Expense Causes'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,ExpenseCauses $expense)
    {
        if ($expense->id == 1){
            return ['status' => false, 'msg' => __('This Expense Causes Cant Not be deleted')];
        }
        $expense->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Expense Causes has been deleted successfully')];
        } else {
            redirect()
                ->route('system.expenseCauses.index')
                ->with('status', 'success')
                ->with('msg', __('This Expense Causes has been deleted'));
        }
    }
}
