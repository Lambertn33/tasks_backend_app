<?php

use App\Http\Controllers\TasksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('tasks')->controller(TasksController::class)->group(function() {
    Route::get('/', 'index');
    Route::prefix('create')->group(function() {
        Route::post('/', 'store');
    });

    Route::prefix('{taskId}')->group(function() {
        Route::get('/', 'show');
        Route::put('/', 'update');
        Route::get('delete', 'destroy');
    });
});
