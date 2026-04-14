<?php

use Illuminate\Support\Facades\Route;

// The Home Page
Route::get('/', function () {
    return view('welcome');
});

// The Register Page (Where "Get Started" goes)
Route::get('/register', function () {
    return view('register');
});

// The Login Page
Route::get('/login', function () {
    return view('login');
});

// The Dashboard Page
Route::get('/dashboard', function () {
    return view('Layouts.app');
});

Route::get('/posts/create', function () {
    return view('posts.create'); 
})->name('posts.create');