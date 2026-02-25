<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/tasks', [TaskController::class, 'store']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
Route::patch('/tasks/{id}/complete', [TaskController::class, 'complete']);
Route::patch('/tasks/{id}', [TaskController::class, 'update']);

Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');
