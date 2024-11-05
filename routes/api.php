<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\TempImageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/admin/login', [AuthenticationController::class, 'authenticate']);


Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::get('/admin/dashboard', [DashboardController::class, 'index']);

    Route::get('/admin/logout', [AuthenticationController::class, 'logout']);

    // Service Routes
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::post('/temp-images', [TempImageController::class, 'store']);

});
