<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FolderController;
use Illuminate\Support\Facades\Route;
use App\Models\Role;

// Users Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Folder Routes
Route::get('/folders', [FolderController::class, 'index'])->middleware('auth:sanctum');
Route::post('/folders', [FolderController::class, 'create'])->middleware('auth:sanctum');
Route::put('/folders/{folder}', [FolderController::class, 'rename'])->middleware('auth:sanctum');
Route::delete('/folders/{folder}', [FolderController::class, 'delete'])->middleware('auth:sanctum');

// Documents Routes
Route::post('/documents', [DocumentController::class, 'create'])->middleware('auth:sanctum');
Route::get('/documents', [DocumentController::class, 'index'])->middleware('auth:sanctum');
Route::get('/documents/{id}', [DocumentController::class, 'show'])->middleware('auth:sanctum');
Route::put('/documents/{id}', [DocumentController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/documents/{id}', [DocumentController::class, 'delete'])->middleware('auth:sanctum');