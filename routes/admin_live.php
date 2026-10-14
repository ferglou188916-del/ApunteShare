<?php

use App\Http\Controllers\AdminManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('panel-admin')->name('admin.')->group(function () {
    Route::get('/usuarios', [AdminManagementController::class, 'users'])->name('users');
    Route::patch('/usuarios/{user}/estado', [AdminManagementController::class, 'toggleUser'])->name('users.toggle');
    Route::get('/publicaciones', [AdminManagementController::class, 'documents'])->name('documents');
    Route::patch('/publicaciones/{document}/estado', [AdminManagementController::class, 'documentStatus'])->name('documents.status');
    Route::get('/reportes', [AdminManagementController::class, 'reports'])->name('reports');
    Route::patch('/reportes/{report}', [AdminManagementController::class, 'resolveReport'])->name('reports.resolve');
    Route::get('/categorias', [AdminManagementController::class, 'categories'])->name('categories');
    Route::post('/categorias', [AdminManagementController::class, 'addCategory'])->name('categories.add');
    Route::patch('/categorias/{kind}/{id}', [AdminManagementController::class, 'toggleCategory'])->name('categories.toggle');
    Route::get('/comentarios', [AdminManagementController::class, 'comments'])->name('comments');
    Route::patch('/comentarios/{comment}', [AdminManagementController::class, 'toggleComment'])->name('comments.toggle');
    Route::delete('/usuarios/{user}', [AdminManagementController::class, 'deleteUser'])->name('users.delete');
    Route::delete('/publicaciones/{document}', [AdminManagementController::class, 'deleteDocument'])->name('documents.delete');
    Route::delete('/reportes/{report}', [AdminManagementController::class, 'deleteReport'])->name('reports.delete');
    Route::delete('/categorias/{kind}/{id}', [AdminManagementController::class, 'deleteCategory'])->name('categories.delete');
    Route::delete('/comentarios/{comment}', [AdminManagementController::class, 'deleteComment'])->name('comments.delete');
});
