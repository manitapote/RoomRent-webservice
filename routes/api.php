<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

Route::get('/test/{id}', function ($id) {
    return response()->json(['name' => $id]);
});

Route::get('/tees', 'TestController@test');
Route::get('/activate/{token}', 'UserController@activate');
Route::get('/forgot_password/{token?}', 'UserController@tokenCheckForgotPassword');

Route::post('/login', 'UserController@login');
Route::post('/register', 'UserController@store');
Route::post('/update','UserController@update');
Route::post('/forgot_password', 'UserController@mailForgotPassword');
Route::post('/reset_password', 'UserController@resetPassword');

Route::post('/logout', 'UserController@logout');
Route::post('/formPassword', 'UserController@forgotForm');
Route::post('/password', 'UserController@forgotPassword');

