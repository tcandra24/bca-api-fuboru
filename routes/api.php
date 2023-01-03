<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ApiTesting;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PegawaiController;

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

Route::post('access-token/b2b', [ApiController::class, 'authenticate']);

Route::get('testing', [ApiTesting::class, 'index']);

Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'transfer-va'], function() {
    Route::post('inquiry', [InvoiceController::class, 'inquiry']);
    Route::post('payment', [InvoiceController::class, 'payment']);
});

Route::fallback(function() {
    abort(404, 'API Resource Not Found.');
});