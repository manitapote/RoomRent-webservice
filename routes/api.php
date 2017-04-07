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

Route::post('/forgot_password', 'UserController@mailForgotPassword');
Route::get('/forgot_password/{token?}', 'UserController@tokenCheckForgotPassword');

Route::post('/reset_password','UserController@resetPassword');

// Route::post('/reset_password', function(){

// 	return redirect()->action('UserController@reset_redirect'); 
// }); //['middleware' => 'reset_password', 'uses' => 'UserController@resetPassword',
	
//Route::get('/reset_redirect','UserController@reset_redirect')->name('direct');

Route::post('/reset_password', [
	'middleware' => 'reset_password', 
	'uses' => 'UserController@resetPassword',
	]);

//Route::get('/', ['uses' => 'HomeController@index'])->name('home');
