<?php
/*
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/','HomeSite@index')->name('system.home-site');

// System
Route::group(['prefix'=>'system'],function(){
//    Auth::routes();



    // Start Login

    //StaffLoginController
    Route::get('/login','Auth\StaffLoginController@showLoginForm')->name('staff.login');
    Route::post('/login','Auth\StaffLoginController@attemptLogin')->name('staff.login.submit');
   // Route::get('/', 'AdminController@index')->name('admin.dashboard');
    Route::any('/logout','Auth\StaffLoginController@logout')->name('system.logout');

    // End Login

    Route::get('/','Dashboard@index')->name('system.dashboard');
    Route::get('/development','Dashboard@development')->name('system.development');
    Route::post('/encrypt','Dashboard@encrypt')->name('system.encrypt');


    Route::get('/contest/spin','ContestController@spin')->name('system.contest.spin');
    Route::resource('/contest', 'ContestController',['as'=>'system']);




    Route::get('/logout','Dashboard@logout')->name('system.logout');
    Route::any('/change-password','Dashboard@changePassword')->name('system.change-password');


    // Notifications
    Route::get('/notifications/{ID}', 'NotificationController@url')->name('system.notifications.url'); //
    Route::get('/notifications', 'NotificationController@index')->name('system.notifications.index'); //

    // Setting
    Route::get('/setting', 'SettingController@index')->name('system.setting.index'); //
    Route::patch('/setting', 'SettingController@update')->name('system.setting.update'); //

    // Permission Group
    Route::resource('/permission-group','PermissionGroupController',['as'=>'system']); //


    // Ajax
    Route::get('/ajax','AjaxController@get')->name('system.ajax.get');
    Route::post('/ajax','AjaxController@post')->name('system.ajax.post');


    Route::any('/access-denied','SystemController@access_denied')->name('staff.access.denied');

  Route::get('/staff/visa-report','StaffController@visaReport')->name('system.staff.visa-report');

    Route::resource('/staff', 'StaffController',['as'=>'system']);



    Route::get('/staff/visa-report','StaffController@visaReport')->name('system.staff.visa-report');



    Route::get('/staff/visa-report','StaffController@visaReport')->name('system.staff.visa-report');


// salman routes
    //
    Route::resource('/visa-tracking', 'VisaTrackingController',['as'=>'system']);
    Route::resource('/staff-target', 'StaffTargetController',['as'=>'system']); //
    Route::get('/item/report','ItemsController@report')->name('system.item.report');
    Route::post('/edit-info','StaffController@editInfo')->name('system.staff.edit-info');
    Route::resource('/item/category','ItemCategoriesController',['as'=>'system']);
    Route::resource('/item','ItemsController',['as'=>'system']);
    Route::resource('/supplier-category','SupplierCategoriesController',['as'=>'system']);
    Route::resource('/supplier/order','SupplierOrdersController',['as'=>'system']);
    Route::resource('/supplier','SuppliersController',['as'=>'system']);
    Route::resource('/vacation/type','VacationTypesController',['as'=>'system']);
    Route::resource('/vacation','VacationsController',['as'=>'system']);
    Route::resource('/client/types','ClientTypesController',['as'=>'system']);
    Route::resource('/client','ClientsController',['as'=>'system']);
    Route::resource('/complaint','ComplainController',['as'=>'system']);
    Route::resource('/call','CallController',['as'=>'system']);
    Route::resource('/brand','BrandController',['as'=>'system']);
    Route::resource('/bus/tracking','BusTrackingController',['as'=>'system']);
    Route::resource('/bus','BusController',['as'=>'system']);

    Route::resource('/maintenance','MaintenanceController',['as'=>'system']);
    Route::resource('/causes/expense','ExpenseCausesController',['as'=>'system']);
    Route::resource('/expenses','ExpenseController',['as'=>'system']);
    Route::resource('/causes/profit','ProfitCausesController',['as'=>'system']);
    Route::resource('/profits','ProfitController',['as'=>'system']);
    Route::resource('/deduction','DeductionController',['as'=>'system']);
    Route::resource('/overtime','OvertimeController',['as'=>'system']);
    Route::get('supplier-report','SuppliersController@supplierReport')->name('system.supplier-report');
    Route::get('client-report','ClientsController@clientReport')->name('system.client-report');

    Route::resource('/clothes','ClotheController',['as'=>'system']);
    Route::resource('/certificates','CertificateController',['as'=>'system']);


    Route::post('/staff-terminate/{staff}','StaffController@staffTerminate')->name('system.staff-terminate');


    Route::get('/monthly-report/summery','MonthlyReportController@summery')->name('system.monthly-report.summery');
    Route::get('/monthly-report','MonthlyReportController@monthlyReport')->name('system.attendance.monthly-report');
    Route::post('/monthly-report-calc','MonthlyReportController@monthlyReportCalc',['as'=>'system'])->name('system.attendance.monthly-report-calc');
    Route::post('/monthly-report-save','MonthlyReportController@monthlyReportSave',['as'=>'system'])->name('system.attendance.monthly-report-save');

    Route::get('/monthly/report','MonthlyReportController@monthlyReportIndex',['as'=>'system'])->name('system.monthly-report-index');

    Route::get('/monthly/report-show/{report}','MonthlyReportController@show',['as'=>'system'])->name('system.monthly-report-show');

    Route::delete('/monthly/report/{report}','MonthlyReportController@destroy',['as'=>'system'])->name('system.monthly-report-delete');


    Route::post('/change-bus-availability','BusController@changeAvailability')->name('system.bus.change-availability');
    Route::resource('supplier-order-back','SupplierOrdersBackController',['as'=>'system']);
    Route::resource('client-order-back','ClientOrdersBackController',['as'=>'system']);

    //amr bdreldin route
    Route::resource('/quotations','QuotationsController',['as'=>'system']);
    Route::resource('/project','ProjectController',['as'=>'system']);
    Route::resource('/department','DepartmentController',['as'=>'system']);
    Route::resource('/contract','ContractController',['as'=>'system']);
    Route::resource('/project-cleaners','ProjectCleanersController',['as'=>'system']);
    Route::post('/project/add-project-cleaners','ProjectController@addProjectCleaners',['as'=>'system'])->name('system.projects.add-project-cleaners');
    Route::resource('/client-orders','ClientOrdersController',['as'=>'system']);

    Route::get('/suppliers/creditDetails/{id}','SuppliersController@supplierCreditDetails')->name('system.credit-details');
    Route::get('/clients/creditDetails/{id}','ClientsController@clientCreditDetails')->name('system.client.credit-details');


    Route::get('/suppliers/creditDetails','SuppliersController@creditDetails',['as'=>'system']);
    Route::get('/project/attendance/{project}','ProjectController@attendance',['as'=>'system'])->name('system.projects.attendance');


    Route::post('/project/attendance-store/{project}','ProjectController@attendanceStore',['as'=>'system'])->name('system.projects.attendance-store');


    Route::get('/project/attendance-edit/{attendance}','ProjectController@attendanceEdit',['as'=>'system'])->name('system.projects.attendance-edit');
    Route::any('/project/attendance-update/{attendance}','ProjectController@attendanceUpdate',['as'=>'system'])->name('system.projects.attendance-update');

  //  Route::post('/project/attendance-update/{project}','ProjectController@attendanceUpdate',['as'=>'system'])->name('system.projects.attendance-update');

    Route::get('/project/attendance-update/{project}','ProjectController@attendanceEdit',['as'=>'system']);


//    Route::get('/suppliers/creditDetails/{id}','SuppliersController@creditDetails')->name('system.credit-details');
//    Route::get('/clients/creditDetails/{id}','ClientsController@creditDetails')->name('system.client.credit-details');







    Route::resource('/attendance','AttendanceController',['as'=>'system']);
    Route::get('/attendance-group','AttendanceController@attendanceGroup')->name('system.attendance-group');
    Route::post('/attendance-group','AttendanceController@attendanceGroupStore')->name('system.attendance-group-store');
    Route::post('/attendance-group-ajax','AttendanceController@attendanceGroupAjax')->name('system.attendance-group-ajax');

    Route::get('/attendance-group-index','AttendanceController@groupIndex')->name('system.attendance-group-index');


    Route::get('/attendance-group-edit/{attendance}','AttendanceController@attendanceGroupEdit')->name('system.attendance-group-edit');
    Route::any('/attendance-group-update/{attendance}','AttendanceController@attendanceGroupUpdate')->name('system.attendance-group-update');








});

