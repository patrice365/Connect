<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validate input
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Rate limiting – throttle 5 attempts per minute per email+IP
        $throttleKey = strtolower($request->input('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in $seconds seconds.",
            ]);
        }

        // 3. Attempt authentication
        $login = $request->input('email');
        $password = $request->input('password');

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $login, 'password' => $password];
        } else {
            $credentials = ['username' => $login, 'password' => $password];
        }
        $remember = $request->filled('remember');

        if (! Auth::attempt($credentials, $remember)) {
            // Count failed attempt
            RateLimiter::hit($throttleKey, 60);

            // Redirect back with error and keep email field
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Authentication succeeded – clear rate limiter
        RateLimiter::clear($throttleKey);

        // 4. Regenerate session (fixation protection)
        $request->session()->regenerate();

        // 5. Redirect based on email verification status
        $user = Auth::user();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}