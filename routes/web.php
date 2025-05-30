<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderPdf;
use App\Http\Controllers\InstitutionOrdersReportController;

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


 
Route::get('/server', OrderPdf::class)->name('order.pdf');

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('institution')->group(function () {
    Route::get('/{institution_hash}/get-orders/{start_date}/{end_date}', InstitutionOrdersReportController::class)->name("institution.orders");
});
