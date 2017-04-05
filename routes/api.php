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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group(['middleware' => ['auth']], function(){
Route::get('/test/{id}', function($id){
	return response()->json(['name' => $id]);
});

Route::post('/register','UserController@store');

Route::get('/tees','TestController@test');

Route::post('/login', 'UserController@login');
Route::get('/activate/{token}','UserController@activate');

Route::post('/update', 'UserController@update');

Route::post('/password_reset', 'UserController@forgotPasswordMail');
//Route::post


//Route::get('/', ['uses' => 'HomeController@index'])->name('home');
