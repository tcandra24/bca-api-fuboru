<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\InvoiceController;
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

Route::group(['prefix' => 'v1', 'middleware' => 'throttle:100,5'], function () {

    Route::post('token', [ApiController::class, 'authenticate']);
    // Route::post('register', [ApiController::class, 'register']);
    
    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('logout', [ApiController::class, 'logout']);
        Route::get('get_user', [ApiController::class, 'get_user']);
        Route::get('pegawai', [PegawaiController::class, 'index']);
        Route::post('invoice', [InvoiceController::class, 'index']);
        // Route::get('products', [ProductController::class, 'index']);
        // Route::get('products/{id}', [ProductController::class, 'show']);
        // Route::post('create', [ProductController::class, 'store']);
        // Route::put('update/{product}',  [ProductController::class, 'update']);
        // Route::delete('delete/{product}',  [ProductController::class, 'destroy']);
    });
});