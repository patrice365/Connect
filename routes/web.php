<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\ShareController;
use App\Http\Controllers\CaptchaController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

// ======================
// PUBLIC ROUTES
// ======================
Route::view('/', 'welcome')->name('home');

Route::view('/privacy-policy', 'privacy-policy')->name('privacy.policy');
Route::view('/terms-of-service', 'terms-of-service')->name('terms.service');

// ======================
// AUTHENTICATED ROUTES
// ======================

// Dashboard – requires login (email verification removed for demo)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])   // 'verified' removed
    ->name('dashboard');

// Human verification (CAPTCHA after email verification) – optional
Route::controller(CaptchaController::class)
    ->middleware(['auth', 'verified'])
    ->prefix('verify-human')
    ->group(function () {
        Route::get('/', 'show')->name('verification.human');
        Route::post('/', 'verify')->name('verification.human.verify');
    });

// All authenticated routes
Route::middleware('auth')->group(function () {

    // Settings page
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    // Profile routes (restructured)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Posts – custom routes before resource
    Route::get('/posts/drafts', [PostController::class, 'drafts'])->name('posts.drafts');
    Route::get('/posts/trash', [PostController::class, 'trash'])->name('posts.trash');
    Route::get('/posts/archive', [PostController::class, 'archive'])->name('posts.archive');

    // Resource controller
    Route::resource('posts', PostController::class);

    // Additional post actions
    Route::patch('/posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('/posts/{post}/force-delete', [PostController::class, 'forceDelete'])->name('posts.force-delete');

    // Comments (shallow nested under posts)
    Route::resource('posts.comments', CommentController::class)->shallow();

    // Reactions (AJAX)
    Route::post('/reactions/toggle', [ReactionController::class, 'toggle'])->name('reactions.toggle');

    // Social OAuth (YouTube + GitHub)
    Route::prefix('social')->group(function () {
        Route::get('connect/{provider}', [SocialAccountController::class, 'redirect'])->name('social.redirect');
        Route::get('callback/{provider}', [SocialAccountController::class, 'callback'])->name('social.callback');
        Route::delete('disconnect/{provider}', [SocialAccountController::class, 'destroy'])->name('social.disconnect');
    });

    // Share post
    Route::post('/posts/{post}/share', [ShareController::class, 'share'])->name('posts.share');

    // YouTube comments (fetch)
    Route::get('/youtube/comments/{videoId}', [SocialAccountController::class, 'fetchComments'])->name('youtube.comments');
    // YouTube post comment (new)
    Route::post('/youtube/comments/{videoId}', [SocialAccountController::class, 'postComment'])->name('youtube.comment.post');
});

// Temporary fake login (remove after OAuth works)
Route::get('/fake-login', function () {
    $user = App\Models\User::where('email', 'test@example.com')->first();
    if ($user) {
        Auth::login($user);
        return redirect('/dashboard');
    }
    return 'No test user found. Create one first.';
});

// EMERGENCY LOGIN: logs in as the first user (or creates one)
Route::get('/quick-login', function () {
    $user = App\Models\User::first();
    if (!$user) {
        $user = App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@connect.test',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
    Auth::login($user);
    return redirect('/dashboard');
});

// Debug route – check if user is logged in
Route::get('/debug-auth', function () {
    return response()->json([
        'logged_in' => auth()->check(),
        'user' => auth()->user() ? auth()->user()->email : null,
        'session_id' => session()->getId(),
        'cookie_domain' => config('session.domain'),
    ]);
});

// DEBUG ROUTE: Check which social accounts are connected
Route::get('/debug-social', function () {
    $user = auth()->user();
    if (!$user) {
        return 'No user logged in. Use /quick-login first.';
    }
    $youtube = $user->socialAccounts()->where('provider', 'youtube')->first();
    $github = $user->socialAccounts()->where('provider', 'github')->first();
    return [
        'logged_in_as' => $user->email,
        'youtube_connected' => $youtube ? true : false,
        'youtube_provider_id' => $youtube ? $youtube->provider_user_id : null,
        'github_connected' => $github ? true : false,
        'github_provider_id' => $github ? $github->provider_user_id : null,
    ];
});

// ======================
// TEMPORARY DEBUG ROUTES TO CAPTURE GOOGLE ACCOUNT INFO
// ======================

// Get only the Google ID of the account you want to connect
Route::get('/debug-google-id', function () {
    $user = Socialite::driver('google')->user();
    return response()->json([
        'provider_user_id' => $user->getId(),
        'email' => $user->getEmail(),
        'name' => $user->getName(),
    ]);
});

// Get full token information (including access token) for manual database insertion
Route::get('/debug-google-token', function () {
    $socialUser = Socialite::driver('google')->user();
    return response()->json([
        'provider_user_id' => $socialUser->getId(),
        'email' => $socialUser->getEmail(),
        'access_token' => $socialUser->token,
        'refresh_token' => $socialUser->refreshToken,
        'expires_in' => $socialUser->expiresIn,
    ]);
});

// ======================
// AUTHENTICATION ROUTES (Breeze)
// ======================
require __DIR__.'/auth.php';