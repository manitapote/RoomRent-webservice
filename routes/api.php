<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


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


Route::get('/activate/{token}','UserController@activate');
Route::get('/forgotpassword/{token?}','UserController@tokenCheckForgotPassword');

Route::post('/login', 'UserController@login');
Route::post('/register', 'UserController@store');
Route::post('/forgotpassword', 'UserController@mailForgotPassword');
Route::post('/formPassword', 'UserController@forgotForm');
Route::post('/password', 'UserController@forgotPassword');

Route::post('/logintest','UserController@logintest');

Route::group(['middleware' => 'auth:api'], function(){
	Route::post('/update','UserController@update');
	Route::post('/resetpassword', 'UserController@resetPassword');
	Route::post('/logout', 'UserController@logout');
	Route::post('/getUserPost', 'PostController@getUserPost');
	Route::post('/getAllPost', 'PostController@getAllPost');
	Route::post('/setPost','PostController@setPost');
	Route::post('/getLocationByDistance','PostController@getLocationByDistance');
	
	Route::get('/getImage/{filename}','ImageController@getImage');
});
