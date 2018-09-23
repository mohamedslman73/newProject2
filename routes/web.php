<?php
//Auth::routes();
//Route::resource('/deposits', 'HomeController',[
//    'as'=>'system',
//    'except'=> [
//        'edit',
//        'update'
//    ]
//]);
//Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix'=>'item'],function(){
    Route::resource('/category','HomeController',['as'=>'system']);

});