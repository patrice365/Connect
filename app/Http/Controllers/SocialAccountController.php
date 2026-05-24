<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAccountController extends Controller
{
    protected $providers = ['youtube', 'github'];

    public function redirect(Request $request, string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);

        session(['social_mode' => $request->query('mode', 'login')]);

        if ($provider === 'youtube') {
            return Socialite::driver('google')
                ->scopes(['https://www.googleapis.com/auth/youtube.readonly'])
                ->redirect();
        }

        if ($provider === 'github') {
            return Socialite::driver('github')
                ->scopes(['read:user'])   // minimal scope
                ->redirect();
        }
    }

    public function callback(Request $request, string $provider)
    {
        abort_unless(in_array($provider, $this->providers), 404);

        $driver = match ($provider) {
            'youtube' => 'google',
            'github'  => 'github',
        };

        try {
            $socialUser = Socialite::driver($driver)->user();
        } catch (\Exception $e) {
            Log::error('OAuth callback failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);

            $message = $e->getMessage() ?: 'Connection failed. Please check your app settings.';
            return redirect()->route('dashboard')
                ->with('error', 'Connection failed: ' . $message);
        }

        $mode = session('social_mode', 'login');
        session()->forget('social_mode');

        // User already logged in – just connect the social account
        if (Auth::check()) {
            $user = Auth::user();

            $existing = SocialAccount::where('provider', $provider)
                ->where('provider_user_id', $socialUser->getId())
                ->first();

            if ($existing) {
                if ($existing->user_id !== $user->id) {
                    return redirect()->route('dashboard')
                        ->with('error', 'This account is already connected to another user.');
                }
                $existing->update([
                    'access_token'  => $socialUser->token,
                    'refresh_token' => $socialUser->refreshToken,
                ]);
                return redirect()->route('dashboard')
                    ->with('success', ucfirst($provider) . ' account re-connected.');
            }

            $user->socialAccounts()->create([
                'provider'         => $provider,
                'provider_user_id' => $socialUser->getId(),
                'access_token'     => $socialUser->token,
                'refresh_token'    => $socialUser->refreshToken,
            ]);
            return redirect()->route('dashboard')
                ->with('success', ucfirst($provider) . ' account connected successfully!');
        }

        // Not logged in – social login or registration
        $existing = SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $socialUser->getId())->first();

        if ($existing) {
            Auth::login($existing->user);
            return redirect()->intended(route('dashboard'));
        }

        if ($mode === 'register') {
            $name  = $socialUser->getName() ?? $socialUser->getNickname() ?? 'New User';
            $email = $socialUser->getEmail() ?? $socialUser->getId() . '@' . $provider . '.local';
            $user  = User::create([
                'name'     => $name,
                'email'    => $email,
                'username' => $socialUser->getNickname() ?? $socialUser->getId(),
                'password' => bcrypt(uniqid()),
            ]);
            if ($socialUser->getEmail()) {
                $user->markEmailAsVerified();
            }
            $user->socialAccounts()->create([
                'provider'         => $provider,
                'provider_user_id' => $socialUser->getId(),
                'access_token'     => $socialUser->token,
                'refresh_token'    => $socialUser->refreshToken,
            ]);
            Auth::login($user);
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Account created and connected!');
        }

        return redirect()->route('login')
            ->with('error', 'No account linked to this ' . ucfirst($provider) . ' profile.');
    }

    public function destroy(Request $request, string $provider)
    {
        $user = Auth::user();
        $account = $user->socialAccounts()->where('provider', $provider)->first();

        if ($account) {
            $account->delete();
            return redirect()->route('dashboard')
                ->with('success', ucfirst($provider) . ' account disconnected.');
        }

        return redirect()->route('dashboard')
            ->with('error', 'No connected ' . ucfirst($provider) . ' account found.');
    }
}