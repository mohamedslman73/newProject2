<?php

namespace App\Modules\Api\Staff;

use App\Models\Contract;
use App\Models\Project;
use App\Modules\Api\StaffTransformers\ContractTransformer;
use function foo\func;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Validator;
use App\Models\Department;
use App\Models\Staff;
use App\Modules\Api\StaffTransformers\DepartmentTransformer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ContractApiController extends StaffApiController
{
    public function __construct(){
//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
        // $this->middleware('auth:ApiStaff')->except(['login']);

    }
    public function contracts(Request $request)
    {
        $eloquentData = Contract::select([
            'id',
            'project_id',
            'date_from',
            'date_to',
            'staff_id',
            'created_at',
            ])
            ->with(['staff'=>function($q){
                $q->select(['id',DB::Raw("CONCAT(firstname,'',lastname)as staff_name")]);
        },'project'=>function($project){
               $project->select(['id','name']);
            }]);


        whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
        whereBetween($eloquentData, 'DATE(date_from)', $request->date_from1, $request->date_from2);
        whereBetween($eloquentData, 'DATE(date_to)', $request->date_to1, $request->date_to2);

        if ($request->id) {
            $eloquentData->where('id', '=', $request->id);
        }
        if ($request->project_id) {
            $eloquentData->where('project_id', '=',  $request->project_id);
        }
        if ($request->staff_id) {
            $eloquentData->where('buses.staff_id', '=', $request->staff_id);
        }
        $Transformer = new ContractTransformer();

        if (empty($eloquentData->first())){
            return $this->json(false,__('No Contracts  Available'));
        }
            $department = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
            $project = Project::get(['id','name']);
            $Transformer->staff = $staff;
            $Transformer->project = $project;
            $allData = $Transformer->transformCollection($department->toArray());
            $allData['staff'] = $staff;
            return $this->json(true, __('Contracts'),$allData);
        }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function oneContract(Request $request)
    {
        $RequestData = $request->only('contract_id');
        $validator = Validator::make($RequestData, [
            'contract_id' => 'required|exists:contract,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        $eloquentData = Contract::select([
            'id',
            'project_id',
            'date_from',
            'date_to',
            'staff_id',
            'created_at',
        ])
            ->with(['staff'=>function($q){
                $q->select(['id',DB::Raw("CONCAT(firstname,'',lastname)as staff_name")]);
            },'project'=>function($project){
                $project->select(['id','name']);
            }])
            ->where('id',$request->contract_id)
        ->first();
        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $Transforrmer = new ContractTransformer();
        $allData =  $Transforrmer->transform($eloquentData);
        return $this->json(true,__('One Contract'),$allData);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createContract(Request $request)
    {
        $theRequest = $request->only([
            'project_id',
            'description',
            'file',
            'date_from',
            'date_to',

        ]);
        $validator = Validator::make($theRequest, [
            'project_id'             =>'required|exists:projects,id',
            'description'            =>'nullable',
            'file'                   =>'nullable|file',
            'date_from'              =>'required|before_or_equal:date_to',
            'date_to'                =>'required|after_or_equal:data_from',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if($request->file){
            $theRequest['file'] = $request->file->store('Contract/'.date('y').'/'.date('m'));
        }

       // $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $contract = Contract::create($theRequest);
        if ($contract)
            return $this->respondCreated($contract);
        else {
            return $this->json(false,__('Can\'t Add new Contract'));
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function updateContract(Request $request)
    {

        $theRequest = $request->only([
            'contract_id',
            'project_id',
            'description',
            'file',
            'date_from',
            'date_to',

        ]);
       // dd($theRequest);
        $validator = Validator::make($theRequest, [
            'contract_id'            => 'required|exists:contract,id',
            'project_id'             =>'nullable|exists:projects,id',
            'description'            =>'nullable',
            'file'                   =>'nullable|file',
            'date_from'              =>'nullable|before_or_equal:date_to',
            'date_to'                =>'nullable|after_or_equal:data_from',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
      }
      //  dd($theRequest);
        if($request->file){
            $theRequest['file'] = $request->file->store('Contract/'.date('y').'/'.date('m'));
        }
        $contract = Contract::where('id',$request->contract_id)->first();
        $columnToUpdate =  array_filter($theRequest);
        $updated = $contract->update($columnToUpdate);

        if ($updated) {
            $Transformer = new ContractTransformer();
          return $this->json(true,__('Contract Updated Successfully'),$Transformer->transform($contract));
        }
        else {
            return $this->json(false,__('No Result'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function deleteContract(Request $request)
    {
        $theRequest = $request->only([
            'contract_id',
        ]);
        $validator = Validator::make($theRequest,[
            'contract_id' =>'required|exists:contract,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (Contract::where('id',$request->contract_id)->delete())
            return $this->json(true,__('This Contract Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }
    /*
     * This Api for Creating Contract projects
     */
    public function projects()
    {
        return $this->json(true,__('Projects'),Project::get(['id','name']));
    }
}
