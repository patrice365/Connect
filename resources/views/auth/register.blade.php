<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect | Create Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #050505; color: white; display: flex; justify-content: center; align-items: center; min-height: 100vh; position: relative; padding: 20px; }
        .mesh-bg { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: #050505;
            background-image: radial-gradient(at 0% 0%, rgba(14,165,233,0.15) 0px, transparent 50%),
                              radial-gradient(at 100% 0%, rgba(139,92,246,0.1) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(14,165,233,0.15) 0px, transparent 50%),
                              radial-gradient(at 0% 100%, rgba(139,92,246,0.1) 0px, transparent 50%); z-index: -1; }
        .blob { position: fixed; width: 500px; height: 500px; background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%); filter: blur(120px); border-radius: 50%; z-index: -1; opacity: 0.2; animation: move 20s infinite alternate; }
        @keyframes move { from { transform: translate(-10%, -10%) rotate(0deg); } to { transform: translate(20%, 20%) rotate(360deg); } }
        .auth-card { background: rgba(15,15,15,0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); padding: 50px 40px; border-radius: 30px; width: 100%; max-width: 460px; text-align: center; }
        .logo { font-size: 38px; font-weight: 900; color: #fff; letter-spacing: -2px; text-decoration: none; }
        .logo span { color: #0ea5e9; }
        .subtitle { color: #94a3b8; font-size: 15px; margin-bottom: 40px; }
        .form-group { text-align: left; margin-bottom: 22px; }
        .form-group label { display: block; color: #cbd5e1; font-size: 13px; font-weight: 600; margin-bottom: 10px; }
        input { width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); padding: 16px; border-radius: 15px; color: white; outline: none; }
        input:focus { border-color: #0ea5e9; }
        .btn-auth { width: 100%; background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); color: white; padding: 18px; border: none; border-radius: 15px; font-weight: 800; font-size: 16px; cursor: pointer; margin-top: 15px; opacity: 0.5; pointer-events: none; }
        .btn-auth.enabled { opacity: 1; pointer-events: auto; }
        .auth-footer { margin-top: 30px; color: #64748b; font-size: 14px; }
        .auth-footer a { color: #0ea5e9; text-decoration: none; font-weight: 700; }
        .social-divider { display: flex; align-items: center; gap: 15px; margin: 25px 0 15px; }
        .social-divider hr { flex: 1; border-color: #2d2d2d; }
        .social-divider span { color: #64748b; font-size: 13px; text-transform: uppercase; }
        .two-buttons { display: flex; gap: 15px; }
        .social-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 14px; border-radius: 12px; font-weight: 700; font-size: 15px; text-decoration: none; }
        .social-btn.youtube { background: #FF0000; color: white; }
        .social-btn.github { background: #333; color: white; }
        .social-btn:hover { transform: scale(1.02); filter: brightness(1.1); }
        .recaptcha-container { margin: 20px 0 10px; text-align: left; }
    </style>
</head>
<body>
    <div class="mesh-bg"></div><div class="blob"></div>
    <div class="auth-card">
        <a href="/" class="logo">CON<span>NECT</span></a>
        <p class="subtitle">Elevate your social management</p>
        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.2); border:1px solid #ef4444; color:#fca5a5; padding:12px; border-radius:10px; margin-bottom:20px; text-align:left;">
                @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
            </div>
        @endif
        <form id="register-form" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group"><label for="name">Full Name</label><input type="text" name="name" id="name" value="{{ old('name') }}" required></div>
            <div class="form-group"><label for="username">Username</label><input type="text" name="username" id="username" value="{{ old('username') }}" required></div>
            <div class="form-group"><label for="email">Email Address</label><input type="email" name="email" id="email" value="{{ old('email') }}" required></div>
            <div class="form-group"><label for="password">Password</label><input type="password" name="password" id="password" required></div>
            <div class="form-group"><label for="password_confirmation">Confirm Password</label><input type="password" name="password_confirmation" id="password_confirmation" required></div>
            <div class="recaptcha-container"><div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.key') ?? env('RECAPTCHA_SITE_KEY') }}" data-callback="recaptchaCallback" data-expired-callback="recaptchaExpired"></div></div>
            <input type="hidden" id="recaptcha-verified" value="0">
            <button type="submit" id="signup-btn" class="btn-auth" disabled>Sign Up</button>
        </form>
        <div class="social-divider"><hr><span>or sign up using</span><hr></div>
        <div class="two-buttons">
            <a href="{{ route('social.redirect', 'youtube') }}" class="social-btn youtube"><i class="fab fa-youtube"></i> YouTube</a>
            <a href="{{ route('social.redirect', 'github') }}" class="social-btn github"><i class="fab fa-github"></i> GitHub</a>
        </div>
        <div class="auth-footer">Already have an account? <a href="{{ route('login') }}">Login here</a></div>
    </div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        const form = document.getElementById('register-form');
        const btn = document.getElementById('signup-btn');
        const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
        const recaptchaVerified = document.getElementById('recaptcha-verified');
        function checkFormValidity() {
            let allFilled = true;
            inputs.forEach(input => { if (input.value.trim() === '') allFilled = false; });
            const captchaOk = recaptchaVerified.value === '1';
            if (allFilled && captchaOk) { btn.disabled = false; btn.classList.add('enabled'); }
            else { btn.disabled = true; btn.classList.remove('enabled'); }
        }
        inputs.forEach(input => input.addEventListener('input', checkFormValidity));
        window.recaptchaCallback = function() { recaptchaVerified.value = '1'; checkFormValidity(); };
        window.recaptchaExpired = function() { recaptchaVerified.value = '0'; checkFormValidity(); };
        checkFormValidity();
    </script>
</body>
</html>