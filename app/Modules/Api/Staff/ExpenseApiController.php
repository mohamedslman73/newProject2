<?php

namespace App\Modules\Api\Staff;

use App\Models\ClientTypes;
use App\Models\Expense;
use App\Models\ExpenseCauses;
use App\Models\ItemCategories;
use App\Models\Staff;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use App\Modules\Api\Staff\StaffApiController;
use App\Modules\Api\StaffTransformers\ExpenseCausesTransformer;
use App\Modules\Api\StaffTransformers\ExpenseTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;

class ExpenseApiController extends StaffApiController
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
    public function expense(Request $request)
    {
        //        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = Expense::select([
            'expenses.id',
            'expenses.expense_causes_id',
            'expenses.date',
            'expenses.amount',
            'expenses.description',
            'expenses.supplier_id',
            'expenses.staff_id',
            'expenses.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'expenses.staff_id')
            ->with(['expense_causes'=>function($expense_causes){
                $expense_causes->select(['id','name']);
            },'supplier'=>function($supplier){
                $supplier->select(['id','name']);
            }]);

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

        $Transformer = new ExpenseTransformer();

        if (empty($eloquentData->first())){
            return $this->json(false,__('No Expense  Available'));
        }
        $expense = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();


        $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $expenseCauses = ExpenseCauses::get(['id','name']);
        $Transformer->staff = $staff;
        $allData = $Transformer->transformCollection($expense->toArray());
        $allData['staff'] = $staff;
        $allData['expense_causes'] = $expenseCauses;
        return $this->json(true, __('Expenses'),$allData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function oneExpense(Request $request)
    {
        $RequestData = $request->only('expense_id');
        $validator = Validator::make($RequestData, [
            'expense_id' => 'required|exists:expenses,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $eloquentData = Expense::select([
            'expenses.id',
            'expenses.expense_causes_id',
            'expenses.date',
            'expenses.amount',
            'expenses.description',
            'expenses.supplier_id',
            'expenses.staff_id',
            'expenses.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'expenses.staff_id')
            ->with(['expense_causes'=>function($expense_causes){
                $expense_causes->select(['id','name']);
            },'supplier'=>function($supplier){
                $supplier->select(['id','name']);
            }])
            ->where('expenses.id',$request->expense_id)
        ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $Transforrmer = new ExpenseTransformer();
        return $this->json(true,__('One Expense'),$Transforrmer->transform($eloquentData));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createExpense(Request $request)
    {
        $theRequest = $request->only([
            'expense_causes_id',
            'date',
            'amount',
            'description',
            'supplier_id'
        ]);
        $validation = [
            'expense_causes_id' =>'required|exists:expense_causes,id',
            'date' =>'required|date',
            'amount' =>'required|numeric',
        ];
        if ($request->expense_causes_id == 1){
            $validation['supplier_id'] = 'required|exists:suppliers,id';
        }

        $validator =   Validator::make($theRequest,$validation);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        //$theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $expense = Expense::create($theRequest);
        if ($expense)
            return $this->respondCreated($expense);
        else {
            return $this->json(false,__('Can\'t Add New Expense'));
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function updateExpense(Request $request)
    {
        $theRequest = $request->only([
            'expense_id',
            'expense_causes_id',
            'date',
            'amount',
            'description',
            'supplier_id'
        ]);
        $validation = [
            'expense_id' =>'required|exists:expenses,id',
            'expense_causes_id' =>'nullable|exists:expense_causes,id',
            'date' =>'nullable|date',
            'amount' =>'nullable|numeric',
        ];
        if ($request->expense_causes_id == 1){
            $validation['supplier_id'] = 'nullable|exists:suppliers,id';
        }

        $validator = Validator::make($theRequest,$validation);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $expense = Expense::where('id',$request->expense_id)->first();


            $columnToUpdate =  array_filter($theRequest);
            $updated = $expense->update($columnToUpdate);

        if ($updated) {
            $Transformer = new ExpenseTransformer();
            return $this->json(true,__('One Expense  Updated'),$Transformer->transform($expense));
        }
        else {
            return $this->json(false,__('Can\'t Update this Expense '));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function deleteExpense(Request $request)
    {
        $theRequest = $request->only([
            'expense_id',
        ]);
        $validator = Validator::make($theRequest,[
            'expense_id' =>'required|exists:expenses,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
       if (Expense::where('id',$request->expense_id)->delete())
         return $this->json(true,__('This Expense Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }
}
