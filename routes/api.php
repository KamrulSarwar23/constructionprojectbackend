<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ProjectController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\Frontend\ServiceController as FrontendServiceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/latest-services', [FrontendServiceController::class, 'LatestService']);
Route::get('/all-services', [FrontendServiceController::class, 'AllService']);

Route::middleware(['auth:sanctum'])->get('/admin/verify-token', function (Request $request) {
    return response()->json(['status' => true]);
});


Route::post('/admin/login', [AuthenticationController::class, 'authenticate']);


Route::group(['middleware' => ['auth:sanctum']], function(){

    Route::get('/admin/dashboard', [DashboardController::class, 'index']);

    Route::get('/admin/logout', [AuthenticationController::class, 'logout']);

    // Service Routes
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    Route::post('/temp-images', [TempImageController::class, 'store']);


     // Project Routes
     Route::get('/projects', [ProjectController::class, 'index']);
     Route::post('/projects', [ProjectController::class, 'store']);

});

