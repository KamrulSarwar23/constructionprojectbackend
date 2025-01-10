<?php

use App\Http\Controllers\admin\ArticleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ProjectController;
use App\Http\Controllers\admin\TeamMemberController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\admin\TestimonialController;
use App\Http\Controllers\Frontend\ArticleController as FrontendArticleController;
use App\Http\Controllers\Frontend\ProjectController as FrontendProjectController;
use App\Http\Controllers\Frontend\ServiceController as FrontendServiceController;
use App\Http\Controllers\Frontend\TeamMemberController as FrontendTeamMemberController;
use App\Http\Controllers\Frontend\TestimonialController as FrontendTestimonialController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Service Routes
Route::get('/latest-services', [FrontendServiceController::class, 'LatestService']);
Route::get('/all-services', [FrontendServiceController::class, 'AllService']);

// Project Routes
Route::get('/latest-projects', [FrontendProjectController::class, 'LatestService']);
Route::get('/all-projects', [FrontendProjectController::class, 'AllService']);

// Article Routes
Route::get('/latest-articles', [FrontendArticleController::class, 'LatestService']);
Route::get('/all-articles', [FrontendArticleController::class, 'AllService']);

Route::get('/all-testimonials', [FrontendTestimonialController::class, 'AllTestimonials']);

Route::get('/all-team-members', [FrontendTeamMemberController::class, 'AllTeamMembers']);

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
    Route::get('/services/{id}', [ServiceController::class, 'show']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);


    Route::post('/temp-images', [TempImageController::class, 'store']);


     // Project Routes
     Route::get('/projects', [ProjectController::class, 'index']);
     Route::post('/projects', [ProjectController::class, 'store']);
     Route::get('/projects/{id}', [ProjectController::class, 'show']);
     Route::put('/projects/{id}', [ProjectController::class, 'update']);
     Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);


    // Article Routes
     Route::get('/articles', [ArticleController::class, 'index']);
     Route::post('/articles', [ArticleController::class, 'store']);
     Route::get('/articles/{id}', [ArticleController::class, 'show']);
     Route::put('/articles/{id}', [ArticleController::class, 'update']);
     Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);


      // Testimonial Routes
      Route::get('/testimonials', [TestimonialController::class, 'index']);
      Route::post('/testimonials', [TestimonialController::class, 'store']);
      Route::get('/testimonials/{id}', [TestimonialController::class, 'show']);
      Route::put('/testimonials/{id}', [TestimonialController::class, 'update']);
      Route::delete('/testimonials/{id}', [TestimonialController::class, 'destroy']);


        // Team Member Routes
        Route::get('/teams', [TeamMemberController::class, 'index']);
        Route::post('/teams', [TeamMemberController::class, 'store']);
        Route::get('/teams/{id}', [TeamMemberController::class, 'show']);
        Route::put('/teams/{id}', [TeamMemberController::class, 'update']);
        Route::delete('/teams/{id}', [TeamMemberController::class, 'destroy']);

});

