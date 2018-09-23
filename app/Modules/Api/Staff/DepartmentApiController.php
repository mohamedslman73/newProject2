<?php

namespace App\Modules\Api\Staff;

use \Illuminate\Support\Facades\Validator;
use App\Models\Department;
use App\Models\Staff;
use App\Modules\Api\StaffTransformers\DepartmentTransformer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DepartmentApiController extends StaffApiController
{
    public function __construct(){
//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
        // $this->middleware('auth:ApiStaff')->except(['login']);

    }
    public function department(Request $request)
    {
            $eloquentData = Department::with(['staff'=>function($staff){
                $staff->select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")]);
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
        $Transformer = new DepartmentTransformer();

        if (empty($eloquentData->first())){
            return $this->json(false,__('No Projects  Available'));
        }
        $department = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();

            $Transformer->staff = $staff;
            $allData = $Transformer->transformCollection($department->toArray());
            $allData['staff'] = $staff;
            return $this->json(true, __('Departments'),$allData);
        }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function oneDepartment(Request $request)
//    {
//        $RequestData = $request->only('department_id');
//        $validator = Validator::make($RequestData, [
//            'department_id' => 'required|exists:department,id',
//        ]);
//        if ($validator->errors()->any()) {
//            return $this->ValidationError($validator, __('Validation Error'));
//        }
//        $eloquentData = Department::with(['staff'=>function($staff){
//            $staff->select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")]);
//        }])
//            ->where('id',$request->department_id)
//        ->first();
//        if(empty($eloquentData))
//            return $this->json(false,__('No Results'));
//        $Transforrmer = new DepartmentTransformer();
//        $allData =  $Transforrmer->transform($eloquentData);
//        return $this->json(true,__('One Department'),$allData);
//
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createDepartment(Request $request)
    {
        $theRequest = $request->only([
            'name',

        ]);
        $validator = Validator::make($theRequest, [
            'name' => 'required',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

       // $theRequest['staff_id'] = Auth::id();
        $theRequest['staff_id'] = 1;
        $department = Department::create($theRequest);
        if ($department)
            return $this->respondCreated($department);
        else {
            return $this->json(false,__('Can\'t Add new Department'));
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function updateDepartment(Request $request)
    {

        $theRequest = $request->only([
            'name',
            'department_id'
        ]);
       // dd($theRequest);
        $validator = Validator::make($theRequest, [
            'department_id' => 'required|exists:department,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
      }
      //  dd($theRequest);
        $department = Department::where('id',$request->department_id)->first();
        $columnToUpdate =  array_filter($theRequest);
        $updated = $department->update($columnToUpdate);

        if ($updated) {
            $Transformer = new DepartmentTransformer();
          return $this->json(true,__('Department Updated Successfully'),$Transformer->transform($department));
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
    public function deleteDepartment(Request $request)
    {
        $theRequest = $request->only([
            'department_id',
        ]);
        $validator = Validator::make($theRequest,[
            'department_id' =>'required|exists:department,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }
        if (Department::where('id',$request->department_id)->delete())
            return $this->json(true,__('This Department Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }
}
