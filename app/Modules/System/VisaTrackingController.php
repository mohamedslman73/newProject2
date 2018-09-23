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
use App\Models\VisaTracking;
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


class VisaTrackingController extends SystemController
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

            $eloquentData = VisaTracking::select([
                'id',
                'staff_name',
                'nationality',
                'date_of_visa_issue',
                'visa_no',
                'gender',
                'visa_status',
                'passport_no',
                'joining_date',
                'created_at',
                'staff_id',
            ]);
            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            /*
             * Start handling filter
             */

            whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);

            if ($request->id) {
                $eloquentData->where('id', '=', $request->id);
            }

            if ($request->name) {
                $eloquentData->where('staff_name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->visa_number) {
                $eloquentData->where('visa_no', '=', $request->visa_number);
            }
            if ($request->nationality){
                $eloquentData->where('nationality','LIKE','%'.$request->nationality.'%');
            }
            if ($request->passport_no){
                $eloquentData->where('passport_no','=',$request->passport_no);
            }
            if ($request->gender) {
                $eloquentData->where('gender', '=', $request->gender);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')

                ->addColumn('staff_name','{{$staff_name}}')
                ->addColumn('passport_no',function ($data){
                    return '<code>'.$data->passport_no .'</code>';
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
                                        <td>'.$data->visa_no.'</td>
                                    </tr>
                                    <tr>
                                        <td>'.__('Visa Status').'</td>
                                        <td>'.$data->visa_status.'</td>
                                    </tr>
                                </tbody>
                            </table>';
                })

                ->addColumn('created_at', function ($data) {
                        return $data->created_at->format('Y-m-d h:iA');
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.visa-tracking.show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a href=\"" . route('system.visa-tracking.edit', $data->id) . "\">" . __('Edit') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.visa-tracking.destroy', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [
                __('ID'),
                __('Name'),
                __('Passport Number'),
                __('Nationality'),
                __('Visa Issue'),
                __('Created At'),
                __('Action')
            ];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Visa Tracking')
            ];


            $return = [];
            $data = Country::get(['id', 'country_name']);
            foreach ($data as $key => $value) {
                $return[$value->country_name] = $value->country_name;
            }
            $this->viewData['country_name'] = $return;

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Visa Tracking');
            } else {
                $this->viewData['pageTitle'] = __('Visa Tracking');
            }
            return $this->view('visa-tracking.index', $this->viewData);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->viewData['breadcrumb'][] = [
            'text' => __('Visa Tracking'),
            'url' => route('system.visa-tracking.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Create Visa Tracking'),
        ];

        $this->viewData['pageTitle'] = __('Create Visa Tracking');
        $return = [];
        $data = Country::get(['id', 'country_name']);
        foreach ($data as $key => $value) {
            $return[$value->country_name] = $value->country_name;
        }
        $this->viewData['country_name'] = $return;

        return $this->view('visa-tracking.create', $this->viewData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'staff_name' => 'required',
            'visa_status' => 'required|in:arrived,cancelled,pending',
            'visa_no' => 'required',
            'id_no' => 'required|unique:visa_tracking,id_no',
           'passport_no' => 'required|unique:visa_tracking,passport_no',
            'gender' => 'required|in:male,female',
            'date_of_visa_issue' => 'required|date_format:"Y-m-d"',
            'joining_date' => 'required|date_format:"Y-m-d"',
            'nationality' => 'required',
        ]);

        $theRequest = $request->all();


        $theRequest['staff_id'] = Auth::id();
        if ($insertedStaff = VisaTracking::create($theRequest)) {
            return redirect()
                ->route('system.visa-tracking.create')
                ->with('status', 'success')
                ->with('msg', __('Data has been added successfully'));
        } else {
            return redirect()
                ->route('system.visa-tracking.create')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Add Visa Tracking'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(VisaTracking $visaTracking, Request $request)
    {

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Visa Tracking'),
                'url' => route('system.visa-tracking.index'),
            ],
            [
                'text' => $visaTracking->staff_name,
            ]
        ];

        $this->viewData['pageTitle'] = __('Show Staff');

        //  $this->viewData['totalActivity']  = Activity::count();

        $this->viewData['result'] = $visaTracking;
        return $this->view('visa-tracking.show', $this->viewData);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(VisaTracking $visaTracking, Request $request)
    {

        $this->viewData['breadcrumb'][] = [
            'text' => __('Visa Tracking'),
            'url' => route('system.visa-tracking.index')
        ];

        $this->viewData['breadcrumb'][] = [
            'text' => __('Edit Visa Tracking'),
        ];
        $return = [];
        $data = Country::get(['id', 'country_name']);
        foreach ($data as $key => $value) {
            $return[$value->country_name] = $value->country_name;
        }
        $this->viewData['country_name'] = $return;

        $this->viewData['pageTitle'] = __('Edit Staff');
        $this->viewData['result'] = $visaTracking;

        return $this->view('visa-tracking.create', $this->viewData);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VisaTracking $visaTracking)
    {
     //  dd($request->all());
        $this->validate($request, [
            'staff_name'                     => 'required',
            'visa_status'                    => 'required|in:arrived,cancelled,pending',
            'visa_no'                        => 'required',
            'id_no'                          => 'required|unique:visa_tracking,id_no'.iif($request->id, ',' . $visaTracking->id),

            'passport_no'                    => 'required|unique:visa_tracking,passport_no'.iif($request->id, ',' . $visaTracking->id),

            'gender'                         => 'required|in:male,female',
            'date_of_visa_issue'             => 'required|date_format:"Y-m-d"',
            'joining_date'                   => 'required|date_format:"Y-m-d"',
            'nationality'                    => 'required',
        ]);

        $theRequest = $request->all();


        if ($visaTracking->update($theRequest)) {

            return redirect()
                ->route('system.visa-tracking.edit', $visaTracking->id)
                ->with('status', 'success')
                ->with('msg', __('Successfully Edit Visa Tracking'));
        } else {
            return redirect()
                ->route('system.visa-tracking.edit')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Edit Visa Tracking'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(VisaTracking $visaTracking , Request $request)
    {
        $visaTracking->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Staff has been deleted successfully')];
        } else {
            redirect()
                ->route('system.visa-tracking.index')
                ->with('status', 'success')
                ->with('msg', __('This Visa Tracking has been deleted'));
        }
    }



}
