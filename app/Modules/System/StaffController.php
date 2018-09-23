<?php

namespace App\Modules\System;

use App\Libs\WalletData;
use App\Models\Certificate;
use App\Models\Clothe;
use App\Models\Country;
use App\Models\Merchant;
use App\Models\PaymentInvoice;
use App\Models\PermissionGroup;
use App\Models\Staff;
use App\Models\StaffClothes;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;
use Form;
use App\Http\Requests\StaffFormRequest;
use Spatie\Activitylog\Models\Activity;
use App\Libs\Create;
use App\Notifications\UserNotification;
use App\Libs\SMS;


class StaffController extends SystemController
{

    public function __construct()
    {
        parent::__construct();
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ]
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->isDataTable) {

            $eloquentData = Staff::select([
                'id',
                'firstname',
                'lastname',
                'avatar',
                'nationality',
                'date_of_visa_issue',
                'visa_number',
                'visa_status',
                'job_title',
                'cancel_date',
                'created_at',
                'staff_id',
            ]);
            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('staff.id', '=', $request->id);
            }

            if ($request->name) {
                $eloquentData->where(DB::raw("CONCAT(firstname,' ',lastname)"), 'LIKE', '%' . $request->name . '%');
            }
            if ($request->visa_number) {
                $eloquentData->where('staff.visa_number', '=', $request->visa_number);
            }

            if ($request->gender) {
                $eloquentData->where('gender', '=', $request->gender);
            }
            if ($request->permission_group_id) {
                $eloquentData->where('permission_group_id', '=', $request->permission_group_id);
            }

            whereBetween($eloquentData, 'birthdate', $request->birthdate1, $request->birthdate2);

            if ($request->job_title) {
                $eloquentData->where('job_title', 'LIKE', '%' . $request->job_title . '%');
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('firstname', function ($data) {
                    return $data->firstname . ' ' . $data->lastname;
                })
                ->addColumn('nationality', function ($data) {
                    if ($data->nationality) {
                        return $data->nationality;
                    }
                    return '--';
                })

                ->addColumn('date_of_visa_issue',function($data){
                    return '<table class="table">
                                <tbody>
                                    <tr>
                                        <td>'.__('Date Of Visa Issue').'</td>
                                        <td>'.$data->date_of_visa_issue.'</td>
                                    </tr>
                                    <tr>
                                        <td>'.__('Visa Number').'</td>
                                        <td>'.$data->visa_number.'</td>
                                    </tr>
                                    <tr>
                                        <td>'.__('Visa Status').'</td>
                                        <td>'.$data->visa_status.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                })
                ->addColumn('cancel_date', function ($data) {
                    if ($data->cancel_date) {
                        return $data->cancel_date;
                    }
                    return '--';
                })
                ->addColumn('created_at', function ($data) {
                    if ($data->created_at) {
                        return $data->created_at->format('Y-m-d h:iA');
                    }
                    return '--';
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.staff.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.staff.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.staff.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Nationality'),
                __('Visa Issue'),
                __('Cancel Date'),
                __('Created At'),
                __('Action')
            ];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Staff')
            ];
            $this->viewData['PermissionGroup'] = array_column(PermissionGroup::get()->toArray(),'name','id');

            $return = [];
            $data = Country::get(['id', 'country_name']);
            foreach ($data as $key => $value) {
                $return[$value->country_name] = $value->country_name;
            }
            $this->viewData['country_name'] = $return;

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Staff');
            } else {
                $this->viewData['pageTitle'] = __('Staff');
            }
            return $this->view('staff.index', $this->viewData);
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
            'text' => __('Staff'),
            'url' => route('system.staff.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Staff'),
        ];
        $this->viewData['PermissionGroup'] = array_column(PermissionGroup::get()->toArray(),'name','id');
        $this->viewData['certificate'] = array_column(Certificate::get()->toArray(),'name','id');
        $this->viewData['clothe'] = array_column(Clothe::get()->toArray(),'name','id');

        $this->viewData['pageTitle'] = __('Create Staff');
        $return = [];
        $data = Country::get(['id', 'country_name']);
        foreach ($data as $key => $value) {
            $return[$value->country_name] = $value->country_name;
        }
        $this->viewData['country_name'] = $return;

        return $this->view('staff.create', $this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation =  [
            'firstname' => 'required',
            'lastname' => 'required',
            'visa_status' => 'required|in:yes,no',
            'visa_number' => 'required',
            'passport_number' => 'required|unique:staff,passport_number',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date_format:"Y-m-d"',
            'date_of_visa_issue' => 'required|date_format:"Y-m-d"',
            'joining_date' => 'required|date_format:"Y-m-d"',
            'salary' => 'required',
            'job_title' => 'required',
            'nationality' => 'required',
            'permission_group_id' => 'required|exists:permission_groups,id',
        ];


     //   dd($request->all());
        if (!empty($request->clothe_id)){
            $validation['clothe_id'] = 'required|array';
            $validation['clothe_id.*'] ='required|exists:clothes,id';
            $validation['size'] = 'required|array';
            $validation['size.*'] ='required';
        }

        $this->validate($request,$validation);

        $theRequest = $request->all();
//        dd($theRequest);
//        $joining_date = $request->joining_date;
//
//        $today = Carbon::now()->format('Y-m-d');
//        $from = \Carbon\Carbon::createFromFormat('Y-m-d', $joining_date);
//        $to = \Carbon\Carbon::createFromFormat('Y-m-d', $today);
//        $theRequest['lenth_of_services'] = $to->diffInDays($from);

        if ($request->has('weekly_vacations')) {
            $theRequest['weekly_vacations'] = implode(',', $request->weekly_vacations);
        }
        if ($request->file('avatar')) {
            $theRequest['avatar'] = $request->avatar->store('staff/' . date('y') . '/' . date('m'));
        }
        if($request->certificate_id){
            $theRequest['certificate'] = implode(',',$request->certificate_id);
        }


        $theRequest['staff_id'] = Auth::id();
        if ($insertedStaff = Staff::create($theRequest)) {
          //  dd($insertedStaff->id);
            if (!empty($request->clothe_id)){
                foreach ($request->clothe_id as $key=>$value){
                   // dd($insertedStaff->id);
                StaffClothes::create([
                    'clothe_id' =>$value,
                    'size'      =>   $request->size[$key],
                    'cleaner_id' =>$insertedStaff->id,
                    'staff_id' =>Auth::id(),
                ]);
                }
            }
            return redirect()
                ->route('system.staff.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        } else {
            return redirect()
                ->route('system.staff.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Add Staff'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(Staff $staff, Request $request)
    {
       // dd($staff->toArray());
        //lenth_of_services
        //joining_date

        $joining_date = $staff->joining_date;
        $today = Carbon::now()->format('Y-m-d');


        if ($staff->termination_date !=null){
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', $joining_date);
            $to = \Carbon\Carbon::createFromFormat('Y-m-d', $staff->termination_date);
            $lenth_of_services = $to->diffInDays($from);
        }else{
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', $joining_date);
            $to = \Carbon\Carbon::createFromFormat('Y-m-d', $today);
            $lenth_of_services = $to->diffInDays($from);
        }

      //  dd($staff->toArray());
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Staff'),
                'url' => route('system.staff.index'),
            ],
            [
                'text' => $staff->firstname . ' ' . $staff->lastname,
            ]
        ];

        $this->viewData['pageTitle'] = __('Show Staff');

        $this->viewData['weekly_vacations'] = explode(',',$staff->weekly_vacations);
        $this->viewData['lenth_of_services'] = $lenth_of_services;

        $this->viewData['result'] = $staff;
        return $this->view('staff.show', $this->viewData);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(Staff $staff, Request $request)
    {
      //dd($staff->toArray());
        // Main View Vars
        $this->viewData['breadcrumb'][] = [
            'text' => __('Staff'),
            'url' => route('system.staff.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Staff'),
        ];
        $return = [];
        $data = Country::get(['id', 'country_name']);
        foreach ($data as $key => $value) {
            $return[$value->country_name] = $value->country_name;
        }
        $this->viewData['country_name'] = $return;

        $this->viewData['PermissionGroup'] = array_column(PermissionGroup::get()->toArray(),'name','id');
        $this->viewData['certificate'] = array_column(Certificate::get()->toArray(),'name','id');
        $this->viewData['clothe'] = array_column(Clothe::get()->toArray(),'name','id');

        $this->viewData['staff_clothes'] =  StaffClothes::where('cleaner_id',$staff->id)->get()->toArray();
        $this->viewData['pageTitle'] = __('Edit Staff');
        $this->viewData['result'] = $staff;
        $this->viewData['weekly_vacations'] = explode(',',$staff->weekly_vacations);
      //  dd($this->viewData['weekly_vacations']);
        return $this->view('staff.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Staff $staff)
    {
      //  dd($request->all());

        $this->validate($request, [
            'firstname' => 'required',
            'lastname' => 'required',
            'visa_status' => 'required|in:arrived,cancelled,pending',
            'visa_number' => 'required',
            'passport_number' => 'required|unique:staff,passport_number' . iif($request->id, ',' . $staff->id),
            'avatar' => 'image',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date_format:"Y-m-d"',
            'date_of_visa_issue' => 'required|date_format:"Y-m-d"',
            'bank_account' => 'required',
            'salary' => 'required',
            'job_title' => 'required',
            'nationality' => 'required',
        ]);
        $theRequest = $request->all();
        if ($request->file('avatar')) {
            $theRequest['avatar'] = $request->avatar->store('staff/' . date('y') . '/' . date('m'));
        } else {
            unset($theRequest['avatar']);
        }

        if ($request->has('weekly_vacations')) {
            $theRequest['weekly_vacations'] = implode(',', $request->weekly_vacations);
        }else{
            unset($theRequest['weekly_vacations']);
        }

        if ($staff->update($theRequest)) {

            return redirect()
                ->route('system.staff.edit', $staff->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Staff'));
        } else {
            return redirect()
                ->route('system.staff.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Staff'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staff $staff, Request $request)
    {
        $staff->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Staff has been deleted successfully')];
        } else {
            redirect()
                ->route('system.staff.index')
                ->with('status', 'success')
                ->with('msg', __('This Staff has been deleted'));
        }
    }

    public function editInfo(Request $request)
    {

        $staff = Staff::find($request->id);
        $theReuest = [];
        if (!empty($request->medical)) {
            $theReuest['medical'] = $request->medical;
        }
        if (!empty($request->blood)) {
            $theReuest['blood'] = $request->blood;
        }

        if (!empty($request->finger_print)) {
            $theReuest['finger_print'] = $request->finger_print;
        }
        if (!empty($request->government_id)) {
            $theReuest['government_id'] = $request->government_id;
        }
        if (!empty($theReuest)) {
            if ($staff->update($theReuest)) {
                $staff->where('blood', '=', 'no')
                    ->orWhere('medical', '=', 'no')
                    ->orWhere('finger_print', '=', 'no')
                    ->update(['cancel_date' => Carbon::now()]);
                return response(['status' => true, 'msg' => __('Data successfully changed')]);
            }
        }
        return response(['status' => false, 'msg' => __('Date Can\'t changed')]);
    }


    function visaReport(Request $request)
    {


        if ($request->isDataTable) {

            $eloquentData = Staff::where('visa_status', 'no')
                ->where('status', 'active')
                ->where('joining_date', '<=', date('Y-m-d', strtotime(date('Y-m-d') . '-' . setting("daily_visa_limit") . ' days')));


            if ($request->id) {
                $eloquentData->where('staff.id', '=', $request->id);
            }

            if ($request->name) {
                $eloquentData->where(DB::raw("CONCAT(firstname,' ',lastname)"), 'LIKE', '%' . $request->name . '%');
            }
            if ($request->visa_number) {
                $eloquentData->where('staff.visa_number', '=', $request->visa_number);
            }

            if ($request->gender) {
                $eloquentData->where('gender', '=', $request->gender);
            }

            whereBetween($eloquentData, 'birthdate', $request->birthdate1, $request->birthdate2);

            if ($request->job_title) {
                $eloquentData->where('job_title', 'LIKE', '%' . $request->job_title . '%');
            }


            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('firstname', function ($data) {
                    return $data->firstname . ' ' . $data->lastname;
                })
                ->addColumn('joining_date', '{{$joining_date}}')
                ->addColumn('staff_id', function ($data) {
                    if ($data->staff_id) {
                        $staff = Staff::where('id', $data->staff_id)->first();
                        //return '<a href="{{route()}}"></a>';
                        return "<a target='_blank' href=\"" . route('system.staff.show', $staff->id) . "\">" . __($staff->firstname . ' ' . $staff->lastname) . "</a>";
                    }
                    return '--';
                })
                ->addColumn('created_at', function ($data) {
                    if ($data->created_at) {
                        return $data->created_at->diffForHumans();
                    }
                    return '--';
                })
                ->make(true);
        } else {

            // View Data
            $this->viewData['tableColumns'] = [__('ID'),
                __('Name'),
                __('Joining Date'),
                __('Created By'),
                __('Created At')];
            $this->viewData['breadcrumb'][] = ['text' => __('Staff')];


            $this->viewData['pageTitle'] = __('Visa Report');

            return $this->view('staff.visa-report', $this->viewData);

        }

    }
    public function staffTerminate(Request $request,Staff $staff){
        $staff->update(['termination_date'=>date('Y-m-d'),'status'=>'in-active']);
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Staff has been Terminated successfully')];
        } else {
            redirect()
                ->route('system.staff.show',$staff->id)
                ->with('status', 'success')
                ->with('msg', __('Staff has been Terminated successfully'));
        }

    }


}
