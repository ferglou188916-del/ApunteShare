<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredDocuments = \App\Models\Document::query()
        ->with(['academicArea', 'documentType'])
        ->where('status', 'published')
        ->latest()
        ->limit(6)
        ->get();

    return view('home', compact('featuredDocuments'));
})->name('apunteshare.home');

require __DIR__.'/demo_apunteshare.php';

require __DIR__.'/auth_apunteshare.php';

require __DIR__.'/documents_live.php';
require __DIR__.'/recovery_apunteshare.php';
require __DIR__.'/verification_apunteshare.php';
require __DIR__.'/admin_live.php';
