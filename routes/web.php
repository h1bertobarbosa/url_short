<?php

use Illuminate\Support\Facades\Route;

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
//Route::resource('urls', 'UrlController');

Route::get('urls/{codeOrId}', 'UrlController@show');
Route::get('/{code}', 'UrlController@index');

Route::middleware(['user.check'])->group(function () {
    Route::post('urls', 'UrlController@store');
    Route::put('urls/{codeOrId}', 'UrlController@update');
    Route::delete('urls/{codeOrId}', 'UrlController@destroy');
});



