<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    /**
     * Show the "Verify you are human" page.
     */
    public function show()
    {
        return view('auth.verify-human');
    }

    /**
     * Handle the CAPTCHA form submission.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'g-recaptcha-response' => 'required|captcha'
        ], [
            'g-recaptcha-response.required' => 'Please complete the CAPTCHA.',
            'g-recaptcha-response.captcha'  => 'CAPTCHA verification failed. Please try again.',
        ]);

        // Mark the user as human‑verified in the session
        session(['human_verified' => true]);

        // Redirect to the intended page (usually dashboard)
        return redirect()->intended(route('dashboard'));
    }
}