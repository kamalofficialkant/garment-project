<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RackOrderController;
use App\Http\Controllers\InboundController;

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

Route::resource('rack-order', RackOrderController::class);
Route::resource('inbound', InboundController::class);