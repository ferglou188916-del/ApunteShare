<?php

use App\Http\Controllers\ApunteSharePasswordResetController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/olvide-contrasena', [ApunteSharePasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/olvide-contrasena', [ApunteSharePasswordResetController::class, 'requestLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/restablecer-contrasena/{token}', [ApunteSharePasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/restablecer-contrasena', [ApunteSharePasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
});
