<?php

use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
    Route::get('/admin', function () {
    return view('admin.dashboard');
})->middleware('auth')->name('admin.dashboard');


Route::get('/', function () {
    return view('admin.chats');
})->middleware('auth')->name('chat');


require __DIR__.'/auth.php';
