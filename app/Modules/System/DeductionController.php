<?php

namespace App\Modules\System;
use App\Models\Deduction;
use App\Models\Vacation;
use App\Models\VacationTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class DeductionController extends SystemController
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
            $eloquentData = Deduction::select([
                'deductions.id',
                'deductions.date',
                'deductions.amount',
                'deductions.deduction_from',
                'deductions.reason',
                'deductions.staff_id',
                'deductions.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'deductions.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(deductions.created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(deductions.date)', $request->date1, $request->date2);
            whereBetween($eloquentData, 'deductions.amount', $request->amount1, $request->amount2);

            if ($request->id) {
                $eloquentData->where('deductions.id', '=', $request->id);
            }
            if ($request->staff_id) {
                $eloquentData->where('deductions.staff_id', '=', $request->staff_id);
            }
            if ($request->deduction_from) {
                $eloquentData->where('deductions.deduction_from', '=', $request->deduction_from);
            }
            if ($request->reason) {
                $eloquentData->where('deductions.reason', 'LIKE', '%' . $request->reason . '%');
            }
            if ($request->num_of_days) {
                $eloquentData->where('deductions.', '=', $request->num_of_days);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('date', '{{$date}}')
                ->addColumn('amount',function ($data){
                    return amount($data->amount,true);
                })
                ->addColumn('deduction_from',function ($data){
                    return "<a target='_blank' href=\"" . route('system.staff.show', $data->deductionFrom->id) . "\">".$data->deductionFrom->Fullname."</a>";
                })
                ->addColumn('staff_name', '<a href="{{route(\'system.staff.show\',$staff_id)}}" target="_blank">{{$staff_name}}</a>')
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.deduction.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.deduction.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.deduction.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Date'),
                __('Amount'),
                __('Deduction From'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Deduction')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Deduction');
            } else {
                $this->viewData['pageTitle'] = __('Deduction');
            }
            return $this->view('deduction.index', $this->viewData);
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
            'text' => __('Deduction'),
            'url' => route('system.deduction.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Deduction'),
        ];

        $this->viewData['pageTitle'] = __('Create Deduction ');
        return $this->view('deduction.create', $this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $this->validate($request,[
            'reason'=> 'required',
            'date'=> 'required|date_format:"Y-m-d"',
            'amount'=> 'required|numeric',
            'deduction_from'=> 'required|exists:staff,id',
        ]);

        $theRequest = $request->only([
            'reason',
            'date',
            'amount',
            'deduction_from',
        ]);


        $theRequest['staff_id'] = Auth::id();
       //dd($theRequest);
        $deduction = Deduction::create($theRequest);
        if ($deduction)
            return redirect()
                ->route('system.deduction.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.deduction.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Deduction'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Deduction $deduction)
    {
       // dd($deduction);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Deduction'),
                'url' => route('system.deduction.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];

        $this->viewData['pageTitle'] = 'Deduction';
        $this->viewData['result'] = $deduction;
        return $this->view('deduction.show', $this->viewData);
    }

    public function edit(Deduction $deduction)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Deduction'),
            'url' => route('system.deduction.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Deduction'),
        ];
        $this->viewData['pageTitle'] = __('Edit Deduction');
        $this->viewData['result'] = $deduction;

        return $this->view('deduction.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Deduction $deduction)
    {
        $this->validate($request,[
            'reason'=> 'required',
            'date'=> 'required|date_format:"Y-m-d"',
            'amount'=> 'required|numeric',
          //  'deduction_from'=> 'required|exists:staff,id',
        ]);

        $theRequest = $request->only([
            'reason',
            'date',
            'amount',
            'deduction_from',
        ]);

        if ($request->has('deduction_from')){
            $theRequest['deduction_from'] = $request->deduction_from;
        }else{
            unset($theRequest['deduction_from']);
        }

        if ($deduction->update($theRequest)) {
            return redirect()
                ->route('system.deduction.edit', $deduction->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Deduction'));
        }
        else {
            return redirect()
                ->route('system.deduction.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Deduction'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Deduction $deduction)
    {
        $deduction->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Deduction has been deleted successfully')];
        } else {
            redirect()
                ->route('system.deduction.index')
                ->with('status', 'success')
                ->with('msg', __('This Deduction has been deleted'));
        }
    }
}
