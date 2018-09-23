<?php

Route::post('/contact-us-post','HomeController@contactUs')->name('website.contact-us');
Route::post('/request-register-merchant','HomeController@RequestRegisterMerchant')->name('website.request-register-merchant');
//Route::get('/{lang?}','HomeController@index')->name('website.index');
//Route::resource('/marketing-message-info','HomeController',['as'=>'system']);
//Route::resource('/marketing-message-user','HomeController',['as'=>'system']); {marketing_message_user}
//Route::resource('/supplier-category','HomeController',['as'=>'system']);
