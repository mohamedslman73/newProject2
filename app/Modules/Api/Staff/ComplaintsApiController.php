<?php
namespace App\Modules\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientTypes;
use App\Models\Complain;
use App\Models\Complaint;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\SupplierCategories;
use App\Modules\Api\StaffTransformers\ClientTransformer;
use App\Modules\Api\StaffTransformers\ComplaintsTransformer;
use App\Modules\Api\StaffTransformers\SupplierReportTransformer;
use App\Modules\Api\StaffTransformers\SupplierTransformer;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ComplaintsApiController extends StaffApiController {

    public function __construct()
    {

//        header("Access-Control-Allow-Origin:*");
//        header("Access-Control-Allow-Credentials: true");
//        header("Access-Control-Allow-Headers: origin, content-type, accept, Set-Cookie");
//        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
//        header('Access-Control-Max-Age: 166400');
    // $this->middleware('auth:ApiStaff')->except(['login']);

    }

    public function complaints(Request $request)
    {
//        if (!staffCan('system.supplier.index', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }

        $eloquentData = Complaint::select([
            'complaints.id',
            'complaints.name',
            'complaints.project_id',
            'complaints.client_id',
            'complaints.status',
            'complaints.details',
            'complaints.staff_id',
            'complaints.created_by_staff_id',
            'complaints.created_at'
        ])
            ->with(['staff'=>function($staff){
                $staff->select(['id',DB::Raw("CONCAT(firstname,'',lastname) as complaint_of_staff")]);
            },'created_by_staff'=>function($created_by_staff){
                $created_by_staff->select(['id',DB::Raw("CONCAT(firstname,'',lastname) as created_by_staff")]);
            },'project'=>function($project){
                $project->select(['id','name']);
            },'client'=>function($client){
                $client->select(['id','name']);
            }]);
        whereBetween($eloquentData, 'DATE(complaints.created_at)', $request->created_at1, $request->created_at2);

        if ($request->id) {
            $eloquentData->where('complaints.id', '=', $request->id);
        }

        if ($request->name) {
            $eloquentData->where('complaints.name', 'LIKE', '%'.$request->name.'%');
        }

        if ($request->project_id) {
            $eloquentData->where('complaints.project_id', '=', $request->project_id);
        }

        if ($request->client_id) {
            $eloquentData->where('complaints.client_id', '=', $request->client_id);
        }

        if ($request->status) {
            $eloquentData->where('complaints.status', '=', $request->status);
        }

        if ($request->staff_id) {
            $eloquentData->where('complaints.staff_id', '=', $request->staff_id);
        }

        if ($request->created_by_staff_id) {
            $eloquentData->where('complaints.created_by_staff_id', '=', $request->created_by_staff_id);
        }

        $Transformer = new ComplaintsTransformer();

            if (empty($eloquentData->first())){
                return $this->json(false,__('No Complaints Available'));
            }
                $complaints = $eloquentData->orderBy('created_at','DESC')->jsonPaginate();

            $project = Project::get(['id','name']);
            $staff = Staff::select(['id',\DB::Raw("CONCAT(firstname,'',lastname) as name")])->get();
            $client = Client::get(['id','name']);
        $Transformer->staff = $staff;
        $allData = $Transformer->transformCollection($complaints->toArray());
        $allData['staff'] = $staff;
        $allData['project'] = $project;
        $allData['client'] = $client;
        return $this->json(true, __('Complaints'),$allData);

    }
    public function oneComplaint(Request $request){
        //        if (!staffCan('system.client.show', Auth::id())) {
//            return $this->json(false,__('Youd Don\'t have permission to this request'),[],403);
//        }
        $RequestData = $request->only('complaint_id');
        $validator = Validator::make($RequestData, [
            'complaint_id' => 'required|exists:complaints,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $eloquentData = Complaint::select([
            'complaints.id',
            'complaints.name',
            'complaints.project_id',
            'complaints.client_id',
            'complaints.status',
            'complaints.details',
            'complaints.staff_id',
            'complaints.created_by_staff_id',
            'complaints.created_at'
        ])
            ->with(['staff'=>function($staff){
                $staff->select(['id',DB::Raw("CONCAT(firstname,'',lastname) as complaint_of_staff")]);
            },'created_by_staff'=>function($created_by_staff){
                $created_by_staff->select(['id',DB::Raw("CONCAT(firstname,'',lastname) as created_by_staff")]);
            },'project'=>function($project){
                $project->select(['id','name']);
            },'client'=>function($client){
                $client->select(['id','name']);
            }])
        ->where('complaints.id',$request->complaint_id)
            ->first();

        if(empty($eloquentData))
            return $this->json(false,__('No Results'));
        $Transforrmer = new ComplaintsTransformer();

       // return $this->json(true,__('One Contract'),$allData);
        return $this->json(true,__('One Complaint'),$Transforrmer->transform($eloquentData));

    }
    public function createComplaint(Request $request)
    {
        // ask for validation of init_credit if it required or not.
        $validator=  Validator::make($request->all(),[
            'name'       => 'required',
            'staff_id'   => 'required|exists:staff,id',
            'project_id' => 'nullable|exists:projects,id',
            'client_id'  => 'required|exists:clients,id',
            'details'    => 'required',
            'status'     => 'required|in:pending,closed,solved'
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        $theRequest = $request->all();
        $theRequest['created_by_staff_id'] = 1;

        $complaint = Complaint::create($theRequest);
        if ($complaint)
            return $this->respondCreated($complaint);
        else {
            return $this->json(false,__('Can\'t Add New Complaint'));
        }
    }
    public function updateComplaint(Request $request)
    {
        $theRequest = $request->all();
        $validator=  Validator::make($theRequest,[
            'complaint_id'       => 'required|exists:complaints,id',
            'staff_id'           => 'nullable|exists:staff,id',
            'project_id'         => 'nullable|exists:projects,id',
            'client_id'          => 'nullable|exists:clients,id',
           // 'details'    => 'required',
            'status'             => 'nullable|in:pending,closed,solved',
        ]);


        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }


            $complaint = Complaint::where('id',$request->complaint_id)->first();
            $columnToUpdate =  array_filter($theRequest);
            $updated = $complaint->update($columnToUpdate);


        if ($updated) {
            $Transformer = new ComplaintsTransformer();
            return $this->json(true,__('One Complaint'),$Transformer->transform($complaint));
        }
        else {
            return $this->json(false,__('Can\'t Update this Complaint'));
        }
    }

    public function deleteComplaint(Request $request){
        $RequestData = $request->only('complaint_id');
        $validator = Validator::make($RequestData, [
            'complaint_id' => 'required|exists:complaints,id',
        ]);
        if ($validator->errors()->any()) {
            return $this->ValidationError($validator, __('Validation Error'));
        }

        if (Complaint::where('id',$request->complaint_id)->delete())
            return $this->json(true,__('Complaint Deleted Successfully'));
        return $this->json(false,__('No Results'));
    }

    public function staffClientsAndProjects()
    {
        $data = [];
        $clients = Client::get(['id','name']);
        $staff = Staff::get(['id',DB::Raw("CONCAT(firstname,'',lastname)as staff_name")]);
        $projects = Project::get(['id','name']);
        $data['staff'] = $staff;
        $data['clients'] = $clients;
        $data['projects'] = $projects;
        return $this->json(true,__('Staff,Clients And Projects'),$data);
    }
}