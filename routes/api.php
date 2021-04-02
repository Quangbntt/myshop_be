<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    Route::post('login', 'AuthController@login');
    Route::get('logout', 'AuthController@logout');

    Route::get('/home', 'HomeController@index')->name('home');
    Route::group(['prefix'=>'user','namespace' =>'User'], function () {
        Route::get('all', 'UserController@index');
        Route::post('create', 'UserController@create');
        Route::post('update', 'UserController@update');
        Route::post('delete', 'UserController@delete');
        Route::get('getUserInfor', 'UserController@getUserInfor');
        Route::get('getUserPhone', 'UserController@getUserPhone');
        Route::get('changeStatus','UserController@changeStatus');
    });
    Route::group(['prefix'=>'role','namespace' =>'Role'], function () {
        Route::get('getAll', 'RoleController@getAll');
    });
    Route::group(['prefix'=>'product','namespace' =>'Product'], function () {
        Route::get('all', 'ProductController@index');
        Route::post('create', 'ProductController@create');
        Route::post('update', 'ProductController@update');
        Route::get('slide', 'ProductController@slide');
        Route::get('feature', 'ProductController@featureProduct');
        Route::get('recent', 'ProductController@recentProduct');
        Route::get('hot', 'ProductController@hotProduct');
        Route::get('list-name', 'ProductController@listProductName');
        Route::get('search', 'ProductController@searchProduct');
        Route::get('detail', 'ProductController@productDetail');
        Route::get('same', 'ProductController@sameProduct');
        Route::get('list-admin', 'ProductController@listProductAdmin');
        Route::post('delete', 'ProductController@delete');
        Route::post('child-create', 'ProductController@createChild');
        Route::post('child-update', 'ProductController@updateChild');
        Route::post('child-delete', 'ProductController@deleteChild');

    });
    Route::group(['prefix'=>'branch','namespace' =>'Branch'], function () {
        Route::get('all', 'BranchController@index');
        Route::get('list', 'BranchController@list');
        Route::get('list-name', 'BranchController@listBranchName');
        Route::post('create', 'BranchController@create');
        Route::post('update', 'BranchController@update');
        Route::post('delete', 'BranchController@delete');
    });
    Route::group(['prefix'=>'category','namespace' =>'Category'], function () {
        Route::get('all', 'CategoryController@index');
        Route::post('create', 'CategoryController@create');
        Route::post('update', 'CategoryController@update');
        Route::get('changeStatus','CategoryController@changeStatus');
        Route::get('getCategoryInfor', 'CategoryController@getCategoryInfor');
        Route::get('info', 'CategoryController@info');

    });
    Route::group(['prefix'=>'feedback','namespace' =>'Feedback'], function () {
        Route::get('all', 'FeedbackController@index');
        Route::post('create', 'FeedbackController@create');
    });
    Route::group(['prefix'=>'menu','namespace' =>'Menu'], function () {
        Route::get('list', 'MenuController@getMenu');
    });

