<?php

namespace App\Modules\System;

use App\Models\Complaint;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ComplainController extends SystemController
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
            $eloquentData = Complaint::select([
                'complaints.id',
                'complaints.name',
                'complaints.project_id',
                'complaints.client_id',
                'complaints.status',
                'complaints.staff_id',
                'complaints.created_by_staff_id',
                'complaints.created_at'
            ])
                ->with('staff','created_by_staff','project','client');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

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

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('name', '{{$name}}')
                ->addColumn('staff_id', function ($data){
                    return '<a target="_blank" href="'.route('system.staff.show',$data->staff->id).'">'.$data->staff->fullname.'</a>';
                })
                ->addColumn('status', '{{$status}}')
                ->addColumn('client_id',function ($data){
                    if ($data->client_id){
                        return "<a target='_blank' href=\"" . route('system.client.show', $data->client->id) . "\">".$data->client->name."</a>";
                    }
                    return '--';
                })
                ->addColumn('project_id',function ($data){
                    if ($data->project_id){
                        return "<a target='_blank' href=\"" . route('system.project.show', $data->project->id) . "\">".$data->project->name."</a>";
                    }
                    return '--';
                })

                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d h:i A');
                })

                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.complaint.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.complaint.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.complaint.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Name'),__('Staff'),__('Status'),__('Client'), __('Project'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Complaints')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Complaints');
            } else {
                $this->viewData['pageTitle'] = __('Complaints');
            }
            $return  = [];
            $projects  = Project::get(['id','name']);
            foreach ($projects as $key=>$value){
                $return[$value->id] = $value->name;
            }
            $this->viewData['projects'] = $return;
            return $this->view('complaints.index', $this->viewData);
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
            'text' => __('Complaint'),
            'url' => route('system.complaint.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Complaint'),
        ];
        $return  = [];
        $projects  = Project::get(['id','name']);
        foreach ($projects as $key=>$value){
            $return[$value->id] = $value->name;
        }
        $this->viewData['projects'] = $return;
        $this->viewData['pageTitle'] = __('Create Complaint');
        return $this->view('complaints.create', $this->viewData);
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
            'name'       => 'required',
            'staff_id'   => 'required|exists:staff,id',
            'project_id' => 'nullable|exists:projects,id',
            'client_id'  => 'required|exists:clients,id',
            'details'    => 'required',
            'status'     => 'required|in:pending,closed,solved'
        ]);

        $theRequest = $request->all();
        $theRequest['created_by_staff_id'] = Auth::id();

        if (Complaint::create($theRequest))
            return redirect()
                ->route('system.complaint.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.complaint.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Complaint'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Complaint $complaint)
    {
        // dd($complain);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Complaint'),
                'url' => route('system.complaint.index'),
            ],
            [
                'text' => __('Show'),
            ]
        ];

        $this->viewData['pageTitle'] = __('Complaint');
        $this->viewData['result'] = $complaint;
        return $this->view('complaints.show', $this->viewData);
    }

    public function edit(Complaint $complaint)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Complaint'),
            'url' => route('system.complaint.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Complaint'),
        ];

        $return  = [];
        $projects  = Project::get(['id','name']);
        foreach ($projects as $key=>$value){
            $return[$value->id] = $value->name;
        }
        $this->viewData['projects'] = $return;
        $this->viewData['pageTitle'] = __('Edit Complaint');
        $this->viewData['result'] = $complaint;

        return $this->view('complaints.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Complaint $complaint)
    {
        $this->validate($request,[
            'name'       => 'required',
            'staff_id'   => 'required|exists:staff,id',
            'project_id' => 'nullable|exists:projects,id',
            'client_id'  => 'required|exists:clients,id',
            'details'    => 'required',
            'status'     => 'required|in:pending,closed,solved'
        ]);

        $theRequest = $request->all();

        if ($complaint->update($theRequest)) {
            return redirect()
                ->route('system.complaint.edit', $complaint->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Complaint'));
        }
        else {
            return redirect()
                ->route('system.complaint.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Complaint'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Complaint $complaint)
    {
        $complaint->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Complaint has been deleted successfully')];
        } else {
            redirect()
                ->route('system.complaint.index')
                ->with('status', 'success')
                ->with('msg', __('This Complaint has been deleted'));
        }
    }
}
