<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TeamController;
use App\Livewire\TeamShow;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('auth.login');
})->middleware('guest');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Route::get('/team/{id}', [TeamController::class, 'show'])
//     ->middleware(['auth', 'verified'])
//     ->name('team.show');
Route::get('/team/{id}', [TeamShow::class])
    ->middleware(['auth', 'verified'])
    ->name('team.show');

Route::get('/project/{project}', [ProjectController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('project.show');

Route::get('/task/{task}/edit', [TaskController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('task.edit');
Route::get('/task/{task}/destroy', [TaskController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('task.destroy');
 Route::post('/task', [TaskController::class, 'store'])
     ->middleware(['auth', 'verified'])
     ->name('task.store');

Route::post('/comment', [CommentController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('comment.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
