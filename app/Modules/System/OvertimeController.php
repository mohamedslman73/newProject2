<?php

namespace App\Modules\System;
use App\Models\Overtime;
use App\Models\Project;
use App\Models\Vacation;
use App\Models\VacationTypes;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class OvertimeController extends SystemController
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
            $eloquentData = Overtime::select([
                'id',
                'added_to',
                'project_id',
                'hours',
                'date',
                'total_added_money',
                'staff_id',
                'created_at',
                ]);

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('id', '=', $request->id);
            }
            if ($request->staff_id) {
                $eloquentData->where('staff_id', '=', $request->staff_id);
            }
            if ($request->added_to) {
                $eloquentData->where('added_to', '=', $request->added_to);
            }
            if ($request->type) {
                $eloquentData->where('type', 'LIKE', '%' . $request->type . '%');
            }
            if ($request->num_of_days) {
                $eloquentData->where('num_of_days', '=', $request->num_of_days);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('project_id', function($data){
                    if ($data->project_id)
                    return $data->project->name;
                    return '--';
                })
                ->addColumn('date','{{$date}}')
                ->addColumn('hours','{{$hours}}')
                ->addColumn('total_added_money','{{$total_added_money}}')
                ->addColumn('added_to',function ($data){
                  return "<a target='_blank' href=\"" . route('system.staff.show', $data->addedTo->id) . "\">".$data->addedTo->Fullname."</a>";
                })
                ->addColumn('staff_name', function ($data){
                    return "<a target='_blank' href=\"" . route('system.staff.show', $data->staff->id) . "\">".$data->staff->Fullname."</a>";
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.overtime.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.overtime.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.overtime.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Project'), __('Date'), __('Hours'),  __('Total Added Money'),__('added To'),__('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Overtimes')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Overtimes');
            } else {
                $this->viewData['pageTitle'] = __('Overtime');
            }

            return $this->view('overtime.index', $this->viewData);
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
            'text' => __('Overtime'),
            'url' => route('system.overtime.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Overtime'),
        ];


        $this->viewData['pageTitle'] = __('Create Overtime ');
        return $this->view('overtime.create', $this->viewData);
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

            'added_to'=> 'required|exists:staff,id',
            'project_id'=> 'nullable|exists:projects,id',
            'hours'=> 'required|numeric',
            'date'=> 'required|date',
        ]);

        $theRequest = $request->only([
            'added_to',
            'project_id',
            'hours',
            'date',
        ]);


        $theRequest['staff_id'] = Auth::id();
        $theRequest['total_added_money'] =  setting('overtime_price_for_hour') * $request->hours;
        $vacation = Overtime::create($theRequest);
        if ($vacation)
            return redirect()
                ->route('system.overtime.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.overtime.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Overtime'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Overtime $overtime)
    {
        //dd($overtime->toArray());
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Overtime'),
                'url' => route('system.overtime.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Overtime';
        $this->viewData['result'] = $overtime;
        return $this->view('overtime.show', $this->viewData);
    }

    public function edit(Overtime $overtime)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Overtime'),
            'url' => route('system.overtime.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Overtime'),
        ];
        $this->viewData['pageTitle'] = __('Edit Overtime');
        $this->viewData['result'] = $overtime;

        return $this->view('overtime.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Overtime $overtime)
    {
        $this->validate($request,[
            'added_to'=> 'required|exists:staff,id',
            'project_id'=> 'nullable|exists:projects,id',
            'hours'=> 'required|numeric',
            'date'=> 'required|date',
        ]);

        $theRequest = $request->only([
            'added_to',
            'project_id',
            'hours',
            'date',
        ]);

        $theRequest['total_added_money'] =  setting('overtime_price_for_hour') * $request->hours;
        if ($request->has('added_to')){
            $theRequest['added_to'] = $request->added_to;
        }else{
            unset($theRequest['added_to']);
        }


        if ($overtime->update($theRequest)) {
          //  dd($category);
            return redirect()
                ->route('system.overtime.edit', $overtime->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Overtime'));
        }
        else {
            return redirect()
                ->route('system.overtime.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Overtime'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Overtime $overtime )
    {
        $overtime->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Item Category has been deleted successfully')];
        } else {
            redirect()
                ->route('system.vacation.index')
                ->with('status', 'success')
                ->with('msg', __('This vacation has been deleted'));
        }
    }
}
