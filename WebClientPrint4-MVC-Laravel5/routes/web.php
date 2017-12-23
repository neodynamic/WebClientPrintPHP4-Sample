<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HomeController@index');
Route::get('home', 'HomeController@index');
Route::get('home/index', 'HomeController@index');
Route::get('home/samples', 'HomeController@samples');
Route::get('home/printersinfo', 'HomeController@printersinfo');
Route::get('DemoPrintFile', 'DemoPrintFileController@index');
Route::get('DemoPrintFileController', 'DemoPrintFileController@printFile');
Route::get('DemoPrintFilePDF', 'DemoPrintFilePDFController@index');
Route::get('DemoPrintFilePDFController', 'DemoPrintFilePDFController@printFile');
Route::get('DemoPrintCommands', 'DemoPrintCommandsController@index');
Route::get('DemoPrintCommandsController', 'DemoPrintCommandsController@printCommands');
Route::any('WebClientPrintController', 'WebClientPrintController@processRequest');