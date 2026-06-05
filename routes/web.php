<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/upload-csv', [DashboardController::class, 'uploadCsv'])->name('upload.csv');
Route::post('/retry/{lead}', [DashboardController::class, 'retryLead'])->name('leads.retry');
Route::post('/reset', [DashboardController::class, 'resetLeads'])->name('leads.reset');
Route::post('/update-copy/{lead}', [DashboardController::class, 'updateCopy'])->name('leads.update_copy');
Route::post('/send/{lead}', [DashboardController::class, 'sendLead'])->name('leads.send');

// Services Orchestration routes
Route::get('/services/status', [DashboardController::class, 'getServicesStatus'])->name('services.status');
Route::post('/services/toggle/{service}', [DashboardController::class, 'toggleService'])->name('services.toggle');
Route::post('/services/update/{service}', [DashboardController::class, 'updateServiceSettings'])->name('services.update');

