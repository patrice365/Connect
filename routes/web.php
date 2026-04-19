<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\ShareController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard (requires auth + email verification)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// All authenticated routes (protected by 'auth' middleware)
Route::middleware('auth')->group(function () {

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Additional post views for drafts, trash, archive (MUST be before resource route)
    Route::get('/posts/drafts', [PostController::class, 'drafts'])->name('posts.drafts');
    Route::get('/posts/trash', [PostController::class, 'trash'])->name('posts.trash');
    Route::get('/posts/archive', [PostController::class, 'archive'])->name('posts.archive');

    // Posts CRUD (standard resource routes)
    Route::resource('posts', PostController::class);

    // Post restore and force delete (for trash management)
    Route::patch('/posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('/posts/{id}/force-delete', [PostController::class, 'forceDelete'])->name('posts.force-delete');

    // Comments CRUD (nested under posts, shallow)
    Route::resource('posts.comments', CommentController::class)->shallow();

    // Reactions toggle
    Route::post('/reactions/toggle', [ReactionController::class, 'toggle'])->name('reactions.toggle');

    // Social account connections (OAuth)
    Route::prefix('social')->group(function () {
        Route::get('connect/{provider}', [SocialAccountController::class, 'redirect'])->name('social.redirect');
        Route::get('callback/{provider}', [SocialAccountController::class, 'callback'])->name('social.callback');
        Route::delete('disconnect/{provider}', [SocialAccountController::class, 'destroy'])->name('social.disconnect');
    });

    // Share a post to external platforms
    Route::post('/posts/{post}/share', [ShareController::class, 'share'])->name('posts.share');

    // Temporary test email route (remove later)
    Route::get('/test-email', function () {
        Mail::raw('This is a test email from Laravel using Mailtrap!', function ($message) {
            $message->to('test@example.com')->subject('Mailtrap Test Email');
        });
        return 'Test email has been sent! Check your Mailtrap inbox.';
    });
});

// Authentication routes (login, register, password reset, etc.) – DO NOT REMOVE
require __DIR__.'/auth.php';