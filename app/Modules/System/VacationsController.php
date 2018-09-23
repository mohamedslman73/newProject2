<?php

namespace App\Modules\System;
use App\Models\Staff;
use App\Models\Vacation;
use App\Models\VacationTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class VacationsController extends SystemController
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
        /*
              *  'num_of_days',
             'vacation_type_id',
             'created_by_staff_id',
             'staff_id',
             'comment',
             'status',
             'from_date',
             'to_date',
              */
        if ($request->isDataTable) {
            $eloquentData = Vacation::select([
                'vacations.id',
                'vacations.vacation_type_id',
                'vacations.from_date',
                'vacations.to_date',
                'vacations.num_of_days',
                'vacations.staff_id',
                'vacations.created_by_staff_id',
                'vacations.created_at',
                \DB::Raw("CONCAT(staff.firstname,' ',staff.lastname) as staff_name"),
                ])
                ->join('staff', 'staff.id', '=', 'vacations.staff_id');

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }
            whereBetween($eloquentData, 'DATE(vacations.created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(vacations.form_date)', $request->form_date1, $request->form_date1);
            whereBetween($eloquentData, 'DATE(vacations.to_date)', $request->to_date1, $request->to_date1);
            whereBetween($eloquentData, 'vacations.num_of_days', $request->num_of_days1, $request->num_of_days2);

            if ($request->id) {
                $eloquentData->where('vacations.id', '=', $request->id);
            }
            if ($request->added_to) {
                $eloquentData->where('vacations.staff_id', '=', $request->added_to);
            }
            if ($request->num_of_days) {
                $eloquentData->where('vacations.num_of_days', '=', $request->num_of_days);
            }
            if ($request->vacation_type_id){
                $eloquentData->where('vacations.vacation_type_id','=',$request->vacation_type_id);
            }
            if ($request->staff_id) {
                $eloquentData->where('vacations.created_by_staff_id', '=', $request->staff_id);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('num_of_days','{{$num_of_days}}')
                ->addColumn('staff_name', function ($data){
                    return "<a target='_blank' href=\"" . route('system.staff.show', $data->staff->id) . "\">".$data->staff->Fullname."</a>";
                })
                ->addColumn('from_date', function ($data){
                    return '<code>'.$data->from_date.'</code>';
                })
                ->addColumn('to_date', function ($data){
                    return '<code>'.$data->to_date.'</code>';
                })
                ->addColumn('staff_id',function ($data){
                  return "<a target='_blank' href=\"" . route('system.staff.show', $data->created_by->id) . "\">".$data->created_by->Fullname."</a>";
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d H:iA');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.vacation.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.vacation.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.vacation.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Vacation Num Of Days'),
                __('added To'),
                __('From Date'),
                __('To Date'),
                __('Created By'),
                __('Created At'),
                __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Vacations')
            ];
            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Vacation');
            } else {
                $this->viewData['pageTitle'] = __('Vacations');
            }
            $return = [];
            $data = VacationTypes::get(['id', 'name']);
            foreach ($data as $key => $value) {
                $return[$value->id] = $value->name;
            }
            $this->viewData['vacation_types'] = $return;

            return $this->view('vacation.index', $this->viewData);
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
            'text' => __('Vacation'),
            'url' => route('system.vacation.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Vacation'),
        ];

        $return = [];
        $data = VacationTypes::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['vacation_types'] = $return;

        $this->viewData['pageTitle'] = __('Create Vacation ');
        return $this->view('vacation.create', $this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*
         *  'num_of_days',
        'vacation_type_id',
        'created_by_staff_id',
        'staff_id',
        'comment',
        'status',
        'from_date',
        'to_date',
         */
        $this->validate($request,[

            'staff_id'=> 'required|exists:staff,id',
            'vacation_type_id'=> 'required|exists:vacation_types,id',
            'from_date'=> 'required|date_format:"Y-m-d"',
            'to_date'=> 'required|after_or_equal:"'.$request->from_date.'"',
        ]);

        $theRequest = $request->only([
            'staff_id',
            'vacation_type_id',
            'from_date',
            'to_date',
            'comment',
        ]);

        $theRequest['created_by_staff_id'] = Auth::id();

       $staff = Staff::where('id',$request->staff_id)->first();
       $vacationTypedate = VacationTypes::where('id',$request->vacation_type_id)->first()->after_months;

        $joining = Carbon::createFromFormat('Y-m-d',$staff->joining_date);
        $now = Carbon::createFromFormat('Y-m-d',date('Y-m-d'));

         $diffBetweenJoingingAndVacationTypeDate = $now->diffInDays($joining);

         if ($diffBetweenJoingingAndVacationTypeDate < $vacationTypedate*30){
             return redirect()
                 ->route('system.vacation.create')
                 ->with('status', 'danger')
                 ->with('msg', __('Sorry Couldn\'t add Vacation Before ' .($vacationTypedate*30). ' Day of Joining Date and '.$staff->Fullname . ' Has Join from ' .$diffBetweenJoingingAndVacationTypeDate. ' Days'));
         }

        $from = Carbon::createFromFormat('Y-m-d',$request->from_date);
        $to   =  Carbon::createFromFormat('Y-m-d', $request->to_date);
        $theRequest['num_of_days'] = $to->diffInDays($from);
        $vacation = Vacation::create($theRequest);
        if ($vacation)
            return redirect()
                ->route('system.vacation.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        else {
            return redirect()
                ->route('system.vacation.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t add Vacation'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */

    public function show(Vacation $vacation)
    {
        //dd($supplier_category);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Vacations'),
                'url' => route('system.vacation.index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Vacations';
        $this->viewData['result'] = $vacation;
        return $this->view('vacation.show', $this->viewData);
    }

    public function edit(Vacation $vacation)
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Vacation'),
            'url' => route('system.vacation.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Vacation'),
        ];

        $return = [];
        $data = VacationTypes::get(['id', 'name']);
        foreach ($data as $key => $value) {
            $return[$value->id] = $value->name;
        }
        $this->viewData['vacation_types'] = $return;
        $this->viewData['pageTitle'] = __('Edit Vacation');
        $this->viewData['result'] = $vacation;

        return $this->view('vacation.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Vacation $vacation)
    {
        $this->validate($request,[

            'staff_id'=> 'required|exists:staff,id',
            'vacation_type_id'=> 'required|exists:vacation_types,id',
            'from_date'=> 'required|date_format:"Y-m-d"',
            'to_date'=> 'required|after_or_equal:"'.$request->from_date.'"',
        ]);

        $theRequest = $request->only([
            'staff_id',
            'vacation_type_id',
            'from_date',
            'to_date',
            'comment',
        ]);
        if ($request->has('staff_id')){
            $theRequest['staff_id'] = $request->staff_id;
        }else{
            unset($theRequest['staff_id']);
        }


        $staff = Staff::where('id',$request->staff_id)->first();
        $vacationTypedate = VacationTypes::where('id',$request->vacation_type_id)->first()->after_months;

        $joining = Carbon::createFromFormat('Y-m-d',$staff->joining_date);
        $now = Carbon::createFromFormat('Y-m-d',date('Y-m-d'));

        $diffBetweenJoingingAndVacationTypeDate = $now->diffInDays($joining);

        if ($diffBetweenJoingingAndVacationTypeDate < $vacationTypedate*30){
            return redirect()
                ->route('system.vacation.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Vacation Before ' .($vacationTypedate*30). ' Day of Joining Date and '.$staff->Fullname . ' Has Join from ' .$diffBetweenJoingingAndVacationTypeDate. ' Days'));
        }


        $from = Carbon::createFromFormat('Y-m-d',$request->from_date);
        $to   =  Carbon::createFromFormat('Y-m-d', $request->to_date);
        $theRequest['num_of_days'] = $to->diffInDays($from);

        if ($vacation->update($theRequest)) {
          //  dd($category);
            return redirect()
                ->route('system.vacation.edit', $vacation->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Vacation'));
        }
        else {
            return redirect()
                ->route('system.vacation.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Vacation'));
        }
    }

    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,Vacation $vacation)
    {
        $vacation->delete();
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
