<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\CaptchaController;
use Illuminate\Support\Facades\Route;

// ======================
// PUBLIC ROUTES
// ======================

Route::view('/', 'welcome')->name('home');

// Debug route – get the verification URL for the latest user
// Route::get('/verification-url', function () {
//     $user = \App\Models\User::latest()->first();
//     if (!$user) {
//         return 'No user found. Register first.';
//     }
//     return url()->temporarySignedRoute(
//         'verification.verify',
//         now()->addMinutes(60),
//         ['id' => $user->id, 'hash' => sha1($user->email)]
//     );
// });

// // Test mail logging
// Route::get('/test-mail', function () {
//     \Illuminate\Support\Facades\Mail::raw('This is a test email body.', function ($message) {
//         $message->to('test@example.com')->subject('Test Mail Logging');
//     });
//     return 'Mail sent. Check storage/logs/laravel.log';
// });

// Route::get('/force-resend', function () {
//     config(['mail.default' => 'resend']);
//     config(['services.resend.key' => env('RESEND_API_KEY')]);

//     \Illuminate\Support\Facades\Mail::raw('Forced Resend test', function ($message) {
//         $message->to('your-real-email@gmail.com')
//                 ->subject('Forced Resend');
//     });
//     return 'Forced email sent.';
// });

// ======================
// AUTHENTICATED ROUTES
// ======================

// Dashboard – requires login, verified email, and human verification
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Human verification (CAPTCHA after email verification)
Route::controller(CaptchaController::class)
    ->middleware(['auth', 'verified'])
    ->prefix('verify-human')
    ->group(function () {
        Route::get('/', 'show')->name('verification.human');
        Route::post('/', 'verify')->name('verification.human.verify');
    });

// General authenticated routes
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // fixed typo

    // Posts – custom routes before resource
    Route::get('/posts/drafts', [PostController::class, 'drafts'])->name('posts.drafts');
    Route::get('/posts/trash', [PostController::class, 'trash'])->name('posts.trash');
    Route::get('/posts/archive', [PostController::class, 'archive'])->name('posts.archive');
    Route::resource('posts', PostController::class);
    Route::patch('/posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('/posts/{post}/force-delete', [PostController::class, 'forceDelete'])->name('posts.force-delete');

    // Comments
    Route::resource('posts.comments', CommentController::class)->shallow();

    // Reactions (AJAX)
    Route::post('/reactions/toggle', [ReactionController::class, 'toggle'])->name('reactions.toggle');

    // Social OAuth
    Route::prefix('social')->group(function () {
        Route::get('connect/{provider}', [SocialAccountController::class, 'redirect'])->name('social.redirect');
        Route::get('callback/{provider}', [SocialAccountController::class, 'callback'])->name('social.callback');
        Route::delete('disconnect/{provider}', [SocialAccountController::class, 'destroy'])->name('social.disconnect');
    });

    // Share post
    Route::post('/posts/{post}/share', [ShareController::class, 'share'])->name('posts.share');
    });

    // Route::get('/test-resend', function () {
    //     \Illuminate\Support\Facades\Mail::raw('This is a direct test from Connect.', function ($message) {
    //         $message->to('patrice404.husain@gmail.com') // <-- Use your real email here!
    //                 ->subject('Resend Test Email');
    //     });
    //     return 'Test email sent to your inbox!';
    // });

    // Route::get('/force-resend', function () {
    //     config(['mail.default' => 'resend']);
    //     \Illuminate\Support\Facades\Mail::raw('Forced Resend test', function ($message) {
    //         $message->to('patrice404.husain@gmail.com')
    //                 ->subject('Forced Resend');
    //     });
    //     return 'Forced email sent. Check inbox/resend dashboard.';
    // });
// ======================
// AUTHENTICATION ROUTES (Breeze)
// ======================
require __DIR__.'/auth.php';