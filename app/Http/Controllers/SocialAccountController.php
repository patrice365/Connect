<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialAccountController extends Controller
{
    public function redirect($provider)
    {
        if ($provider === 'github') {
            return Socialite::driver('github')
                ->scopes(['repo', 'gist', 'user:email'])
                ->redirect();
        }

        if ($provider === 'youtube') {
            return Socialite::driver('google')
                ->scopes([
                    'openid',
                    'https://www.googleapis.com/auth/youtube.force-ssl',
                    'https://www.googleapis.com/auth/userinfo.email',
                    'https://www.googleapis.com/auth/userinfo.profile',
                ])
                ->with([
                    'access_type' => 'offline',
                    'prompt' => 'consent',
                ])
                ->redirect();
        }

        return redirect('/login')->with('error', 'Invalid provider');
    }

    public function callback($provider)
    {
        try {
            // Get social user from provider
            if ($provider === 'github') {
                $socialUser = Socialite::driver('github')->user();
                $providerId = $socialUser->getId();
                $email = $socialUser->getEmail();
                $name = $socialUser->getName() ?? $socialUser->getNickname();
                $avatar = $socialUser->getAvatar();

                if (!$email) {
                    $emails = Socialite::driver('github')->user()->getEmails();
                    $primaryEmail = collect($emails)->firstWhere('primary', true);
                    $email = $primaryEmail['email'] ?? null;
                }
            } else { // youtube (google driver)
                $socialUser = Socialite::driver('google')->user();
                $providerId = $socialUser->getId();
                $email = $socialUser->getEmail();
                $name = $socialUser->getName();
                $avatar = $socialUser->getAvatar();
            }

            if (empty($providerId)) {
                Log::error('Provider ID missing', ['provider' => $provider]);
                return redirect('/login')->with('error', 'Could not retrieve user ID from ' . ucfirst($provider));
            }

            // CASE 1: User is already logged in → CONNECT provider to current user (reassign if needed)
            if (auth()->check()) {
                $currentUser = auth()->user();

                // Check if this social account already exists
                $existing = SocialAccount::where('provider', $provider)
                                         ->where('provider_user_id', $providerId)
                                         ->first();

                if ($existing && $existing->user_id !== $currentUser->id) {
                    // Reassign the social account to the current user
                    $existing->user_id = $currentUser->id;
                    $existing->access_token = $socialUser->token;
                    $existing->refresh_token = $socialUser->refreshToken ?? null;
                    $existing->expires_at = $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null;
                    $existing->avatar = $avatar;
                    $existing->save();

                    return redirect('/dashboard')->with('success', ucfirst($provider) . ' reconnected to your current account!');
                }

                // No existing or already linked to this user
                SocialAccount::updateOrCreate(
                    [
                        'provider' => $provider,
                        'provider_user_id' => $providerId,
                    ],
                    [
                        'user_id' => $currentUser->id,
                        'access_token' => $socialUser->token,
                        'refresh_token' => $socialUser->refreshToken ?? null,
                        'expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
                        'avatar' => $avatar,
                    ]
                );

                return redirect('/dashboard')->with('success', ucfirst($provider) . ' connected successfully!');
            }

            // CASE 2: Not logged in → normal LOGIN / REGISTER
            $socialAccount = SocialAccount::where('provider', $provider)
                                          ->where('provider_user_id', $providerId)
                                          ->first();

            if ($socialAccount) {
                $user = $socialAccount->user;
            } else {
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => $name ?? 'User',
                        'email' => $email,
                        'username' => Str::slug($name ?? 'user') . rand(100, 999),
                        'password' => bcrypt(Str::random(16)),
                        'email_verified_at' => now(),
                    ]);
                }

                SocialAccount::create([
                    'provider' => $provider,
                    'provider_user_id' => $providerId,
                    'user_id' => $user->id,
                    'access_token' => $socialUser->token,
                    'refresh_token' => $socialUser->refreshToken ?? null,
                    'expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
                    'avatar' => $avatar,
                ]);
            }

            Auth::login($user, true);
            session()->regenerate();

            return redirect()->intended('/dashboard')->with('success', 'Logged in with ' . ucfirst($provider));
        } catch (\Exception $e) {
            Log::error('Socialite callback error: ' . $e->getMessage(), [
                'provider' => $provider,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    public function destroy($provider)
    {
        $user = auth()->user();
        $account = $user->socialAccounts()->where('provider', $provider)->first();
        if ($account) {
            $account->delete();
            return back()->with('success', ucfirst($provider) . ' disconnected.');
        }
        return back()->with('error', 'Account not found.');
    }

    public function fetchComments($videoId)
    {
        $user = auth()->user();
        $account = $user->socialAccounts()->where('provider', 'youtube')->first();
        if (!$account) {
            return response()->json(['error' => 'YouTube not connected'], 403);
        }

        $response = Http::withToken($account->access_token)
            ->get('https://www.googleapis.com/youtube/v3/commentThreads', [
                'part' => 'snippet',
                'videoId' => $videoId,
                'maxResults' => 20,
                'order' => 'time',
            ]);

        if ($response->successful()) {
            $comments = $response->json();
            $formatted = [];
            foreach ($comments['items'] ?? [] as $item) {
                $snippet = $item['snippet']['topLevelComment']['snippet'];
                $formatted[] = [
                    'authorDisplayName' => $snippet['authorDisplayName'],
                    'authorProfileImageUrl' => $snippet['authorProfileImageUrl'],
                    'textDisplay' => $snippet['textDisplay'],
                    'publishedAt' => \Carbon\Carbon::parse($snippet['publishedAt'])->diffForHumans(),
                ];
            }
            return response()->json(['comments' => $formatted]);
        }
        return response()->json(['error' => 'Unable to load comments'], 500);
    }

    public function postComment(Request $request, $videoId)
    {
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        $user = auth()->user();
        $account = $user->socialAccounts()->where('provider', 'youtube')->first();
        if (!$account) {
            return response()->json(['error' => 'YouTube not connected'], 403);
        }

        $response = Http::withToken($account->access_token)
            ->post('https://www.googleapis.com/youtube/v3/commentThreads?part=snippet', [
                'snippet' => [
                    'videoId' => $videoId,
                    'topLevelComment' => [
                        'snippet' => [
                            'textOriginal' => $request->comment,
                        ],
                    ],
                ],
            ]);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => 'Comment posted!']);
        }
        return response()->json(['error' => 'Failed to post comment: ' . $response->body()], 500);
    }
}
