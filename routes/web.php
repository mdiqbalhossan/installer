<?php
use Illuminate\Support\Facades\Route;
use Softmax\Installer\Http\Controllers\InstallerController;

Route::prefix('softmax-installer')->name('softmax.installer.')->group(function() {
    // Main installer page
    Route::get('/', [InstallerController::class, 'start'])->name('start');
    
    // API endpoints for installer steps
    Route::get('/system-info', [InstallerController::class, 'getSystemInfo'])->name('system-info');
    Route::post('/validate-license', [InstallerController::class, 'validateLicense'])->name('validate-license');
    Route::post('/test-database', [InstallerController::class, 'testDatabase'])->name('test-database');
    Route::post('/install', [InstallerController::class, 'install'])->name('install');
    
    // Development helper (non-production only)
    Route::post('/reset', [InstallerController::class, 'reset'])->name('reset');
});
