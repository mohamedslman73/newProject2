<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;
use App\Models\Attendance;
use App\Models\Deduction;
use App\Models\MonthlyReport;
use App\Models\Overtime;
use App\Models\PermissionGroup;
use App\Models\Staff;
use App\Models\Vacation;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Facades\Datatables;

class AttendanceController extends SystemController
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
            $eloquentData = Attendance::select([
                'id',
                'cleaner_id',
                'date',
                'group_id',
                'project_id',
                'type',
                'notes',
                'staff_id',
                'created_at',
            ]);

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(date)', $request->date1, $request->date2);

            if ($request->id) {
                $eloquentData->where('id', '=', $request->id);
            }
            if ($request->cleaner_id) {
                $eloquentData->where('cleaner_id', '=',  $request->cleaner_id);
            }
            if ($request->project_id) {
                $eloquentData->where('project_id', '=', $request->project_id);
            } if ($request->group_id) {
                $eloquentData->where('group_id', '=', $request->group_id);
            }

            if ($request->staff_id) {
                $eloquentData->where('staff_id', '=', $request->staff_id);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('cleaner_id', function($data){
                    if ($data->cleaner_id)
                        return '<a href="'.route("system.staff.show",$data->cleaner_id).'" target="_blank">'.$data->cleaner->Fullname.'</a>';
                    return '--';
                })
                ->addColumn('project_id', function($data){
                    if ($data->project_id)
                        return '<a href="'.route("system.project.show",$data->project_id).'" target="_blank">'.$data->project->name.'</a>';
                    return '--';
                })
                ->addColumn('date', '{{$date}}')
                ->addColumn('type', '{{$type}}')
                ->addColumn('notes', function ($data){
                    if ($data->notes) {
                        return str_limit($data->notes, 25);
                    }
                    return '--';
                })
                ->addColumn('staff_id', function($data){
                    if ($data->staff_id)
                        return '<a href="'.route("system.staff.show",$data->staff_id).'" target="_blank">'.$data->staff->Fullname.'</a>';
                    return '--';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                              <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.attendance.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Cleaner'),
                __('Project'),
                __('Date'),
                __('type'),
                __('Notes'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Attendance')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Attendance');
            } else {
                $this->viewData['pageTitle'] = __('Attendance');
            }



            return $this->view('attendance.index', $this->viewData);
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
            'text' => __('Attendance'),
            'url' => route('system.attendance.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Attendance'),
        ];
        // $this->viewData['permissionGroup'] = $retutn;
        $this->viewData['pageTitle'] = __('Create Attendance');
        return $this->view('attendance.create', $this->viewData);

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
            'cleaner_id'                    =>'required|exists:staff,id',
            'project_id'                    =>'required|exists:projects,id',
            'date'                          =>'required',
        ]);
        $theRequest = $request->all();
        $theRequest['staff_id']  = Auth::id();
        $supplier = Attendance::create($theRequest);
        if ($supplier)
            return redirect()
                ->route('system.attendance.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.attendance.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Attendance'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Attendance $attendance)
    {
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Attendance'),
                'url' => route('system.attendance.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Attendance';
        $this->viewData['result'] = $attendance;
        return $this->view('attendance.show', $this->viewData);
    }

    public function edit(Attendance $attendance)

    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Attendance'),
            'url' => route('system.attendance.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Attendance'),
        ];

        $this->viewData['pageTitle'] = __('Edit Attendance');
        $this->viewData['result'] = $attendance;


        return $this->view('attendance.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Attendance $attendance)
    {
        $this->validate($request,[
            'cleaner_id'                    =>'required|exists:staff,id',
            'project_id'                    =>'required|exists:projects,id',
            'date'                          =>'required',
        ]);
        $theRequest = $request->all();
        if ($attendance->update($theRequest)) {
            return redirect()
                ->route('system.attendance.edit', $attendance->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Attendance'));
        }
        else {
            return redirect()
                ->route('system.Attendance.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Attendance'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Attendance $attendance)
    {
        $attendance->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Attendance  has been deleted successfully')];
        } else {
            redirect()
                ->route('system.attendance.index')
                ->with('status', 'success')
                ->with('msg', __('This Attendance  has been deleted'));
        }
    }


    public function attendanceGroup(){


        $this->viewData['breadcrumb'][] = [
            'text' => __('Attendance'),
            'url' => route('system.attendance.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Attendance'),
        ];
        $permission=  PermissionGroup::get(['id','name']);
        $retutn = [];
        foreach ($permission as $key=>$value){

            $retutn[$value->id] = $value->name;

        }
        $this->viewData['permissionGroup'] = $retutn;
        $this->viewData['pageTitle'] = __('Create Attendance');
        return $this->view('attendance.attendance', $this->viewData);
    }
    public function attendanceGroupStore(Request $request){
//dd(date('Y-m-d'));
        $this->validate($request,[
            'type' =>'required|in:absence,presence',

            'permission_group_id' =>'required|exists:permission_groups,id',
            'cleaner_id.*' =>'required|exists:staff,id',
//            'cleaner_id' =>'',
            'cleaner_id' =>    Rule::unique('attendance')->where(function ($query) {
                $query->where('date', date('Y-m-d'));
                /*
                 *  'month'     =>  Rule::unique('staff_target','month')
                        ->where('year',$this->year)
                        ->where('staff_id',$this->staff_id),
                 */
                //'unique:attendance,cleaner_id,NULL,id,date,'.date("Y-m-d")
            }),

        ]);

        $permissionGroup = PermissionGroup::where('id',$request->permission_group_id)->first();
        $theRequest = $request->only([
            'type',
            'cleaner_id',
            'permission_group_id'
        ]);
        $date = date('Y-m-d');

        $anotherType = 'presence';
        if($theRequest['type'] == 'presence' )
            $anotherType = 'absence';


        $attendanceData = [];
        foreach ($permissionGroup->staff as $key => $row){
            //  dd($row->id);
            if(in_array($row->id,$theRequest['cleaner_id'])){
                $attendanceData[] = [
                    'cleaner_id'=>$row->id,
                    'type'=>$theRequest['type'],
                    'staff_id'=>Auth::id(),
                    'group_id' =>$theRequest['permission_group_id'],
                    'date'=>date('Y-m-d')
                ];
            }else{
                $attendanceData[] = [
                    'cleaner_id'=>$row->id,
                    'type'=>$anotherType,
                    'staff_id'=>Auth::id(),
                    'group_id' =>$theRequest['permission_group_id'],
                    'date'=>date('Y-m-d')
                ];
            }
        }
        $attendance = Attendance::insert($attendanceData);

        if ($attendance)
            return redirect()
                ->route('system.attendance-group')
                ->with('status', 'success')
                ->with('msg', __('attendance has been added successfully'));
        else {
            return redirect()
                ->route('system.attendance-group')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add attendance'));
        }
    }
    public function attendanceGroupAjax(Request $request){
        $permissionGroup = PermissionGroup::where('id',$request->permission_group_id)->first();
        if ($permissionGroup){
            $this->viewData['cleaners'] = $permissionGroup->staff;
        }
        return $this->view('attendance.attendance-ajax', $this->viewData);
    }

    public function groupIndex(Request $request)
    {

        if ($request->isDataTable) {
            $eloquentData = Attendance::select([
                'id',
                'cleaner_id',
                'date',
                'project_id',
                'notes',
                'group_id',
                'type',
                'staff_id',
                'created_at',
            ])->whereNull('project_id')->groupBy('date','group_id');
//  dd($eloquentData->get()->toArray());
            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(date)', $request->date1, $request->date2);

            if ($request->id) {
                $eloquentData->where('id', '=', $request->id);
            }
            if ($request->cleaner_id) {
                $eloquentData->where('cleaner_id', '=',  $request->cleaner_id);
            }
            if ($request->project_id) {
                $eloquentData->where('project_id', '=', $request->project_id);
            }

            if ($request->staff_id) {
                $eloquentData->where('staff_id', '=', $request->staff_id);
            }
            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('type', '{{$type}}')
                ->addColumn('cleaner_id', function($data){
                    return $data->cleaner->permission_group->name;
                    //return '<a href="'.route("system.staff.show",$data->cleaner_id).'" target="_blank">'.$data->cleaner->Fullname.'</a>';
                })
                ->addColumn('date', '{{$date}}')
                ->addColumn('notes', function ($data){
                    if ($data->notes) {
                        return str_limit($data->notes, 25);
                    }
                    return '--';
                })
                ->addColumn('staff_id', function($data){
                    return '<a href="'.route("system.staff.show",$data->staff_id).'" target="_blank">'.$data->staff->Fullname.'</a>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                               <li class=\"dropdown-item\"><a target='_blank' href=\"" . route('system.attendance.index') . "?group_id=".$data->group_id."&date1=".$data->date."&date2=".$data->date."\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.attendance.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Type'),
                __('Group'),
                __('Date'),
                __('Notes'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Attendance Groups')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Attendance');
            } else {
                $this->viewData['pageTitle'] = __('Attendance Groups');
            }



            return $this->view('attendance.index-group', $this->viewData);
        }
    }


    public  function attendanceGroupEdit(Attendance $attendance){
        if(strtotime($attendance->date) > strtotime(date('Y-m-d')) )
            return $this->view('project.attendance.taked', ['msg'=>'You Cannot Edit Attendance After it Month']);

        $permissionGroup = PermissionGroup::where('id',$attendance->group_id)->first();
        if ($permissionGroup){
            $this->viewData['cleaners'] = $permissionGroup->staff;
        }else {
            $this->viewData['cleaners'] = [];
        }
        $permission=  PermissionGroup::get(['id','name']);
        $retutn = [];
        foreach ($permission as $key=>$value){
            $retutn[$value->id] = $value->name;
        }
        $this->viewData['permissionGroup'] = $retutn;
        $this->viewData['pageTitle'] = __('Edit Attendance');
        $this->viewData['result'] = $attendance;
        return $this->view('attendance.attendance', $this->viewData);
    }



    public function attendanceGroupUpdate(Attendance $attendance,Request $request){

        if(strtotime($attendance->date) > strtotime(date('Y-m-d')) )
            return $this->view('project.attendance.taked', ['msg'=>'You Cannot Edit Attendance After it Month']);


        $this->validate($request,[
            'type' =>'required|in:absence,presence',
            'cleaner_id' =>'required|array',
            'cleaner_id.*' =>'exists:staff,id'
        ]);
        $theRequest = $request->only([
            'type',
            'cleaner_id',
            'permission_group_id'
        ]);


        $attendanceData = [];


        $delete = Attendance::where(['date'=>$attendance->date,'group_id'=>$attendance->group_id])->delete();
        $permissionGroup = PermissionGroup::where('id',$request->permission_group_id)->first();
        if($delete) {
            foreach ($permissionGroup->staff as $key => $row){
                if(in_array($row->id,$theRequest['cleaner_id'])){
                    $attendanceData[] = [
                        'cleaner_id'=>$row->id,
                        'type'=>'presence',
                        'staff_id'=>Auth::id(),
                        'group_id' =>$theRequest['permission_group_id'],
                        'date'=>$attendance->date
                    ];
                }else{
                    $attendanceData[] = [
                        'cleaner_id'=>$row->id,
                        'type'=>'absence',
                        'staff_id'=>Auth::id(),
                        'group_id' =>$theRequest['permission_group_id'],
                        'date'=>$attendance->date
                    ];
                }
            }
            $attendanceInsert = Attendance::insert($attendanceData);
            if ($attendanceInsert) {
                $newAttendance = Attendance::where(['group_id'=>$attendance->group_id,'date'=>$attendance->date])->first();
                return redirect()
                    ->route('system.attendance-group-edit',$newAttendance->id)
                    ->with('status', 'success')
                    ->with('msg', __('attendance has been Updated successfully'));
            } else {
                return redirect()
                    ->route('system.attendance-group-edit',$attendance->id)
                    ->with('status', 'danger')
                    ->with('msg', __('Sorry Couldn\'t Updated attendance'));
            }

        }else{
            return redirect()
                ->route('system.attendance-group-edit', $attendance->id)
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit attendance'));
        }

    }








}
