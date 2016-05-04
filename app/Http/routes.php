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

Route::get('/', function () {
    return "Nothing Rocks yet !!";
});

// Authentication Routes...
Route::post('login', 'Auth\AuthController@login');
Route::get('logout', 'Auth\AuthController@logout');

// Registration Routes...
Route::post('register', 'Auth\AuthController@register');
Route::get('/home', 'HomeController@index');

Route::get('/auth/google','Auth\AuthController@redirectToProvider');
Route::get('/auth/callback','Auth\AuthController@handleProviderCallback');


