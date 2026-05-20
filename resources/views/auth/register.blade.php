<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect | Create Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { 
            background: #050505; color: white; display: flex; justify-content: center; align-items: center; 
            min-height: 100vh; position: relative; overflow-y: auto; padding: 20px;
        }
        .mesh-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: #050505;
            background-image: radial-gradient(at 0% 0%, rgba(14,165,233,0.15) 0px, transparent 50%),
                              radial-gradient(at 100% 0%, rgba(139,92,246,0.1) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(14,165,233,0.15) 0px, transparent 50%),
                              radial-gradient(at 0% 100%, rgba(139,92,246,0.1) 0px, transparent 50%);
            z-index: -1;
        }
        .blob {
            position: fixed; width: 500px; height: 500px; background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
            filter: blur(120px); border-radius: 50%; z-index: -1; opacity: 0.2; animation: move 20s infinite alternate;
        }
        @keyframes move {
            from { transform: translate(-10%, -10%) rotate(0deg); }
            to { transform: translate(20%, 20%) rotate(360deg); }
        }
        .auth-card { 
            background: rgba(15,15,15,0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1);
            padding: 50px 40px; border-radius: 30px; width: 100%; max-width: 440px; text-align: center;
            box-shadow: 0 0 40px rgba(0,0,0,0.6); margin: 20px 0;
        }
        .logo { font-size: 38px; font-weight: 900; color: white; letter-spacing: -2px; text-decoration: none; }
        .logo span { color: #0ea5e9; }
        .subtitle { color: #94a3b8; font-size: 15px; margin-bottom: 40px; }
        .form-group { text-align: left; margin-bottom: 22px; }
        .form-group label { display: block; color: #cbd5e1; font-size: 13px; font-weight: 600; margin-bottom: 10px; }
        input { 
            width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1);
            padding: 16px; border-radius: 15px; color: white; outline: none;
        }
        input:focus { border-color: #0ea5e9; background: rgba(14,165,233,0.05); box-shadow: 0 0 20px rgba(14,165,233,0.2); }
        .btn-auth { 
            width: 100%; background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); color: white;
            padding: 18px; border: none; border-radius: 15px; font-weight: 800; font-size: 16px;
            cursor: pointer; transition: 0.3s; margin-top: 15px; text-transform: uppercase;
            opacity: 0.5; pointer-events: none;
        }
        .btn-auth.enabled { opacity: 1; pointer-events: auto; }
        .auth-footer { margin-top: 30px; color: #64748b; font-size: 14px; }
        .auth-footer a { color: #0ea5e9; text-decoration: none; font-weight: 700; }
        .recaptcha-container {
            margin: 20px 0 10px 0;
            display: flex;
            justify-content: center;    /* centers horizontally */
        }
    </style>
</head>
<body>
    <div class="mesh-bg"></div>
    <div class="blob"></div>

    <div class="auth-card">
        <a href="/" class="logo">CON<span>NECT</span></a>
        <p class="subtitle">Elevate your social management</p>

        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.2); border:1px solid #ef4444; color:#fca5a5; padding:12px; border-radius:10px; margin-bottom:20px; text-align:left;">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form id="register-form" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group"><label>Full Name</label><input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Alex Rivera" required></div>
            <div class="form-group"><label>Username</label><input type="text" name="username" value="{{ old('username') }}" placeholder="alexrivera" required></div>
            <div class="form-group"><label>Email Address</label><input type="email" name="email" value="{{ old('email') }}" placeholder="alex@connect.com" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" placeholder="Enter a strong password" required></div>
            <div class="form-group"><label>Confirm Password</label><input type="password" name="password_confirmation" placeholder="Re-enter your password" required></div>

            {{-- CAPTCHA --}}
            <div class="recaptcha-container">
                <div id="g-recaptcha"></div>
            </div>

            <button type="submit" id="signup-btn" class="btn-auth" disabled>Sign Up</button>
        </form>

        <div class="auth-footer">
            Part of the hub? <a href="{{ route('login') }}">Login here</a>
        </div>
    </div>

    <script>
        const RECAPTCHA_SITE_KEY = '{{ config("services.recaptcha.key") }}';
        let recaptchaReady = false;
        
        function checkFormValidity() {
            const form = document.getElementById('register-form');
            const btn = document.getElementById('signup-btn');
            const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
            
            let allFilled = true;
            inputs.forEach(input => { if (input.value.trim() === '') allFilled = false; });
            
            // Only check reCAPTCHA if widget is ready
            const captchaOk = recaptchaReady && typeof grecaptcha !== 'undefined' && grecaptcha.getResponse() !== '';
            
            if (allFilled && captchaOk) {
                btn.disabled = false;
                btn.classList.add('enabled');
            } else {
                btn.disabled = true;
                btn.classList.remove('enabled');
            }
        }
        
        function onRecaptchaSuccess() {
            recaptchaReady = true;
            checkFormValidity();
        }

        function onRecaptchaExpired() {
            recaptchaReady = false;
            checkFormValidity();
        }

        // Load and render reCAPTCHA
        function loadRecaptcha() {
            if (RECAPTCHA_SITE_KEY && typeof grecaptcha !== 'undefined') {
                try {
                    grecaptcha.render('g-recaptcha', {
                        'sitekey': RECAPTCHA_SITE_KEY,
                        'callback': onRecaptchaSuccess,
                        'expired-callback': onRecaptchaExpired,
                        'size': 'normal'
                    });
                } catch(e) {
                    console.error('reCAPTCHA render error:', e);
                }
            }
        }

        // Load Google reCAPTCHA script
        const script = document.createElement('script');
        script.src = 'https://www.google.com/recaptcha/api.js?onload=loadRecaptcha&render=explicit';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);

        const form = document.getElementById('register-form');
        const btn = document.getElementById('signup-btn');
        const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');

        // Check form validity on input change
        inputs.forEach(input => input.addEventListener('input', checkFormValidity));
    </script>
</body>
</html>

