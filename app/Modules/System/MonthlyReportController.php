<?php

namespace App\Modules\System;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\ClientOrderBack;
use App\Models\ClientOrders;
use App\Models\Deduction;
use App\Models\Expense;
use App\Models\MonthlyReport;
use App\Models\Overtime;
use App\Models\Profit;
use App\Models\Staff;
use App\Models\SupplierOrderBack;
use App\Models\SupplierOrders;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;

class MonthlyReportController extends SystemController
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

    public function monthlyReportIndex(Request $request)
    {

        if ($request->isDataTable) {
            $eloquentData = MonthlyReport::select([
                'id',
                'cleaner_id',
                'date',
                'staff_id',
                'total_money_staff',
                'created_at'
            ]);

            if ($request->withTrashed) {
                $eloquentData->onlyTrashed();
            }

            whereBetween($eloquentData, 'DATE(created_at)', $request->created_at1, $request->created_at2);
            whereBetween($eloquentData, 'DATE(date)', $request->date1, $request->date2);
            whereBetween($eloquentData, 'total_money_staff', $request->total_money_staff1, $request->total_money_staff2);

            if ($request->id) {
                $eloquentData->where('id', '=', $request->id);
            }
            if ($request->cleaner_id) {
                $eloquentData->where('cleaner_id', '=', $request->cleaner_id);
            }
            if ($request->staff_id) {
                $eloquentData->where('staff_id', '=', $request->staff_id);
            }

            return Datatables::eloquent($eloquentData)
                ->addColumn('id', '{{$id}}')
                ->addColumn('date', function ($data) {
                    return date('Y-m', strtotime($data->date));
                })
                ->addColumn('total_money_staff', function ($data) {
                    return amount($data->total_money_staff, true);
                })
                ->addColumn('cleaner_name', function ($data) {
                    return '<a href="' . route("system.staff.show", $data->cleaner_id) . '" target="_blank">' . $data->cleaner->Fullname . '</a>';
                })
                ->addColumn('staff_name', function ($data) {
                    return '<a href="' . route("system.staff.show", $data->staff_id) . '" target="_blank">' . $data->staff->Fullname . '</a>';
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at->diffForHumans();
                })
                ->addColumn('action', function ($data) {
                    return " <div class=\"dropdown\">
                              <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\"><i class=\"ft-cog icon-left\"></i>
                              <span class=\"caret\"></span></button>
                              <ul class=\"dropdown-menu\">
                                <li class=\"dropdown-item\"><a href=\"" . route('system.monthly-report-show', $data->id) . "\">" . __('View') . "</a></li>
                                <li class=\"dropdown-item\"><a onclick=\"deleteRecord('" . route('system.monthly-report-delete', $data->id) . "')\" href=\"javascript:void(0)\">" . __('Delete') . "</a></li>
                              </ul>
                            </div>";
                })
                ->make(true);
        } else {
            // View Data
            $this->viewData['tableColumns'] = [__('ID'), __('Date'), __('Total Money'), __('Cleaner Name'), __('Created By'), __('Created At'), __('Action')];
            $this->viewData['breadcrumb'][] = [
                'text' => __('Monthly Reports')
            ];

            if ($request->withTrashed) {
                $this->viewData['pageTitle'] = __('Deleted Monthly Reports');
            } else {
                $this->viewData['pageTitle'] = __('Monthly Reports');
            }

            return $this->view('monthly-report.index', $this->viewData);
        }
    }


    public function show(MonthlyReport $report)
    {
        //dd($report);
        //dd($supplier_category);
        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Monthly Report'),
                'url' => route('system.monthly-report-index'),
            ],
            [
                'text' => 'Show',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Monthly Report';
        $this->viewData['result'] = $report;
        return $this->view('monthly-report.show', $this->viewData);
    }


    /**
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */


    public function monthlyReport()
    {

        $this->viewData['breadcrumb'] = [
            [
                'text' => __('Home'),
                'url' => url('system'),
            ],
            [
                'text' => __('Monthly Report'),
                'url' => route('system.monthly-report-index'),
            ],
            [
                'text' => 'Create',
            ]
        ];
//
//
        $this->viewData['pageTitle'] = 'Monthly Report';
        return $this->view('attendance.report.index', $this->viewData);

    }


    public function monthlyReportCalc(Request $request)
    {


        $array['date'] = 'required';
        if ($request->staff_id)
            $array['staff_id'] = 'required|exists:staff,id';

        $this->validate($request, $array);


        $reportEloquentData = MonthlyReport::where('cleaner_id', $request->staff_id)
            ->where('date', '>=', $request->date . '-01')
            ->where('date', '<=', $request->date . '-31');

        if (!empty($reportEloquentData->first())) {

            return redirect()
                ->route('system.attendance.monthly-report')
                ->with('status', 'danger')
                ->with('msg', __('Sorry Couldn\'t Create the Same Report Twice in the same Day'));
        }
        $staff = Staff::find($request->staff_id);

        // weekly vications
        $weeklyVications = explode(',', $staff['weekly_vacations']);
        $salaryByDay = round($staff->salary / 30, 0);

        // Attendance of Staff
        $AttendanceEloquentData = Attendance::where('cleaner_id', $request->staff_id)->selectRaw('*,DAY(date) as day');
        whereBetween($AttendanceEloquentData, 'DATE(date)', $request->date . '-1', $request->date . '-31');

        // calculate Presence Days  $AttendancePresenceDays[day] = id;
        $AttendancePresenceEloquentData = clone $AttendanceEloquentData;
        $AttendancePresenceDays = array_column($AttendancePresenceEloquentData->where('type', 'presence')->get()->toArray(), 'id', 'day');
        // calculate Absence Days  $AttendanceAbsenceDays[day] = id;
        $AttendanceAbsenceEloquentData = clone $AttendanceEloquentData;
        $AttendanceAbsenceDays = array_column($AttendanceAbsenceEloquentData->where('type', 'absence')->get()->toArray(), 'id', 'day');


        //Vacation of staff   $VicationsDays[day] =  type
        $VicationsEloquentData = Vacation::where(['added_to' => $request->staff_id, 'decision' => 'approved']);
        whereBetween($VicationsEloquentData, 'DATE(vacation_start)', $request->date . '-1', $request->date . '-31');
        $Vications = $VicationsEloquentData->get();
        $VicationsDays = [];      // calculate days  $VicationsDays[day] = id;

        foreach ($Vications as $row) {

            if ($row->num_of_days > 1) {
                for ($eachVacationDay = 1; $eachVacationDay <= $row->num_of_days; $eachVacationDay++) {

                    if ($eachVacationDay == 1) {

                        $eachVacationDayDate = $row->vacation_start;
                        $VicationsDays[explode('-', $eachVacationDayDate)[2]] = $row->type;

                    } else {
                        $eachVacationDayDate = date('Y-m-d', strtotime($row->vacation_start . ' + ' . $eachVacationDay . ' days'));
                        $VicationsDays[explode('-', $eachVacationDayDate)[2]] = $row->type;
                    }
                }
            } else {
                $VicationsDays[explode('-', $row->vacation_start)[2]] = $row->type;
            }

        }


        // overtime of staff   $overtimeDays[day] = money;
        $overtimeEloquentData = Overtime::where(['added_to' => $request->staff_id])->selectRaw('*,DAY(date) as day');
        whereBetween($overtimeEloquentData, 'DATE(date)', $request->date . '-1', $request->date . '-31');
        $overtimeDays = array_column($overtimeEloquentData->get()->toArray(), 'total_added_money', 'day');
        $overtimeHours = $overtimeEloquentData->sum('hours');


        // deductions of staff  $deductionDays[day] = amount
        $deductionsEloquentData = Deduction::where('deduction_from', $request->staff_id)->selectRaw('*,DAY(date) as day');
        whereBetween($deductionsEloquentData, 'DATE(date)', $request->date . '-1', $request->date . '-31');
        $deductionDays = array_column($deductionsEloquentData->get()->toArray(), 'amount', 'day');


        // days of month
        $DAYS = cal_days_in_month(CAL_GREGORIAN, explode('-', $request->date)[1], explode('-', $request->date)[0]);


        $totalMoneyPresence = 0;
        $totalMoneyWeeklyVacation = 0;
        $totalMoneyAbsence = 0;
        $totalMoneyPaidVacations = 0;
        $totalMoneyUnPaidVacations = 0;
        $totalMoneyOvertime = 0;
        $totalMoneyDeduction = 0;
        $totalMoneyStaff = 0;


        for ($dayCounter = 01; $dayCounter <= $DAYS; $dayCounter++) {

            $dayDate = $request->date . '-' . $dayCounter;
            $dayName = strtolower(date('D', strtotime($dayDate)));

            // add money if Presence this day
            if (array_key_exists($dayCounter, $AttendancePresenceDays)) {
                $totalMoneyPresence += $salaryByDay;
                $totalMoneyStaff += $salaryByDay;
            }


            // add money if Weekly Vacation this Day
            if (in_array($dayName, $weeklyVications)) {
                $totalMoneyWeeklyVacation += $salaryByDay;
                $totalMoneyStaff += $salaryByDay;
            }


            // minus money if Absence this day
            if (array_key_exists($dayCounter, $AttendanceAbsenceDays)) {
                $totalMoneyAbsence += $salaryByDay;
                $totalMoneyStaff -= $salaryByDay;
            }

            // add or minus money if paid or unpaid Vacation this Day
            if (array_key_exists($dayCounter, $VicationsDays)) {
                if ($VicationsDays[$dayCounter] == 'paid') {
                    $totalMoneyPaidVacations += $salaryByDay;
                    $totalMoneyStaff += $salaryByDay;
                }
                if ($VicationsDays[$dayCounter] == 'un-paid') {
                    $totalMoneyUnPaidVacations += $salaryByDay;
                    $totalMoneyStaff -= $salaryByDay;
                }
            }


            // add money if Overtime in this Day
            if (array_key_exists($dayCounter, $overtimeDays)) {
                $totalMoneyOvertime += $overtimeDays[$dayCounter];
                $totalMoneyStaff += $overtimeDays[$dayCounter];
            }


            // minus money if Deduction in this Day
            if (array_key_exists($dayCounter, $deductionDays)) {
                $totalMoneyDeduction += $deductionDays[$dayCounter];
                $totalMoneyStaff -= $deductionDays[$dayCounter];
            }


        }

        $total = [
            'totalDaysPresence' => count($AttendancePresenceDays),
            'totalMoneyPresence' => $totalMoneyPresence,
            'totalDaysWeeklyVacation' => count($weeklyVications),
            'totalMoneyWeeklyVacation' => $totalMoneyWeeklyVacation,
            'totalDaysAbsence' => count($AttendanceAbsenceDays),
            'totalMoneyAbsence' => $totalMoneyAbsence,
            'totalDaysPaidVacations' => $totalMoneyPaidVacations / $salaryByDay,
            'totalMoneyPaidVacations' => $totalMoneyPaidVacations,
            'totalDaysUnPaidVacations' => $totalMoneyUnPaidVacations / $salaryByDay,
            'totalMoneyUnPaidVacations' => $totalMoneyUnPaidVacations,
            'totalHoursOvertime' => $overtimeHours,
            'totalMoneyOvertime' => $totalMoneyOvertime,
            'totalDaysDeduction' => count($deductionDays),
            'totalMoneyDeduction' => $totalMoneyDeduction,
            'totalMoneyStaff' => $totalMoneyStaff
        ];


        $this->viewData['result'] = $total;
        $this->viewData['result']['staff_id'] = $staff->id;
        $this->viewData['result']['date'] = $request->date;
        $this->viewData['result']['serialized'] = serialize($this->viewData['result']);
        $this->viewData['pageTitle'] = __('Calculate Monthly Report For ' . $staff->fullName);

        return $this->view('attendance.report.calculate', $this->viewData);

    }


    function monthlyReportSave(Request $request)
    {
        $data = unserialize($request->data);
        if (empty($data))
            return ['status' => false, 'msg' => 'Empty Data'];
        else {


            $reportEloquentData = MonthlyReport::where('cleaner_id', $data['staff_id'])
                ->where('date', '>=', $data['date'] . '-01')
                ->where('date', '<=', $data['date'] . '-31');


            if (!empty($reportEloquentData->first())) {
                return ['status' => false, 'msg' => 'Report Already Saved'];
            }
            $reportData = [
                'cleaner_id' => $data['staff_id'],
                'date' => $data['date'] . '-01',
                'total_days_presence' => $data['totalDaysPresence'],
                'total_money_presence' => $data['totalMoneyPresence'],
                'total_days_weekly_vacation' => $data['totalDaysWeeklyVacation'],
                'total_money_weekly_vacation' => $data['totalMoneyWeeklyVacation'],
                'total_days_absence' => $data['totalDaysAbsence'],
                'total_money_absence' => $data['totalMoneyAbsence'],
                'total_days_paid_vacations' => $data['totalDaysPaidVacations'],
                'total_money_paid_vacations' => $data['totalMoneyPaidVacations'],
                'total_days_unpaid_vacations' => $data['totalDaysUnPaidVacations'],
                'total_money_unpaid_vacations' => $data['totalMoneyUnPaidVacations'],
                'total_days_overtime' => $data['totalHoursOvertime'],
                'total_money_overtime' => $data['totalMoneyOvertime'],
                'total_days_deduction' => $data['totalDaysDeduction'],
                'total_money_deduction' => $data['totalMoneyDeduction'],
                'total_money_staff' => $data['totalMoneyStaff'],
                'staff_id' => Auth::id()
            ];

            if (MonthlyReport::create($reportData)) {
                return ['status' => true, 'msg' => 'Report Saved'];
            }

        }
    }


    public function destroy(Request $request, MonthlyReport $report)
    {
        $report->delete();
        if ($request->ajax()) {
            return ['status' => true, 'msg' => __('Brand has been deleted successfully')];
        } else {
            redirect()
                ->route('system.brand.index')
                ->with('status', 'success')
                ->with('msg', __('This Brand has been deleted'));
        }
    }


    public function summery(Request $request)
    {


        if(!$request->date)
            $request->date = date('Y-m');


        // supplier orders
        $supplierOrders = SupplierOrders::select(['id', 'total_price', 'supplier_id']);
        whereBetween($supplierOrders, 'DATE(created_at)', $request->date . '-1', $request->date . '-31');


        // supplier orders back
        $supplierOrdersBack = SupplierOrderBack::select(['id', 'total_price', 'supplier_id']);
        whereBetween($supplierOrdersBack, 'DATE(created_at)', $request->date . '-1', $request->date . '-31');


        // client orders
        $clientOrders = ClientOrders::select(['id', 'total_price', 'client_id']);
        whereBetween($clientOrders, 'DATE(created_at)', $request->date . '-1', $request->date . '-31');

        // client orders back
        $clientOrdersBack = ClientOrderBack::select(['id', 'total_price', 'client_id']);
        whereBetween($clientOrdersBack, 'DATE(created_at)', $request->date . '-1', $request->date . '-31');

        // total revenue
        $revenue = Profit::select(['id', 'amount', 'client_id']);
        whereBetween($revenue, 'DATE(date)', $request->date . '-1', $request->date . '-31');

        // total expense
        $expense = Expense::select(['id', 'amount', 'supplier_id']);
        whereBetween($expense, 'DATE(date)', $request->date . '-1', $request->date . '-31');

        // total expense without supplier deposits
        $expenseWithOutSupplierDeposit = clone $expense;
        $expenseWithOutSupplierDeposit->where('expense_causes_id', '!=', 1)->sum('amount');

        // total of supplier deposits
        $expenseOfSupplierDeposit = clone $expense;
        $expenseOfSupplierDeposit->where('expense_causes_id', '=', 1)->sum('amount');

        // total revenue without client deposits
        $revenueWithOutClientDeposit = clone $revenue;
        $revenueWithOutClientDeposit->where('revenue_causes_id', '!=', 1)->sum('amount');

        // total revenue of client deposits
        $revenueOfClientDeposit = clone $revenue;
        $revenueOfClientDeposit->where('revenue_causes_id', '=', 1)->sum('amount');

        $data = [
            'supplierOrders' => $supplierOrders->sum('total_price'),
            'supplierOrdersBack' => $supplierOrdersBack->sum('total_price'),
            'clientOrders' => $clientOrders->sum('total_price'),
            'clientOrdersBack' => $clientOrdersBack->sum('total_price'),
            'revenue' => $revenue->sum('amount'),
            'expense' => $expense->sum('amount'),
            'expenseWithOutSupplierDeposit' => $expenseWithOutSupplierDeposit->sum('amount'),
            'expenseOfSupplierDeposit' => $expenseOfSupplierDeposit->sum('amount'),
            'revenueWithOutClientDeposit' => $revenueWithOutClientDeposit->sum('amount'),
            'revenueOfClientDeposit' => $revenueOfClientDeposit->sum('amount'),

        ];



        $this->viewData['data'] = $data;
        $this->viewData['pageTitle'] = __('summery Report');
        return $this->view('monthly-report.summery', $this->viewData);


    }


}
