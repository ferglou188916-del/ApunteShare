<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('apunteshare.home');

require __DIR__.'/demo_apunteshare.php';

require __DIR__.'/auth_apunteshare.php';

require __DIR__.'/documents_live.php';
require __DIR__.'/recovery_apunteshare.php';
require __DIR__.'/verification_apunteshare.php';
require __DIR__.'/admin_live.php';
