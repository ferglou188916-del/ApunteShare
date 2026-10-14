<?php

use Illuminate\Support\Facades\Route;

// Rutas de demostración visual: no sustituyen las rutas reales de la aplicación.
Route::prefix('demo-apunteshare')->name('demo.')->group(function () {
    Route::view('/', 'home')->name('home');
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
    Route::view('/forgot', 'auth.forgot')->name('forgot');
    Route::view('/explore', 'documents.explore')->name('explore');
    Route::view('/upload', 'documents.upload')->name('upload');
    Route::view('/document', 'documents.show')->name('document');
    Route::view('/favorites', 'documents.favorites')->name('favorites');
    Route::view('/profile', 'profile.show')->name('profile');
    Route::view('/report', 'reports.create')->name('report');
    Route::view('/admin', 'admin.dashboard')->name('admin');
    Route::view('/admin/users', 'admin.users')->name('admin.users');
    Route::view('/admin/documents', 'admin.documents')->name('admin.documents');
    Route::view('/admin/reports', 'admin.reports')->name('admin.reports');
    Route::view('/admin/categories', 'admin.categories')->name('admin.categories');
});
