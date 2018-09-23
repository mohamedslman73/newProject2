<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::pattern('id', '\d+');

Route::group(['prefix'=>'merchant','namespace'=>'Merchant'],function(){

    Route::post('/login','MerchantApiController@login');
    Route::post('/logout','MerchantApiController@logout');

    Route::post('/password/forget', 'Auth\ForgotPasswordMerchantApiController@sendResetLinkEmail');
    Route::post('/password/check-code', 'Auth\ResetPasswordMerchantApiController@CheckCode');
    Route::post('/password/reset', 'Auth\ResetPasswordMerchantApiController@reset');


    Route::post('/deny-access','MerchantApiController@no_access')->name('api.merchant.deny.access');


    Route::group(['prefix'=>'user'],function(){
        Route::post('/info', 'StaffInfoApiController@info')->name('api.merchant.user.info');
        Route::post('/update-info', 'StaffInfoApiController@updateInfo')->name('api.merchant.user.update-info');
        Route::post('/change-password', 'StaffInfoApiController@changePassword')->name('api.merchant.user.change-password');
    });


    //Merchant Branches
    Route::group(['prefix'=>'branch'],function(){
        Route::post('/getalldata','BranchApiController@getalldata')->name('api.merchant.branch.index');
        Route::post('/create','BranchApiController@create')->name('api.merchant.branch.create');
        Route::post('/view/{id}','BranchApiController@view')->name('api.merchant.branch.show');
        Route::post('/edit/{id}','BranchApiController@edit')->name('api.merchant.branch.update');
        Route::post('/delete/{id}','BranchApiController@delete')->name('api.merchant.branch.delete');
    });

    //merchant product categories
    Route::group(['prefix'=>'product-category'],function(){
        Route::post('/getalldata','ProductCategoryApiController@getalldata')->name('api.merchant.product-category.index');
        Route::post('/create','ProductCategoryApiController@create')->name('api.merchant.product-category.create');
        Route::post('/view/{id}','ProductCategoryApiController@view')->name('api.merchant.product-category.show');
        Route::post('/edit/{id}','ProductCategoryApiController@edit')->name('api.merchant.product-category.update');
        Route::post('/delete/{id}','ProductCategoryApiController@delete')->name('api.merchant.product-category.delete');
    });

    //merchant products
    Route::group(['prefix'=>'product'],function(){
        Route::post('/getalldata','ProductApiController@getalldata')->name('api.merchant.product.index');
        Route::post('/create','ProductApiController@create')->name('api.merchant.product.create');
        Route::post('/view/{id}','ProductApiController@view')->name('api.merchant.product.show');
        Route::post('/edit/{id}','ProductApiController@edit')->name('api.merchant.product.update');
        Route::post('/delete/{id}','ProductApiController@delete')->name('api.merchant.product.delete');
    });

    //merchant Orders
    Route::group(['prefix'=>'order'],function(){
        Route::post('/getalldata','OrderApiController@getalldata')->name('api.merchant.order.index');
        Route::post('/create','OrderApiController@create')->name('api.merchant.order.create');
        Route::post('/view/{id}','OrderApiController@view')->name('api.merchant.order.show');
        Route::post('/edit/{id}','OrderApiController@edit')->name('api.merchant.order.update');
        Route::post('/delete/{id}','OrderApiController@delete')->name('api.merchant.order.delete');

        //Order items
        Route::post('/view/{order}/qrcode','OrderApiController@qrcode')->name('api.merchant.order.qrcode');
        Route::post('/view/{order}/edit-item/{id}','OrderApiController@edit_order_item')->name('api.merchant.order.item.update');
        Route::post('/view/{order}/delete-item/{id}','OrderApiController@delete_order_item')->name('api.merchant.order.item.delete');
        Route::post('/view/{order}/add-item','OrderApiController@add_order_item')->name('api.merchant.order.item.add');
        Route::post('/view/{order}/add-bulk-items','OrderApiController@add_bulk_items')->name('api.merchant.order.item.add.bulk');

        //Order Transactions
        Route::post('/view/{order}/edit-transaction/{id}','OrderApiController@edit_order_transaction')->name('api.merchant.order.trans.update');
        Route::post('/view/{order}/delete-transaction/{id}','OrderApiController@delete_order_transaction')->name('api.merchant.order.trans.delete');
        Route::post('/view/{order}/add-transaction','OrderApiController@add_order_transaction')->name('api.merchant.order.trans.add');
        Route::post('/view/{order}/add-bulk-transaction','OrderApiController@add_bulk_transactions')->name('api.merchant.order.trans.add.bulk');
    });


    //News
    Route::group(['prefix'=>'news'],function(){
        Route::post('/getalldata','NewsApiController@getalldata')->name('api.merchant.news.home');
        Route::post('/article/{id}','NewsApiController@view')->name('api.merchant.news.show');
        Route::post('/category/{id}','NewsApiController@view_category')->name('api.merchant.news.category');
    });

    //Knowledge
    Route::group(['prefix'=>'knowledge'],function(){
        Route::post('/getalldata','MerchantKnowledgeApiController@getalldata')->name('api.merchant.knowledge.home');
        Route::post('/create','MerchantKnowledgeApiController@create')->name('api.merchant.knowledge.create');
        Route::post('/view/{id}','MerchantKnowledgeApiController@view')->name('api.merchant.knowledge.show');
        Route::post('/search','MerchantKnowledgeApiController@search')->name('api.merchant.knowledge.search');
        Route::post('/edit/{id}','MerchantKnowledgeApiController@edit')->name('api.merchant.knowledge.update');
        Route::post('/delete/{id}','MerchantKnowledgeApiController@delete')->name('api.merchant.knowledge.delete');
    });

    //Knowledge
    Route::group(['prefix'=>'mail'],function(){
        Route::post('/getalldata','MerchantMailApiController@getalldata')->name('api.merchant.mail.inbox');
        Route::post('/view/{id}','MerchantMailApiController@view')->name('api.merchant.mail.show');
    });

    //merchant staff
    Route::group(['prefix'=>'employee'],function(){
        Route::post('/getalldata','MerchantStaffApiController@getalldata')->name('api.merchant.employee.index');
        Route::post('/create','MerchantStaffApiController@create')->name('api.merchant.employee.create');
        Route::post('/view/{id}','MerchantStaffApiController@view')->name('api.merchant.employee.show');
        Route::post('/edit/{id}','MerchantStaffApiController@edit')->name('api.merchant.employee.update');
        Route::post('/delete/{id}','MerchantStaffApiController@delete')->name('api.merchant.employee.delete');
    });

    //merchant staff group
    Route::group(['prefix'=>'staff-group'],function(){
        Route::post('/getalldata','MerchantStaffGroupApiController@getalldata')->name('api.merchant.staff-group.index');
        Route::post('/getallpermissions','MerchantStaffGroupApiController@getallpermissions')->name('api.merchant.staff-group.permissions');
        Route::post('/create','MerchantStaffGroupApiController@create')->name('api.merchant.staff-group.create');
        Route::post('/view/{id}','MerchantStaffGroupApiController@view')->name('api.merchant.staff-group.show');
        Route::post('/edit/{id}','MerchantStaffGroupApiController@edit')->name('api.merchant.staff-group.update');
        Route::post('/delete/{id}','MerchantStaffGroupApiController@delete')->name('api.merchant.staff-group.delete');
    });

    //sub-merchant
    Route::group(['prefix'=>'sub-merchant'],function(){
        Route::post('/getalldata','SubMerchantApiController@getalldata')->name('api.merchant.sub-merchant.index');
        Route::post('/create','SubMerchantApiController@create')->name('api.merchant.sub-merchant.create');
        Route::post('/view/{id}','SubMerchantApiController@view')->name('api.merchant.sub-merchant.show');
        Route::post('/edit/{id}','SubMerchantApiController@edit')->name('api.merchant.sub-merchant.update');
        Route::post('/delete/{id}','SubMerchantApiController@delete')->name('api.merchant.sub-merchant.delete');
    });


    //wallet
    Route::group(['prefix'=>'wallet'],function(){
        /*
        Route::post('/getalldata','WalletApiController@balance')->name('api.merchant.wallet.index');
        Route::post('/transactions','WalletApiController@transactions')->name('api.merchant.wallet.trans');
        */
        Route::post('/Transactions','WalletApiController@walletTransactions')->name('api.merchant.wallet.transactions');
        //api.merchant.wallet.transactions
        Route::post('/OneTransaction','WalletApiController@OneWalletTransactions')->name('api.merchant.wallet.transactions.show');
        //api.merchant.wallet.transactions.show


    });


    //merchant payment
    Route::group(['prefix'=>'payment'],function(){
        Route::post('/inquiry','PaymentApiController@inquiry')->name('api.merchant.payment.service.inquiry');
        Route::post('/payment','PaymentApiController@payment')->name('api.merchant.payment.service.payment');

        Route::post('/getDatabase','PaymentApiController@getDatabase')->name('api.merchant.payment.getDatabase');
        Route::post('/invoice','PaymentApiController@invoice')->name('api.merchant.payment.invoice.index');


        Route::post('/getUserServiceByTransaction','PaymentApiController@getUserServiceByTransaction')->name('api.merchant.payment.getUserServiceByTransaction');
        Route::post('/get-total-amount','PaymentApiController@getTAmount')->name('api.merchant.payment.service.totalamount');
        //Route::post('/pre-transfer','PaymentApiController@pretransfer')->name('api.merchant.payment.transfer');
        Route::post('/transfer','PaymentApiController@transfer')->name('api.merchant.payment.transfer.do');
        Route::post('/oneInvoice','PaymentApiController@GetOneInvoice')->name('api.merchant.payment.invoice.show');
    });


    Route::post('/about-us','StaticPagesController@aboutUs')->name('api.merchant.aboutus');
    Route::post('/checkversion','StaticPagesController@checkversion')->name('api.merchant.checkversion');

    Route::post('/request-register-merchant','MerchantApiController@RequestRegisterMerchant');
    Route::post('/request-equipment','MerchantApiController@requestEquipment');



    Route::any('/latest-app.apk','MerchantApiController@DownloadApk')->name('api.merchant.latest-apk');


    Route::post('/content-data','ContestController@getAllData');
    Route::post('/contest-consumed','ContestController@contestConsumed');



});

