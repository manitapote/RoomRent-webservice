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


Route::get('/activate/{token}','UserController@activate')->name('user.activate');
Route::get('/forgotpassword/{token?}','UserController@tokenCheckForgotPassword')
	->name('user.forgotpassword');

Route::post('/login', 'UserController@login');
Route::post('/register', 'UserController@store');
Route::post('/forgotpassword', 'UserController@mailForgotPassword');
Route::post('/formpassword', 'UserController@forgotForm');
Route::post('/forgotpassword/change', 'UserController@forgotPassword');

Route::post('/logintest','UserController@logintest');

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('/update','UserController@update');
    Route::post('/changepassword', 'UserController@changePassword');
    Route::post('/logout', 'UserController@logout');
    Route::post('/userpost', 'PostController@getUserPost');
    Route::post('/allpost', 'PostController@getAllPost');
    Route::post('/post/create','PostController@setPost');
    Route::post('/postbylocation','PostController@getPostByLocation');
    
    Route::get('/image/{filename}','ImageController@getImage');
});
