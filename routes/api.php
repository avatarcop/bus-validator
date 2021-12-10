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

Route::post('login', 'UserController@login')->name('login');
Route::post('logout', 'UserController@logout')->name('logout');

// User
Route::post('user/list', 'UserController@userlist')->name('user_list');
Route::post('user/create', 'UserController@usercreate')->name('user_create');
Route::post('user/edit', 'UserController@useredit')->name('user_edit');
Route::post('user/insert', 'UserController@register');
Route::post('user/update', 'UserController@userupdate');
Route::post('user/delete', 'UserController@userdelete')->name('user_delete');

// Accesscontrollist
Route::post('accesscontrol/list', 'AclController@accesscontrollist');
Route::post('accesscontrol/create', 'AclController@accesscontrolcreate');
Route::post('accesscontrol/edit', 'AclController@accesscontroledit');
Route::post('accesscontrol/insert', 'AclController@accesscontrolinsert');
Route::post('accesscontrol/update', 'AclController@accesscontrolupdate');
Route::post('accesscontrol/delete', 'AclController@accesscontroldelete');

// Master PO
Route::post('po/list', 'MasterpoController@polist');
Route::post('po/create', 'MasterpoController@pocreate');
Route::post('po/edit', 'MasterpoController@poedit');
Route::post('po/insert', 'MasterpoController@poinsert');
Route::post('po/update', 'MasterpoController@poupdate');
Route::post('po/delete', 'MasterpoController@podelete');

// Master BUS
Route::post('bus/list', 'MasterbusController@buslist');
Route::post('bus/create', 'MasterbusController@buscreate');
Route::post('bus/edit', 'MasterbusController@busedit');
Route::post('bus/insert', 'MasterbusController@businsert');
Route::post('bus/update', 'MasterbusController@busupdate');
Route::post('bus/delete', 'MasterbusController@busdelete');

// Master TIPEKURSI
Route::post('tipekursi/list', 'TipekursiController@tipekursilist');
Route::post('tipekursi/create', 'TipekursiController@tipekursicreate');
Route::post('tipekursi/edit', 'TipekursiController@tipekursiedit');
Route::post('tipekursi/insert', 'TipekursiController@tipekursiinsert');
Route::post('tipekursi/update', 'TipekursiController@tipekursiupdate');
Route::post('tipekursi/delete', 'TipekursiController@tipekursidelete');

// Master CUSTOMER
Route::post('customer/list', 'CustomerController@customerlist');
Route::post('customer/create', 'CustomerController@customercreate');
Route::post('customer/edit', 'CustomerController@customeredit');
Route::post('customer/insert', 'CustomerController@customerinsert');
Route::post('customer/update', 'CustomerController@customerupdate');
Route::post('customer/delete', 'CustomerController@customerdelete');

// Master TRANSAKSI
Route::post('transaksi/list', 'TransaksiController@transaksilist');
Route::post('transaksi/create', 'TransaksiController@transaksicreate');
Route::post('transaksi/edit', 'TransaksiController@transaksiedit');
Route::post('transaksi/insert', 'TransaksiController@transaksiinsert');
Route::post('transaksi/update', 'TransaksiController@transaksiupdate');
Route::post('transaksi/delete', 'TransaksiController@transaksidelete');
Route::post('transaksi/bayar', 'TransaksiController@transaksibayar');

Route::post('transaksi/notifikasi', 'TransaksiController@transaksinotifikasi');
