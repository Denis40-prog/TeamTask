<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Teams
    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);

    // Projects
    Route::get('/teams/{team}/projects', [ProjectController::class, 'index']);
    Route::post('/teams/{team}/projects', [ProjectController::class, 'store']);

    // Tasks
    Route::get('/projects/{project}/tasks', [TaskController::class, 'index']);

    // Comments
    Route::get('/projects/{project}/comments', [CommentController::class, 'index']);
});
