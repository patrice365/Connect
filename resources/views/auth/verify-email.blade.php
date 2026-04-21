<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email | Connect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        
        body { 
            background: #050505; 
            color: white; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh;
            position: relative;
            overflow-y: auto;
            padding: 20px;
        }

        .mesh-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: #050505;
            background-image: 
                radial-gradient(at 0% 0%, rgba(14, 165, 233, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(139, 92, 246, 0.1) 0px, transparent 50%);
            z-index: -1;
        }

        .blob {
            position: fixed;
            width: 500px; height: 500px;
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
            filter: blur(120px);
            border-radius: 50%;
            z-index: -1;
            opacity: 0.2;
            animation: move 20s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(-10%, -10%) rotate(0deg); }
            to { transform: translate(20%, 20%) rotate(360deg); }
        }

        .auth-card { 
            background: rgba(15, 15, 15, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 50px 40px; 
            border-radius: 30px; 
            width: 100%; 
            max-width: 480px; 
            text-align: center;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(255,255,255,0.02);
            animation: fadeIn 0.8s ease-out;
            margin: 20px 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo { 
            font-size: 38px; font-weight: 900; 
            color: #ffffff;
            letter-spacing: -2px;
            text-decoration: none; display: block; margin-bottom: 8px;
        }
        .logo span { color: #0ea5e9; }

        .subtitle { color: #94a3b8; font-size: 15px; margin-bottom: 30px; }

        .message-box {
            background: rgba(14, 165, 233, 0.1);
            border: 1px solid rgba(14, 165, 233, 0.3);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            color: #cbd5e1;
        }

        .btn-primary { 
            display: inline-block;
            width: 100%; 
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            color: white; 
            padding: 16px; 
            border: none; 
            border-radius: 15px; 
            font-weight: 800; 
            font-size: 16px;
            cursor: pointer; 
            transition: 0.3s;
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
        }
        .btn-primary:hover { 
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(14, 165, 233, 0.6);
        }

        .auth-footer { margin-top: 25px; color: #64748b; font-size: 14px; }
        .auth-footer button {
            background: none;
            border: none;
            color: #0ea5e9;
            font-weight: 700;
            cursor: pointer;
            text-decoration: underline;
        }
        .auth-footer button:hover { color: #38bdf8; }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="mesh-bg"></div>
    <div class="blob"></div>

    <div class="auth-card">
        <a href="/" class="logo">CON<span>NECT</span></a>
        <p class="subtitle">Verify your email address</p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert-success">
                A new verification link has been sent to your email address.
            </div>
        @endif

        <div class="message-box">
            <i class="fas fa-envelope" style="font-size: 32px; color: #0ea5e9; margin-bottom: 15px;"></i>
            <p style="margin-bottom: 10px;">
                Thanks for signing up! Before getting started, please verify your email address by clicking the link we just emailed to you.
            </p>
            <p style="font-size: 14px; color: #94a3b8;">
                If you didn't receive the email, we'll gladly send you another.
            </p>
        </div>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">Resend Verification Email</button>
        </form>

        <div class="auth-footer">
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background:none;border:none;color:#0ea5e9;cursor:pointer;text-decoration:underline;">
                    Back to Home Page
                </button>
            </form>
        </div>
    </div>
</body>
</html>