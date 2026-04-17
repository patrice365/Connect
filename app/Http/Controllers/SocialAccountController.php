<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SocialAccountController extends Controller
{
    /**
     * Redirect to OAuth provider for authentication
     */
    public function redirect($provider)
    {
        // Validate provider
        $allowed = ['twitter', 'facebook', 'linkedin', 'github'];
        if (!in_array($provider, $allowed)) {
            return Redirect::back()->with('error', 'Invalid provider.');
        }

        // For now, store the provider in session and redirect to provider
        // In production, you would use Laravel Socialite or similar library
        session(['oauth_provider' => $provider]);
        
        return Redirect::away('https://oauth.provider.com/authorize?client_id=YOUR_CLIENT_ID&redirect_uri=' . route('social.callback', $provider));
    }

    /**
     * Handle OAuth callback from provider
     */
    public function callback($provider)
    {
        // Validate provider
        $allowed = ['twitter', 'facebook', 'linkedin', 'github'];
        if (!in_array($provider, $allowed)) {
            return Redirect::route('profile.edit')->with('error', 'Invalid provider.');
        }

        // In production, exchange authorization code for access token using OAuth library
        // This is a simplified example
        $accessToken = request('access_token') ?? session('oauth_token');
        $providerUserId = request('user_id') ?? session('oauth_user_id');

        if (!$accessToken || !$providerUserId) {
            return Redirect::route('profile.edit')->with('error', 'Failed to authenticate with ' . ucfirst($provider) . '.');
        }

        // Find or create social account
        $socialAccount = SocialAccount::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'provider' => $provider,
            ],
            [
                'provider_user_id' => $providerUserId,
                'access_token' => $accessToken,
                'refresh_token' => request('refresh_token'),
                'expires_at' => request('expires_at'),
            ]
        );

        return Redirect::route('profile.edit')->with('success', ucfirst($provider) . ' account connected successfully!');
    }

    /**
     * Disconnect a social account
     */
    public function destroy($provider)
    {
        // Validate provider
        $allowed = ['twitter', 'facebook', 'linkedin', 'github'];
        if (!in_array($provider, $allowed)) {
            return Redirect::back()->with('error', 'Invalid provider.');
        }

        $deleted = SocialAccount::where('user_id', Auth::id())
            ->where('provider', $provider)
            ->delete();

        if ($deleted) {
            return Redirect::route('profile.edit')->with('success', ucfirst($provider) . ' account disconnected.');
        }

        return Redirect::route('profile.edit')->with('error', 'Social account not found.');
    }
}
