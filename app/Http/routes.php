<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(["prefix"=>"api"],function(){


    Route::get('/', function () {
        return "Nothing Rocks yet !!";
    });
    Route::group(["prefix"=>"auth"],function (){
    
    
// Authentication Routes...
    Route::post('login', 'Auth\AuthController@login');
    Route::get('logout', 'Auth\AuthController@logout');

// Registration Routes...
    Route::post('register', 'Auth\AuthController@register');

    Route::post('google','Auth\AuthController@redirectToProvider');

    
    });
    Route::get('users', ['middleware'=>'jwt.auth',function(){
        return \App\User::all();
    }]);


});
