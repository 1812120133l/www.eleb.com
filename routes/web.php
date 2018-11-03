<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('shop/list','Shop\ShopController@list');

Route::get('shop/menus','Shop\ShopController@menus');
Route::get('shop/index','Shop\ShopController@index');
Route::get('user/sms','User\UserController@sms');
Route::get('note','User\UserController@note');
Route::post('user/regist','User\UserController@regist');
Route::post('user/login','User\UserController@login');
Route::post('user/changePassword','User\UserController@changePassword');
Route::post('user/forgetPassword','User\UserController@forgetPassword');

Route::post('addresse/addAddress','Addresse\AddresseController@addAddress');
Route::get('addresse/addressList','Addresse\AddresseController@addressList');
Route::post('addresse/editAddress','Addresse\AddresseController@editAddress');
Route::get('addresse/address','Addresse\AddresseController@address');

Route::post('cart/addCart','Cart\CartController@addCart');
Route::get('cart/cart','Cart\CartController@cart');

Route::post('order/addorder','Order\OrderController@addorder');
Route::get('order/order','Order\OrderController@order');
Route::get('order/orderList','Order\OrderController@orderList');


Route::get('index','Detail\OrderDetailController@index');
