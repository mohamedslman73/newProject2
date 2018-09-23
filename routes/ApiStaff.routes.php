<?php

// staff
//Route::group(['middleware'=>'auth:apiStaff'],function (){
Route::group(['prefix'=>'staff','namespace'=>'Staff'],function(){
    // Auth::routes();
//
    Route::post('/login','StaffApiController@login')->name('api.staff.login');
    Route::post('/logout','StaffApiController@logout')->name('api.staff.logout');
    Route::get('/dashboard','StaffApiController@dashboard')->name('api.staff.dashboard');
    //ClientApiController
    Route::group(['prefix'=>'client'],function(){
        //Clients
        Route::get('/','ClientApiController@clients')->name('api.staff.clients');
        Route::get('/view','ClientApiController@oneClient')->name('api.staff.one-client');
        Route::post('/delete','ClientApiController@deleletClient')->name('api.staff.client-delete');
        Route::get('/clientReport','ClientApiController@clientReport')->name('api.staff.client-report');
        //  Route::get('/clientCreditDetails','ClientApiController@clientCreditDetails')->name('api.staff.client-report');
        //Client Orders
        Route::get('/orders','ClientOrdersApiController@clientOrders')->name('api.staff.client-orders');
        Route::get('/orders/oneOrder','ClientOrdersApiController@oneClientOrder')->name('api.staff.one-client-order');
        Route::post('/orders/delete','ClientOrdersApiController@deleletClientOrder')->name('api.staff.client-order-delete');
        Route::post('/createClientOrder','ClientOrdersApiController@createClientOrder')->name('api.staff.create-client-order');
        Route::post('/updateClientOrder','ClientOrdersApiController@updateClientOrder')->name('api.staff.client-order-update');
        // Client Order Back
        Route::get('ordersBack','ClientOrdersBackApiController@clientOrdersBack')->name('api.staff.client-order-back');
        Route::get('oneClientOrderBack','ClientOrdersBackApiController@oneClientOrderBack')->name('api.staff.one-client-order-back');
        Route::post('/deleteClientOrderBack','ClientOrdersBackApiController@deleteClientOrderBack')->name('api.staff.delete-client-order-back');
        Route::post('/createClientOrderBack','ClientOrdersBackApiController@createClientOrderBack')->name('api.staff.create-client-order-back');
    });

    Route::group(['prefix'=>'supplier'],function(){
        //Suppliers
        Route::get('/','SupplierApiController@suppliers')->name('api.staff.suppliers');
        Route::get('/oneSupplier','SupplierApiController@oneSupplier')->name('api.staff.one-supplier');
        Route::post('/delete','SupplierApiController@deleteSupplier')->name('api.staff.supplier-delete');
        Route::post('/createSupplier','SupplierApiController@createSupplier')->name('api.staff.supplier-create');
        Route::post('/updateSupplier','SupplierApiController@updateSupplier')->name('api.staff.supplier-update');
        Route::get('/supplierReport','SupplierApiController@supplierReport')->name('api.staff.supplier-report');
        //Supplier Orders
        Route::get('/orders','SupplierOrdersApiController@supplierOrders')->name('api.staff.supplier-orders');
        Route::get('/orders/oneOrder','SupplierOrdersApiController@oneSupplierOrder')->name('api.staff.one-supplier-order');
        Route::post('/orders/delete','SupplierOrdersApiController@deleletSuppliertOrder')->name('api.staff.supplier-order-delete');
        Route::post('/createSupplierOrder','SupplierOrdersApiController@createSupplierOrder')->name('api.staff.create-supplier-order');
        Route::post('/updateSupplierOrder','SupplierOrdersApiController@updateSupplierOrder')->name('api.staff.supplier-order-update');
        //Suppliers Order Back
        Route::get('/ordersBack','SupplierOrdersBackApiController@supplierOrderBack')->name('api.staff.supplier-order-back');
        Route::get('/oneSupplierOrderBack','SupplierOrdersBackApiController@oneSupplierOrderBack')->name('api.staff.one-supplier-order-back');
        Route::post('/deleteSupplierOrderBack','SupplierOrdersBackApiController@deleletSupplierOrderBack')->name('api.staff.delete-supplier-order-back');
        Route::post('/createSupplierOrderBack','SupplierOrdersBackApiController@createSupplierOrderBack')->name('api.staff.create-supplier-order-back');
    });

    Route::group(['prefix'=>'bus'],function (){
        //Bus
        Route::get('/','BusApiController@allBus')->name('api.staff.bus');
        Route::get('/oneBus','BusApiController@oneBus')->name('api.staff.one-bus');
        Route::post('/deleteBus','BusApiController@deleteBus')->name('api.staff.delete-bus');
        Route::post('/createBus','BusApiController@createBus')->name('api.staff.create-bus');
        Route::post('/updateBus','BusApiController@updateBus')->name('api.staff.update-bus');
        //Bus Tracking

        Route::get('/allBusTracking','BusTrackingApiController@allBusTracking')->name('api.staff.bus-tracking');
        Route::get('/oneBusTracking','BusTrackingApiController@oneBusTracking')->name('api.staff.one-bus-tracking');
        Route::post('/createBusTracking','BusTrackingApiController@createBusTracking')->name('api.staff.create-bus-tracking');
        Route::post('/updateBusTracking','BusTrackingApiController@updateBusTracking')->name('api.staff.update-bus-tracking');
        Route::post('/deleteBusTracking','BusTrackingApiController@deleteBusTracking')->name('api.staff.delete-bus-tracking');
        //Maintenance
        Route::get('/allMaintenance','MaintenanceApiController@allMaintenance')->name('api.staff.bus-maintenance');
        Route::get('/oneMaintenance','MaintenanceApiController@oneMaintenance')->name('api.staff.one-bus-maintenance');
        Route::post('/createMaintenance','MaintenanceApiController@createMaintenance')->name('api.staff.create-bus-maintenance');
        Route::post('/deleteMaintenance','MaintenanceApiController@deleteMaintenance')->name('api.staff.delete-bus-maintenance');
        Route::post('/updateMaintenance','MaintenanceApiController@updateMaintenance')->name('api.staff.update-bus-maintenance');
       // Route::post('/createMaintenance','MaintenanceApiController@createMaintenance')->name('api.staff.update-bus-maintenance');
    });
    Route::group(['prefix'=>'quotation'],function (){
        Route::get('/','QuotationApiController@allQuotation')->name('api.staff.quotation');
        Route::get('/oneQuotation','QuotationApiController@oneQuotation')->name('api.staff.one-quotation');
        Route::post('/createQuotation','QuotationApiController@createQuotation')->name('api.staff.create-quotation');
        Route::post('/deleteQuotation','QuotationApiController@deleteQuotation')->name('api.staff.delete-quotation');
        Route::post('/updateQuotation','QuotationApiController@updateQuotation')->name('api.staff.update-quotation');
    });
    Route::group(['prefix'=>'item'],function (){
        // Item Categories
        Route::get('/categories','ItemCategoriesApiController@itemCategories')->name('api.staff.item-categories');
        Route::get('/oneItemCategories','ItemCategoriesApiController@oneItemCategories')->name('api.staff.one-item-category');
        Route::post('/createItemCategories','ItemCategoriesApiController@createItemCategories')->name('api.staff.create-item-category');
        Route::post('/deleteItemCategories','ItemCategoriesApiController@deleteItemCategories')->name('api.staff.delete-item-category');
        Route::post('/updateItemCategories','ItemCategoriesApiController@updateItemCategories')->name('api.staff.update-item-category');
        // Items
        Route::get('/','ItemsApiController@items')->name('api.staff.items');
        Route::get('/oneItem','ItemsApiController@oneItem')->name('api.staff.one-item');
        Route::post('/createItem','ItemsApiController@createItem')->name('api.staff.create-item');
        Route::post('/updateItem','ItemsApiController@updateItem')->name('api.staff.update-item');
        Route::post('/deleteItem','ItemsApiController@deleteItem')->name('api.staff.delete-item');
    });
    Route::group(['prefix'=>'revenue'],function (){
        // Revenue Causes
        Route::get('/causes','RevenueCausesApiController@revenueCauses')->name('api.staff.revenue-causes');
        Route::get('/oneRevenueCauses','RevenueCausesApiController@oneRevenueCauses')->name('api.staff.one-revenue-cause');
        Route::post('/createRevenueCauses','RevenueCausesApiController@createRevenueCauses')->name('api.staff.create-revenue-causes');
        Route::post('/updateRevenueCauses','RevenueCausesApiController@updateRevenueCauses')->name('api.staff.update-revenue-causes');
        Route::post('/deleteRevenueCauses','RevenueCausesApiController@deleteRevenueCauses')->name('api.staff.delete-revenue-causes');

        // Revenues
        Route::get('/','RevenueApiController@revenue')->name('api.staff.revenue');
        Route::get('/oneRevenue','RevenueApiController@oneRevenue')->name('api.staff.one-revenue');
        Route::post('/createRevenue','RevenueApiController@createRevenue')->name('api.staff.create-revenue');
        Route::post('/updateRevenue','RevenueApiController@updateRevenue')->name('api.staff.update-revenue');
        Route::post('/deleteRevenue','RevenueApiController@deleteRevenue')->name('api.staff.delete-revenue');
    });
    Route::group(['prefix'=>'expense'],function (){
        // expense Causes
        Route::get('/causes','ExpenseCausesApiController@expenseCauses')->name('api.staff.expense-causes');
        Route::get('/oneExpenseCauses','ExpenseCausesApiController@oneExpenseCauses')->name('api.staff.one-expense-causes');
        Route::post('/createExpenseCauses','ExpenseCausesApiController@createExpenseCauses')->name('api.staff.create-expense-causes');
        Route::post('/updateExpenseCauses','ExpenseCausesApiController@updateExpenseCauses')->name('api.staff.update-expense-causes');
        Route::post('/deleteExpenseCauses','ExpenseCausesApiController@deleteExpenseCauses')->name('api.staff.delete-expense-causes');

        // expense
        Route::get('/','ExpenseApiController@expense')->name('api.staff.expense');
        Route::get('/oneExpense','ExpenseApiController@oneExpense')->name('api.staff.expense');
        Route::post('/createExpense','ExpenseApiController@createExpense')->name('api.staff.create-expense');
        Route::post('/updateExpense','ExpenseApiController@updateExpense')->name('api.staff.update-expense');
        Route::post('/deleteExpense','ExpenseApiController@deleteExpense')->name('api.staff.delete-expense');

    });
    Route::group(['prefix'=>'project'],function (){
        //Projects
        Route::get('/','ProjectApiController@projects')->name('api.staff.project');
        Route::get('/oneProject','ProjectApiController@oneProject')->name('api.staff.one-project');
        Route::post('/createProject','ProjectApiController@createProject')->name('api.staff.create-project');
        Route::post('/updateProject','ProjectApiController@updateProject')->name('api.staff.update-project');
        Route::post('/deleteProject','ProjectApiController@deleteProject')->name('api.staff.delete-project');
        Route::post('/addProjectCleaners','ProjectApiController@addProjectCleaners')->name('api.staff.addProjectCleaners');
        //departments
        Route::get('/department','DepartmentApiController@department')->name('api.staff.department');
        Route::get('/oneDepartment','DepartmentApiController@oneDepartment')->name('api.staff.one-department');
        Route::post('/deleteDepartment','DepartmentApiController@deleteDepartment')->name('api.staff.delete-department');
        Route::post('/createDepartment','DepartmentApiController@createDepartment')->name('api.staff.create-department');
        Route::post('/updateDepartment','DepartmentApiController@updateDepartment')->name('api.staff.update-department');
        //contract
        Route::get('/contracts','ContractApiController@contracts')->name('api.staff.contract');
        Route::get('/oneContract','ContractApiController@oneContract')->name('api.staff.one-contract');
        Route::post('/createContract','ContractApiController@createContract')->name('api.staff.create-contract');
        Route::post('/updateContract','ContractApiController@updateContract')->name('api.staff.update-contract');
        Route::post('/deleteContract','ContractApiController@deleteContract')->name('api.staff.delete-contract');

    });
    Route::group(['prefix'=>'complaint'],function (){
        Route::get('/','ComplaintsApiController@complaints')->name('api.staff.complaint');
        Route::get('/oneComplaint','ComplaintsApiController@oneComplaint')->name('api.staff.one-complaint');
        Route::post('/createComplaint','ComplaintsApiController@createComplaint')->name('api.staff.create-complaint');
        Route::post('/updateComplaint','ComplaintsApiController@updateComplaint')->name('api.staff.update-complaint');
        Route::post('/deleteComplaint','ComplaintsApiController@deleteComplaint')->name('api.staff.delete-complaint');
    });
    // This api for creating client Order.
    Route::get('/clientsProjectsItems','ClientOrdersApiController@clientsProjectsItems')->name('api.staff.clientsProjectsItems');
    //This api for creating client order back.
    Route::get('/clientsAndClientIdsItems','ClientOrdersBackApiController@clientsAndClientIdsItems')->name('api.staff.clientsAndClientIdsItems');
    // This api for creating supplier Order.
    Route::get('/supplierAndItems','SupplierOrdersApiController@supplierAndItems')->name('api.staff.supplierAndItems');
    // This api for creating supplier Order Back.
    Route::get('/suppliersAndOrderIdItems','SupplierOrdersBackApiController@suppliersAndOrderIdItems')->name('api.staff.suppliersAndOrderIdItems');
    // This api for creating Bus Tracking.
    Route::get('/busProjectsAndDrivers','BusTrackingApiController@busProjectsAndDrivers')->name('api.staff.busProjectsAndDrivers');
    //This api for creating Bus Maintenance.
    Route::get('/buses','MaintenanceApiController@bus')->name('api.staff.bus-for-maintenance');
    //This Api for Create Quotation.
    Route::get('/clientsAndDepartmentsAndItems','QuotationApiController@clientsAndDepartmentsAndItems')->name('api.staff.quotation.clientsAndDepartmentsAndItems');
    //This Api for creating item.
    Route::get('/itemCategories','ItemsApiController@itemCategories')->name('api.staff.itemCategories');
    //This Api for creating Revenue.
    Route::get('/revenueCauses','RevenueApiController@revenueCauses');
    //This Api for adding Cleaners to project.
    Route::get('DepartmentsAndCleaners','ProjectApiController@DepartmentsAndCleaners')->name('api.staff.DepartmentsAndCleaners');
    //This api for creating suppliers.
    Route::get('/supplierCategories','SupplierApiController@supplierCategories')->name('api.staff.supplierCategories');
    // This Api for Creating Contract projects.
    Route::get('/projects','ContractApiController@projects')->name('api.staff.projects');
    // This Api for Creating Contract Complaints.
    Route::get('/staffClientsAndProjects','ComplaintsApiController@staffClientsAndProjects')->name('api.staff.staffClientsAndProjects');
});
//});