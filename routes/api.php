<?php

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])->get('/admin/verify-token', function (Request $request) {
    return response()->json(['status' => true]);
});

Route::post('/admin/login', [AuthenticationController::class, 'authenticate']);

Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
    
    Route::get('/admin/logout', [AuthenticationController::class, 'logout']);
});