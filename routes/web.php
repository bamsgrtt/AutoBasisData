<?php

use App\Http\Controllers\DatabaseConfigController;
use App\Http\Controllers\PracticalController;
use App\Http\Controllers\SQLExecutorController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', [PracticalController::class, 'dashboard'])->name('home');

// Praktikum
Route::get('/praktikum', [PracticalController::class, 'index'])->name('practicals.index');
Route::post('/praktikum', [PracticalController::class, 'store'])->name('practicals.store');
Route::get('/praktikum/{practical}', [PracticalController::class, 'show'])->name('practicals.show');

// API endpoints (JSON, dipanggil dari workspace via AJAX)
Route::post('/api/database/test', [DatabaseConfigController::class, 'testConnection'])->name('database.config.test');
Route::post('/api/database/save', [DatabaseConfigController::class, 'storeSelectedDatabase'])->name('database.save');
Route::post('/api/sql/execute', [SQLExecutorController::class, 'executeSQL'])->name('sql.execute');
Route::post('/api/execution/save', [PracticalController::class, 'saveExecution'])->name('execution.save');

// Pengaturan
Route::get('/pengaturan', [DatabaseConfigController::class, 'showConfigForm'])->name('settings');
