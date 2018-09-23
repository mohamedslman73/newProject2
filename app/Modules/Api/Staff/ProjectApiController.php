<?php

namespace App\Modules\Api\Staff;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\ClientOrders;
use App\Models\ClientTypes;
use App\Models\Contract;
use App\Models\Department;
use App\Models\Expense;
use App\Models\ExpenseCauses;
use App\Models\ItemCategories;
use App\Models\Project;
use App\Models\ProjectCleaners;
use App\Models\Staff;
use App\Models\SupplierCategories;
use App\Models\VacationTypes;
use App\Modules\Api\Staff\StaffApiController;
use App\Modules\Api\StaffTransformers\ExpenseCausesTransformer;
use App\Modules\Api\StaffTransformers\ExpenseTransformer;
use App\Modules\Api\StaffTransformers\ProjectTransformer;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Facades\Datatables;

class ProjectApiController extends StaffApiController
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
    public function projects(Request $request)
    {
        //        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = Project::select(['projects.*',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),])
            ->join('staff', 'staff.id', '=', 'projects.staff_id')
            ->with(['client'=>function($client){
                $client->select(['id','name']);
            }]);


        whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);

        if ($request->id) {
            $eloquentData->where('id', '=', $request->id);
        }

        if ($request->name) {
            $eloquentData->where('name', 'LIKE', '%' . $request->name . '%');
        }
        if ($request->staff_id) {
            $eloquentData->where('staff_id', '=', $request->staff_id);
        }
        if ($request->client_id) {
            $eloquentData->where('client_id', '=', $request->client_id);
        }
        if ($request->status) {
            $eloquentData->where('status', '=', $request->status);
        }

        $Transformer = new ProjectTransformer();

        if (empty($eloquentData->first())){
            return $this->json(false,__('No Projects  Available'));
        }
        $expense = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();


        $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
        $clients = Client::get(['id','name']);
        $Transformer->staff = $staff;
        $allData = $Transformer->transformCollection($expense->toArray());
        $allData['staff'] = $staff;
        $allData['clients'] = $clients;
        return $this->json(true, __('Projects'),$allData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function oneProject(Request $request)
    {
        $RequestData = $request->only('project_id');
        $validator = Validator::make($RequestData, [
            'project_id' => 'required|exists:projects,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $eloquentData = Project::select(['projects.*',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),])
            ->join('staff', 'staff.id', '=', 'projects.staff_id')
            ->with(['client'=>function($client){
                $client->select(['id','name']);
            }])
        ->where('projects.id',$request->project_id)
        ->first();


        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $Transforrmer = new ProjectTransformer();
        $allData =  $Transforrmer->transform($eloquentData);
        $contract = Contract::where('project_id',$request->project_id)

            ->get();
        $projectCleaners = ProjectCleaners::where('project_id',$request->project_id)
            ->with(['cleaner'=>function($cleaner){
                $cleaner->select(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as cleaner_name")]);
            },'department'=>function($department){
                $department->select(['id','name']);
            }])
            ->get();
        $orders = ClientOrders::where('project_id',$request->project_id)->get();
        $attendance = Attendance::where('project_id',$request->project_id)->groupBy('date')->get();
        $allData['contract'] = $contract;
        $allData['projectCleaners'] = $projectCleaners;
        $allData['attendance'] = $attendance;
        $allData['orders'] = $orders;
        return $this->json(true,__('One Project'),$allData);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createProject(Request $request)
    {
        $theRequest = $request->only([
            'name',
            'client_id',
            'status',

        ]);

        $validator =   Validator::make($theRequest,[
            'name' =>'required',
            'client_id' =>'required|exists:clients,id',
            'status' =>'required|in:in-progress,in-active,hold'
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        //$theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $project = Project::create($theRequest);
        if ($project)
            return $this->respondCreated($project);
        else {
            return $this->json(false,__('Can\'t Add New Project'));
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function updateProject(Request $request)
    {
        $theRequest = $request->only([
            'name',
            'client_id',
            'status',
            'project_id'
        ]);
        $validator =   Validator::make($theRequest,[
           // 'name' =>'required',
            'project_id' =>'required|exists:projects,id',
            'client_id' =>'nullable|exists:clients,id',
            'status' =>'nullable|in:in-progress,in-active,hold'
        ]);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $project = Project::where('id',$request->project_id)->first();
            $columnToUpdate =  array_filter($theRequest);
            $updated = $project->update($columnToUpdate);

        if ($updated) {
            $Transformer = new ProjectTransformer();
            return $this->json(true,__('One Project Updated'),$Transformer->transform($project));
        }
        else {
            return $this->json(false,__('Can\'t Update this Project '));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function deleteProject(Request $request)
    {
        $theRequest = $request->only([
            'project_id',
        ]);
        $validator = Validator::make($theRequest,[
            'project_id' =>'required|exists:projects,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
       if (Project::where('id',$request->project_id)->delete())
         return $this->json(true,__('This Project Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }


    public function addProjectCleaners(Request $request){

        $validator = Validator::make($request->toArray(), [
            'department_id' =>'required|exists:department,id',
            'cleaner_id' =>'required|exists:staff,id',
            'project_id' =>'required|exists:projects,id',
        ]);

        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $check = ProjectCleaners::where(['cleaner_id'=>$request->cleaner_id,'project_id'=>$request->project_id]);

        if($check->first()){
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $theRequest = $request->only(['department_id','cleaner_id','project_id']);

        $add = ProjectCleaners::create($theRequest);
        if($add){
           // return ['status'=>true,'msg'=>__('Add Successfuly')];
            return $this->json(true,__('Cleaner Added Successfully'),$add);
        }else{
            return $this->json(false,__('Sorry Cannot add Cleaners'));
        }

    }
    public function DepartmentsAndCleaners()
    {
        $data = [];
        $department = Department::get(['id','name']);
        $cleaners = Staff::get(['id',\DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as cleaner_name")]);
        $data['department'] = $department;
        $data['cleaners'] = $cleaners;
        return $this->json(true,__('Departments And Cleaners'),$data);
    }
}
