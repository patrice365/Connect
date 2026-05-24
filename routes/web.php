<?php

use App\Http\Controllers\DashboardController;
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

// Temporary debug route – shows Facebook OAuth config
Route::get('/debug-config', function () {
    dd(config('services.facebook'));
});

// ======================
// AUTHENTICATED ROUTES
// ======================

// Dashboard – requires login, verified email
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Human verification (CAPTCHA after email verification) – optional, keep if needed
Route::controller(CaptchaController::class)
    ->middleware(['auth', 'verified'])
    ->prefix('verify-human')
    ->group(function () {
        Route::get('/', 'show')->name('verification.human');
        Route::post('/', 'verify')->name('verification.human.verify');
    });

// General authenticated routes
Route::middleware('auth')->group(function () {

    // Settings page (authenticated)
    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Posts – custom routes before resource
    Route::get('/posts/drafts', [PostController::class, 'drafts'])->name('posts.drafts');
    Route::get('/posts/trash', [PostController::class, 'trash'])->name('posts.trash');
    Route::get('/posts/archive', [PostController::class, 'archive'])->name('posts.archive');
    // Video post creation (alias for posts.create)
    Route::get('/posts/video/create', [PostController::class, 'create'])->name('posts.video.create');
    // Video post submission alias (points to same store action)
    Route::post('/posts/video', [PostController::class, 'store'])->name('posts.video.store');
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

    Route::view('/privacy-policy', 'privacy-policy')->name('privacy.policy');

    Route::get('/youtube/comments/{videoId}', function ($videoId) {
        $user = auth()->user();
        $account = $user->socialAccounts()->where('provider', 'youtube')->first();
        if (!$account) return response()->json(['error' => 'Not connected'], 403);

        try {
            $comments = Http::get('https://www.googleapis.com/youtube/v3/commentThreads', [
                'part'         => 'snippet',
                'videoId'      => $videoId,
                'maxResults'   => 20,
                'order'        => 'time',
                'access_token' => $account->access_token,
            ])->json();

            $formatted = [];
            foreach ($comments['items'] ?? [] as $item) {
                $snippet = $item['snippet']['topLevelComment']['snippet'];
                $formatted[] = [
                    'authorDisplayName'     => $snippet['authorDisplayName'],
                    'authorProfileImageUrl' => $snippet['authorProfileImageUrl'],
                    'textDisplay'           => $snippet['textDisplay'],
                    'publishedAt'           => \Carbon\Carbon::parse($snippet['publishedAt'])->diffForHumans(),
                ];
            }
            return response()->json(['comments' => $formatted]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to load comments.'], 500);
        }
    })->middleware('auth')->name('youtube.comments');

    Route::view('/terms-of-service', 'terms-of-service')->name('terms.service');
// ======================
// AUTHENTICATION ROUTES (Breeze)
// ======================
require __DIR__.'/auth.php';