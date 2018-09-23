<?php

namespace App\Modules\System;

use App\Libs\Payments\Validator;

use App\Models\Contract;
use App\Models\Project;
use App\Models\Quotations;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class ContractController extends SystemController
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
            $eloquentData = Contract::select([
                'id',
                'project_id',
                'date_from',
                'date_to',
                'staff_id',
               'created_at', ])->with('staff');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

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
            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('project_id', function($data){
                    return '<a href="'.route("system.project.show",$data->staff_id).'" target="_blank">'.$data->project->name.'</a>';
                })

                ->addColumn('date_from', '{{$date_from}}')
                ->addColumn('date_to', '{{$date_to}}')
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
                                <li class=\"dropdown-item\"><a href=\"" . route('system.contract.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.contract.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.contract.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data

            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Project'),

                __('Date from'),
                __('Date to'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Contract')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Contract');
            } else {
                $this->viewData['pageTitle'] = __('Contract');
            }

            return $this->view('contract.index', $this->viewData);
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
            'text' => __('Contract'),
            'url' => route('system.contract.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Contract'),
        ];



        $this->viewData['pageTitle'] = __('Create Contract');
        return $this->view('contract.create', $this->viewData);
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
           'project_id'             =>'required|exists:projects,id',
           'description'            =>'nullable',
           'file'                   =>'nullable',
           'date_from'              =>'required',
           'date_to'                =>'required',
        ]);
        $theRequest = $request->all();
        $theRequest['staff_id']  = Auth::id();
        if($request->file){
            $theRequest['file'] = $request->file->store('Contract/'.date('y').'/'.date('m'));
        }
        $contract = Contract::create($theRequest);
        if ($contract)
            return redirect()
                ->route('system.contract.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.contract.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add contract'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Contract $contract)
    {
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('contract'),
                'url' => route('system.contract.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Contract';
        $this->viewData['result'] = $contract;
        return $this->view('contract.show', $this->viewData);
    }

    public function edit(Contract $contract)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Contract'),
            'url' => route('system.contract.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Contract'),
        ];



        $this->viewData['pageTitle'] = __('Edit Contract');
        $this->viewData['result'] = $contract;

        return $this->view('contract.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Contract $contract)
    {
        $this->validate($request,[
            'project_id'             =>'required|exists:projects,id',
            'description'            =>'nullable',
         //   'file'                   =>'nullable|image',
            'file'                  =>'nullable|mimes:jpeg,png,PNG,jpg,zip,docx,doc,WEBP,pdf|max:2048',
            'date_from'              =>'required',
            'date_to'                =>'required',


        ]);
        $theRequest = $request->all();
        $theRequest['staff_id']  = Auth::id();
        if($request->file('file')){
            dd($theRequest['file']);
            $theRequest['file'] = $request->file->store('Contract/'.date('y').'/'.date('m'));
        }
        $updated = $contract->update($theRequest);
        if ($updated) {
            return redirect()
                ->route('system.contract.edit', $contract->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Contract'));
        }
        else {
            return redirect()
                ->route('system.contract.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Contract'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Contract $contract)
    {
        $contract->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Contract  has been deleted successfully')];
        } else {
            redirect()
                ->route('system.contract.index')
                ->with('status', 'success')
                ->with('msg', __('This contract  has been deleted'));
        }
    }
}
