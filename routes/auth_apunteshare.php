<?php

use App\Http\Controllers\ApunteShareAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [ApunteShareAuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [ApunteShareAuthController::class, 'login'])->middleware('throttle:6,1')->name('apunteshare.login.submit');
    Route::get('/registro', [ApunteShareAuthController::class, 'registerForm'])->name('apunteshare.register');
    Route::post('/registro', [ApunteShareAuthController::class, 'register'])->middleware('throttle:6,1')->name('apunteshare.register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/salir', [ApunteShareAuthController::class, 'logout'])->name('apunteshare.logout');
    Route::get('/mi-perfil', [ApunteShareAuthController::class, 'profile'])->name('apunteshare.profile');
    Route::put('/mi-perfil', [ApunteShareAuthController::class, 'updateProfile'])->name('apunteshare.profile.update');
    Route::get('/panel-admin', function () {
        abort_unless(auth()->user()->role === 'admin' && auth()->user()->is_active, 403);
        return view('admin.dashboard');
    })->name('apunteshare.admin');
});
