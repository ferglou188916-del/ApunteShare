<?php

use App\Http\Controllers\ApunteShareEmailVerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/verificar-correo', [ApunteShareEmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/verificar-correo/{id}/{hash}', [ApunteShareEmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/verificar-correo/reenviar', [ApunteShareEmailVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
});
