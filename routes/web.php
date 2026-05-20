<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

// 1. Landing & Auth Routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', function () {
    return view('register');
});

Route::get('/login', function () {
    return view('login');
});

// 2. Core App Layout & Feed
Route::get('/dashboard', function () {
    return view('Layouts.app');
});

Route::get('/feed', [PostController::class, 'index'])->name('feed');

// 3. Post Management
Route::get('/posts/create', function () {
    return view('posts.create'); 
})->name('posts.create');

Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

// 4. New App Views (Settings, Profile, Archive)
Route::get('/settings', function () {
    return view('settings');
});

Route::get('/profile', function () {
    return view('profile');
});

Route::get('/archive', function () {
    return view('archive');
});