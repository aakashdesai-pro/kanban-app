<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [TaskController::class, 'index'])->name('index');
Route::get('/list', [TaskController::class, 'list'])->name('list');
Route::post('/store', [TaskController::class, 'store'])->name('store');
Route::post('/update', [TaskController::class, 'update'])->name('update');
Route::post('/update-data', [TaskController::class, 'updateData'])->name('update-data');
Route::post('/file-upload', [TaskController::class, 'fileUpload'])->name('file-upload');
Route::post('/delete', [TaskController::class, 'delete'])->name('delete');
