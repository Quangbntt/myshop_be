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
    Route::post('login-admin', 'AuthController@loginAdmin');
    Route::get('logout', 'AuthController@logout');

    Route::get('/home', 'HomeController@index')->name('home');
    Route::group(['prefix'=>'user','namespace' =>'User'], function () {
        Route::get('all', 'UserController@index');
        Route::post('create', 'UserController@create');
        Route::post('update', 'UserController@update');
        Route::post('client-update', 'UserController@updateClient');
        Route::post('delete', 'UserController@delete');
        Route::get('detail/{id}', 'UserController@detail');
        Route::get('detail-client/{id}', 'UserController@detailClient');
        Route::get('getUserInfor', 'UserController@getUserInfor');
        Route::get('getUserPhone', 'UserController@getUserPhone');
        Route::get('changeStatus','UserController@changeStatus');
    });
    Route::group(['prefix'=>'role','namespace' =>'Role'], function () {
        Route::get('getAll', 'RoleController@getAll');
    });
    Route::group(['prefix'=>'promotion','namespace' =>'Promotion'], function () {
        Route::get('all', 'PromotionController@index');
        Route::post('create', 'PromotionController@create');
        Route::post('delete', 'PromotionController@delete');
        Route::get('active', 'PromotionController@changeStatus');
    });
    Route::group(['prefix'=>'usersocial','namespace' =>'UserSocial'], function () {
        Route::post('create', 'UserSocialController@create');
    });
    Route::group(['prefix'=>'news','namespace' =>'News'], function () {
        Route::get('all', 'NewsController@listAll');
        Route::get('new', 'NewsController@index');
        Route::get('list', 'NewsController@listAllClient');
        Route::get('list-desc', 'NewsController@listAllDesc');
        Route::post('create', 'NewsController@create');
        Route::post('delete', 'NewsController@delete');
        Route::get('detail', 'NewsController@detail');
    });
    Route::group(['prefix'=>'order','namespace' =>'Order'], function () {
        Route::post('addProduct', 'OrderController@addProduct');
        Route::post('delete', 'OrderController@delete');
        Route::post('update-type', 'OrderController@updateType');
        Route::post('update-type', 'OrderController@updateType');
        Route::post('update-status', 'OrderController@updateStatus');
        Route::post('add-order', 'OrderController@addOrder');
        Route::get('list', 'OrderController@listOrder');
        Route::post('delete-cart', 'OrderController@deleteCart');
        Route::post('delete-cart', 'OrderController@deleteCart');
        Route::get('get-order', 'OrderController@getOrder');
        Route::get('get-admin-order', 'OrderController@getAdminOrder');
        Route::get('report', 'OrderController@report');
        Route::get('report-bar', 'OrderController@reportBar');
        Route::post('export-admin-order', 'OrderController@exportAdminOrder');

    });
    Route::group(['prefix'=>'shipplace','namespace' =>'ShipPlace'], function () {
        Route::get('ship-place', 'ShipPlaceController@getShipPlace');
        Route::get('list-default', 'ShipPlaceController@listDefault');
        Route::post('default', 'ShipPlaceController@default');
        Route::post('delete', 'ShipPlaceController@delete');
        Route::post('create', 'ShipPlaceController@create');
        Route::post('update', 'ShipPlaceController@update');
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
        Route::get('list-name-filter', 'ProductController@listProductNameFilter');
        Route::get('search', 'ProductController@searchProduct');
        Route::get('search-client', 'ProductController@searchProductClient');
        Route::get('detail', 'ProductController@productDetail');
        Route::get('same', 'ProductController@sameProduct');
        Route::get('list-admin', 'ProductController@listProductAdmin');
        Route::post('delete', 'ProductController@delete');
        Route::post('child-create', 'ProductController@createChild');
        Route::post('child-update', 'ProductController@updateChild');
        Route::post('child-delete', 'ProductController@deleteChild');
        Route::get('find-color', 'ProductController@findColor');

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
    Route::group(['prefix'=>'feedback-product','namespace' =>'FeedbackProduct'], function () {
        Route::post('create', 'FeedbackProductController@create');
        Route::get('list', 'FeedbackProductController@list');
    });
    Route::group(['prefix'=>'menu','namespace' =>'Menu'], function () {
        Route::get('list', 'MenuController@getMenu');
    });

