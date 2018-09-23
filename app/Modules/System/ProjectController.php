<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;
use App\Models\Attendance;
use App\Models\ClientOrders;
use Doctrine\Common\Annotations\Annotation\Attribute;
use Illuminate\Support\Facades\Validator as Valid;

use App\Models\Contract;
use App\Models\Project;
use App\Models\ProjectCleaners;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Facades\Datatables;

class ProjectController extends SystemController
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
            $eloquentData = Project::with(['staff','client']);
            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

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

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('client',function ($data){
                    return '<a target="_blank" href="'.route("system.client.show",$data->client_id).'" >'.$data->client->name.'</a>';
                })
                ->addColumn('status', '{{$status}}')
                ->addColumn('staff_id', function($data){
                    return '<a href="'.route("system.staff.show",$data->staff_id).'" target="_blank">'.$data->staff->Fullname.'</a>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:ia');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.projects.attendance', $data->id) . "\">" . __('Attendance') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.project.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.project.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.project.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'), __('Client'), __('Status'), __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Project')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Project');
            } else {
                $this->viewData['pageTitle'] = __('Project');
            }

            return $this->view('project.index', $this->viewData);
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
            'text' => __('Project'),
            'url' => route('system.project.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Project'),
        ];

        $this->viewData['pageTitle'] = __('Create Project');
        return $this->view('project.create', $this->viewData);

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
           'client_id' =>'required|exists:clients,id',
           'status' =>'required|in:in-progress,in-active,hold'

        ]);
        $theRequest = $request->only([
            'name',
            'client_id',
            'status',

        ]);


        $theRequest['staff_id'] = Auth::id();
        $project = Project::create($theRequest);
        if ($project)
            return redirect()
                ->route('system.project.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.project.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Project'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request, Project $project)
    {
//        $eloquentData = Attendance::where('project_id',$project->id)->groupBy('date')->get();
//        dd($eloquentData->toArray());
       // dd($project->lastContract()->id);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Project'),
                'url' => route('system.project.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];


        if($request->isContract){
            $eloquentData = Contract::where('project_id',$project->id);
            return Datatables::eloquent($eloquentData)
                ->addColumn('id','<a target="_blank" href="{{route(\'system.contract.show\',$id)}}">{{$id}}</a>')
                ->addColumn('description','{{str_limit($description,10)}}')
                ->addColumn('date_from','{{$date_from}}')
                ->addColumn('date_to','{{$date_to}}')
                ->addColumn('staff_firstname',function($data){
                    return '<a target="_blank" href="'.route('system.staff.show',$data->staff_id).'">'.$data->staff->firstname.' '.$data->staff->lastname.'</a>';
                })
                ->make(true);
        }

        if($request->isCleaners){

            $eloquentData = ProjectCleaners::where('project_id',$project->id);

            return Datatables::eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('department', function($data){
                    return "<a target='_blank' href=\"".route('system.department.index',"id=".$data->department_id)."\">".__($data->department->name)."</a>";
                })
                 ->addColumn('firstname', function($data){
                     return "<a target='_blank' href=\"".route('system.staff.show',$data->cleaner_id)."\">".__($data->cleaner->firstname .' '.$data->cleaner->lastname )."</a>";
                 })
                ->addColumn('created_at',function ($data){
                    if ($data->created_at){
                        return $data->created_at->diffForHumans();
                    }
                    return '--';
                })
                ->addColumn('action',function($data){
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                 <li class=\"dropdown-item\"><a onclick=\"deleteRecord('".route('system.project-cleaners.destroy',$data->id)."')\" href=\"javascript:void(0)\">".__('Delete')."</a></li>
                              </ul>
                            </div>";
                })
                     ->make(true);

        }

        if($request->isOrders){

            $eloquentData = ClientOrders::where('project_id',$project->id);

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', function($data){
                    return "<a target='_blank' href=\"".route('system.client-orders.show',$data->id)."\">".$data->id."</a>";
                })
                ->addColumn('total_price','{{$total_price}}')
                ->addColumn('staff_id', function($data){
                    return "<a target='_blank' href=\"".route('system.staff.show',$data->staff_id)."\">".__($data->staff->firstname .' '.$data->staff->lastname )."</a>";
                })
                ->addColumn('created_at',function ($data){
                    if ($data->created_at){
                        return $data->created_at->diffForHumans();
                    }
                    return '--';
                })
                ->addColumn('action',function($data){
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.client-orders.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.client-orders.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                 <li class=\"dropdown-item\"><a onclick=\"deleteRecord('".route('system.client-orders.destroy',$data->id)."')\" href=\"javascript:void(0)\">".__('Delete')."</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);

        }
        if ($request->isAttendance){
            $eloquentData = Attendance::where('project_id',$project->id)->groupBy('date');

            return Datatables::eloquent($eloquentData)
                ->addColumn('id','{{$id}}')
                ->addColumn('date','{{$date}}')
                ->addColumn('staff_id', function($data){
                    return "<a target='_blank' href=\"".route('system.staff.show',$data->staff_id)."\">".__($data->staff->firstname .' '.$data->staff->lastname )."</a>";
                })
                ->addColumn('created_at',function ($data){
                    if ($data->created_at){
                        return $data->created_at->diffForHumans();
                    }
                    return '--';
                })
                ->addColumn('action',function($data){
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a target='_blank' href=\"" . route('system.attendance.index') . "?project_id=".$data->project_id."&date1=".$data->date."&date2=".$data->date."\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.client-orders.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        }

        $this->viewData['pageTitle'] = 'Project';
        $this->viewData['result'] = $project;
        $this->viewData['lang'] = \DataLanguage::get();

        return $this->view('project.show', $this->viewData);
    }

    public function edit(Project $project)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Project'),
            'url' => route('system.project.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit project'),
        ];


        $this->viewData['pageTitle'] = __('Edit project');
        $this->viewData['result'] = $project;
        $this->viewData['result']->client_name = $project->client->name;

        return $this->view('project.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Project $project)
    {
        $this->validate($request,[
            'name' =>'required',
            'client_id' =>'required|exists:clients,id',
            'status' =>'required|in:in-progress,in-active,hold'
        ]);
        $theRequest = $request->only([
            'name',
            'client_id',
            'status',
        ]);
        if ($project->update($theRequest)) {
          //  dd($category);
            return redirect()
                ->route('system.project.edit', $project->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit project'));
        }
        else {
            return redirect()
                ->route('system.project.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit project'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Project $project)
    {
        $project->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Project has been deleted successfully')];
        } else {
            redirect()
                ->route('system.project.index')
                ->with('status', 'success')
                ->with('msg', __('This Project has been deleted'));
        }
    }



    public function addProjectCleaners(Request $request){

        $validator = Valid::make($request->toArray(), [
            'department_id' =>'required|exists:department,id',
            'cleaner_id' =>'required|exists:staff,id',
            'project_id' =>'required|exists:projects,id',
        ]);

        if ($validator->errors()->any()) {
            return ['status' => false, 'msg' => __('Please Choose Department and Cleaner')];
        }

        $check = ProjectCleaners::where(['cleaner_id'=>$request->cleaner_id,'project_id'=>$request->project_id]);

        if($check->first()){
            return ['status' => false, 'msg' => __('Cleaner Already added to project')];
        }

        $theRequest = $request->only(['department_id','cleaner_id','project_id']);

        $add = ProjectCleaners::create($theRequest);
        if($add){
            return ['status'=>true,'msg'=>__('Add Successfuly')];
        }else{
            return ['status'=>false,'msg'=>__('Sorry Cannot add Cleaner')];
        }

    }



    function attendance(Project $project){

        $this->viewData['breadcrumb'][] = [
            'text' => __('Project'),
            'url' => route('system.project.show',$project->id)
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Attendance'),
        ];


        $attendanceToday = Attendance::where(['project_id'=>$project->id,'date'=>date('Y-m-d')])->first();
        if(!empty($attendanceToday))
            return $this->view('project.attendance.taked', ['msg'=>'Attendance is taked for '.date('Y-m-d')]);
        $this->viewData['project'] = $project;
        $this->viewData['cleaners'] = $project->cleaners;
        $this->viewData['pageTitle'] = __('Add Attendance');

        return $this->view('project.attendance.attendance', $this->viewData);

    }


    function attendanceStore(Project $project,Request $request){
        $attendanceToday = Attendance::where(['project_id'=>$project->id,'date'=>date('Y-m-d')])->first();
        if(!empty($attendanceToday))
            return $this->view('project.attendance.taked', ['msg'=>'You Cannot Add Two Attendance for one day']);

        $this->validate($request,[
            'type' =>'required|in:absence,presence',
            'cleaner_id' =>'required|array',
            'cleaner_id.*' =>'exists:staff,id'

        ]);
        $theRequest = $request->only([
            'type',
            'cleaner_id'
            ]);


            $anotherType = 'presence';
            if($theRequest['type'] == 'presence' )
                $anotherType = 'absence';


            $attendanceData = [];
            foreach ($project->cleaners as $key => $row){
                if(in_array($row->cleaner_id,$theRequest['cleaner_id'])){
                    $attendanceData[] = [
                        'cleaner_id'=>$row->cleaner_id,
                        'type'=>$theRequest['type'],
                        'project_id'=>$project->id,
                        'staff_id'=>Auth::id(),
                        'date'=>date('Y-m-d')
                    ];
                }else{
                    $attendanceData[] = [
                        'cleaner_id'=>$row->cleaner_id,
                        'type'=>$anotherType,
                        'project_id'=>$project->id,
                        'staff_id'=>Auth::id(),
                        'date'=>date('Y-m-d')
                    ];
                }
            }

        $attendance = Attendance::insert($attendanceData);
        if ($attendance)
            return redirect()
                ->route('system.project.show',$project->id)
                ->with('status', 'success')
                ->with('msg', __('attendance has been added successfully'));
        else {
            return redirect()
                ->route('system.projects.attendance',$project->id)
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add attendance'));
        }




    }
    
    function attendanceEdit(Attendance $attendance){


        if(strtotime($attendance->date) > strtotime(date('Y-m-d')) )
            return $this->view('project.attendance.taked', ['msg'=>'You Cannot Edit Attendance After it Month']);

        $this->viewData['result'] = $attendance;
        $this->viewData['project'] = $attendance->project;
        $this->viewData['cleaners'] = Attendance::where(['project_id'=>$attendance->project_id,'date'=>$attendance->date])->get();
        $this->viewData['pageTitle'] = __('Edit Attendance');

//dd($project->cleaners->toArray());
       // $attendanceToday = Attendance::where(['project_id'=>$project->id,'date'=>date('Y-m-d')])->first();
        if(!empty($attendanceToday))
            return $this->view('project.attendance.taked', $this->viewData);
     //   $this->viewData['project'] = $project;
        $this->viewData['cleaners'] = $attendance;
        $this->viewData['pageTitle'] = __('Attendance');


        return $this->view('project.attendance.attendance', $this->viewData);
    }



    function attendanceUpdate(Attendance $attendance,Request $request){

        if(strtotime($attendance->date) > strtotime(date('Y-m-d')) )
            return $this->view('project.attendance.taked', ['msg'=>'You Cannot Edit Attendance After it Month']);


        $this->validate($request,[
            'type' =>'required|in:absence,presence',
            'cleaner_id' =>'required|array',
            'cleaner_id.*' =>'exists:staff,id'
        ]);
        $theRequest = $request->only([
            'type',
            'cleaner_id'
        ]);


        $attendanceData = [];

        $delete = Attendance::where(['date'=>$attendance->date,'project_id'=>$attendance->project_id])->delete();
        if($delete) {
            foreach ($attendance->project->cleaners as $key => $row) {
                if (in_array($row->cleaner_id, $theRequest['cleaner_id'])) {
                    $attendanceData[] = [
                        'cleaner_id' => $row->cleaner_id,
                        'type' => 'presence',
                        'project_id' => $attendance->project_id,
                        'staff_id' => Auth::id(),
                        'date' =>$attendance->date
                    ];
                } else {
                    $attendanceData[] = [
                        'cleaner_id' => $row->cleaner_id,
                        'type' => 'absence',
                        'project_id' => $attendance->project_id,
                        'staff_id' => Auth::id(),
                        'date' => $attendance->date
                    ];
                }
            }

            $attendanceInsert = Attendance::insert($attendanceData);
            if ($attendanceInsert) {
                $newAttendance = Attendance::where(['project_id'=>$attendance->project_id,'date'=>$attendance->date])->first();
                return redirect()
                    ->route('system.projects.attendance-edit', $newAttendance->id)
                    ->with('status', 'success')
                    ->with('msg', __('attendance has been Updated successfully'));
            } else {
                return redirect()
                    ->route('system.projects.attendance-edit',$attendance->id)
                    ->with('status', 'danger')
                    ->with('msg', __('Sorry Couldn\'t Updated attendance'));
            }

        }else{
            return redirect()
                ->route('system.projects.attendance.edit', $attendance->id)
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit attendance'));
        }

    }

}
