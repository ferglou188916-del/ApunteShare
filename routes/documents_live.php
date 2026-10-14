<?php

use App\Http\Controllers\AcademicDocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/materiales', [AcademicDocumentController::class, 'index'])->name('documents.index');
    Route::get('/compartir-material', [AcademicDocumentController::class, 'create'])->name('documents.create');
    Route::post('/compartir-material', [AcademicDocumentController::class, 'store'])->middleware('throttle:15,1')->name('documents.store');
    Route::get('/mis-favoritos', [AcademicDocumentController::class, 'favorites'])->name('documents.favorites');
    Route::get('/materiales/{document}', [AcademicDocumentController::class, 'show'])->name('documents.show');
    Route::get('/materiales/{document}/descargar', [AcademicDocumentController::class, 'file'])->name('documents.download');
    Route::get('/materiales/{document}/vista-previa', [AcademicDocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/materiales/{document}/editar', [AcademicDocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/materiales/{document}', [AcademicDocumentController::class, 'update'])->name('documents.update');
    Route::delete('/materiales/{document}', [AcademicDocumentController::class, 'destroy'])->name('documents.destroy');
    Route::post('/materiales/{document}/favorito', [AcademicDocumentController::class, 'toggleFavorite'])->middleware('throttle:30,1')->name('documents.favorite');
    Route::post('/materiales/{document}/reportar', [AcademicDocumentController::class, 'report'])->middleware('throttle:5,1')->name('documents.report');
    Route::post('/materiales/{document}/comentar', [AcademicDocumentController::class, 'comment'])->middleware('throttle:10,1')->name('documents.comment');
    Route::post('/materiales/{document}/calificar', [AcademicDocumentController::class, 'rate'])->middleware('throttle:15,1')->name('documents.rate');
});
