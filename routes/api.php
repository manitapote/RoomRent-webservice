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

Route::post('/register', 'User\Controller\UserController@store');

Route::get('/activate/{token}','User\Controller\UserController@activate')->name('user.activate');
Route::get('/forgotpassword/{token?}','User\Controller\UserController@tokenCheckForgotPassword')
    ->name('user.forgotpassword');

Route::post('/login', 'User\Controller\UserController@login');
Route::post('/forgotpassword', 'User\Controller\UserController@mailForgotPassword');
Route::post('/formpassword', 'User\Controller\UserController@forgotForm');
Route::post('/forgotpassword/change', 'User\Controller\UserController@forgotPassword');

Route::group(['middleware' => 'auth:api'], function() {
//     Route::post('/update','UserController@update');
    Route::post('/changepassword', 'User\Controller\UserController@changePassword');
    Route::post('/logout', 'User\Controller\UserController@logout');

    Route::get('/post', 'Posts\Controller\PostController@getPost');

    Route::post('/post/create','Posts\Controller\PostController@setPost');
    Route::post('/postbylocation','Posts\Controller\PostController@getPostByLocation');
    
    Route::get('/image/{filename}','Images\Controller\ImageController@getImage');
});

