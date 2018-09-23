<?php

namespace App\Modules\Api\Staff;

use App\Models\ClientTypes;
use App\Models\ExpenseCauses;
use App\Models\ItemCategories;
use App\Models\Staff;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use App\Modules\Api\Staff\StaffApiController;
use App\Modules\Api\StaffTransformers\ExpenseCausesTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;

class ExpenseCausesApiController extends StaffApiController
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
    public function expenseCauses(Request $request)
    {
        //        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

            $eloquentData = ExpenseCauses::select([
                'expense_causes.id',
                'expense_causes.name',
                'expense_causes.description',
                'expense_causes.staff_id',
                'expense_causes.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'expense_causes.staff_id');

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


        $Transformer = new ExpenseCausesTransformer();

        if (empty($eloquentData->first())){
            return $this->json(false,__('No Expense Causes Available'));
        }
        $revenue = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();


        $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
       // $revenueCauses = ProfitCauses::get(['id','name']);
        $Transformer->staff = $staff;
        $allData = $Transformer->transformCollection($revenue->toArray());
        $allData['staff'] = $staff;
       // $allData['revenue_causes'] = $revenueCauses;
        return $this->json(true, __('Expense Causes'),$allData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function oneExpenseCauses(Request $request)
    {
        $RequestData = $request->only('expense_cause_id');
        $validator = Validator::make($RequestData, [
            'expense_cause_id' => 'required|exists:expense_causes,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $eloquentData = ExpenseCauses::select([
            'expense_causes.id',
            'expense_causes.name',
            'expense_causes.description',
            'expense_causes.staff_id',
            'expense_causes.created_at',
            \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
        ])
            ->join('staff', 'staff.id', '=', 'expense_causes.staff_id')
            ->where('expense_causes.id',$request->expense_cause_id)
        ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $Transforrmer = new ExpenseCausesTransformer();
        return $this->json(true,__('One Expense Causes'),$Transforrmer->transform($eloquentData));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createExpenseCauses(Request $request)
    {
        $theRequest = $request->only([
            'name',
            'description',
        ]);
        $validator = Validator::make($theRequest,[
           'name' =>'required',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        //$theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $expenseCauses = ExpenseCauses::create($theRequest);
        if ($expenseCauses)
            return $this->respondCreated($expenseCauses);
        else {
            return $this->json(false,__('Can\'t Add New Expense Causes'));
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function updateExpenseCauses(Request $request)
    {
        $theRequest = $request->only([
            'expense_cause_id',
            'name',
            'description',
        ]);
        $validator = Validator::make($theRequest,[
            'expense_cause_id' =>'required|exists:expense_causes,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $expense = ExpenseCauses::where('id',$request->expense_cause_id)->first();

            $columnToUpdate =  array_filter($theRequest);
            $updated = $expense->update($columnToUpdate);

        if ($updated) {
            $Transformer = new ExpenseCausesTransformer();
            return $this->json(true,__('One Expense Causes Updated'),$Transformer->transform($expense));
        }
        else {
            return $this->json(false,__('Can\'t Update this Expense Causes'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function deleteExpenseCauses(Request $request)
    {
        $theRequest = $request->only([
            'expense_cause_id',
        ]);
        $validator = Validator::make($theRequest,[
            'expense_cause_id' =>'required|exists:expense_causes,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if ($request->expense_cause_id == 1){
            return ['status' => false, 'msg' => __('This Expense Causes Cant Not be deleted')];
        }
       if (ExpenseCauses::where('id',$request->expense_cause_id)->delete())
         return $this->json(true,__('This Expense Causes Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }
}
