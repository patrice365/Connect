<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect | Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Shared Eye-Catching Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: #050505; color: white; display: flex; justify-content: center; align-items: center; min-height: 100vh; position: relative; overflow: hidden; }
        
        .mesh-bg {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background-image: 
                radial-gradient(at 0% 0%, rgba(14, 165, 233, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.15) 0px, transparent 50%);
            z-index: -1;
        }

        .auth-card { 
            background: rgba(15, 15, 15, 0.7); backdrop-filter: blur(25px); border: 1px solid rgba(255, 255, 255, 0.1); 
            padding: 50px 40px; border-radius: 30px; width: 100%; max-width: 440px; text-align: center;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.8);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .logo { font-size: 38px; font-weight: 900; color: #fff; letter-spacing: -2px; text-decoration: none; display: block; margin-bottom: 8px; }
        .logo span { color: #0ea5e9; }
        .subtitle { color: #94a3b8; font-size: 15px; margin-bottom: 40px; }

        .alert-success {
            background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; 
            padding: 16px; border-radius: 15px; margin-bottom: 25px; font-size: 14px; text-align: left;
            display: flex; align-items: center; gap: 12px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }

        .form-group { text-align: left; margin-bottom: 22px; }
        .form-group label { display: block; color: #cbd5e1; font-size: 13px; font-weight: 600; margin-bottom: 10px; }
        
        input { 
            width: 100%; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); 
            padding: 16px; border-radius: 15px; color: white; outline: none; transition: 0.3s;
        }
        input:focus { border-color: #0ea5e9; background: rgba(14, 165, 233, 0.05); }

        .btn-auth { 
            width: 100%; background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); color: white; 
            padding: 18px; border: none; border-radius: 15px; font-weight: 800; font-size: 16px; 
            cursor: pointer; transition: 0.3s; box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
        }
        .btn-auth:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(14, 165, 233, 0.6); }

        .auth-footer { margin-top: 30px; color: #64748b; font-size: 14px; }
        .auth-footer a { color: #0ea5e9; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
    <div class="mesh-bg"></div>

    <div class="auth-card">
        <a href="/" class="logo">CON<span>NECT</span></a>
        <p class="subtitle">Welcome back to your hub</p>

        @if(request()->get('registered'))
            <div class="alert-success">
                <i class="fas fa-check-circle" style="font-size: 18px;"></i>
                <span>Ready to go! Log in with your new account.</span>
            </div>
        @endif

        <form action="/dashboard" method="GET">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn-auth">Sign In</button>
        </form>

        <div class="auth-footer">
            New here? <a href="/register">Create an account</a>
        </div>
    </div>
</body>
</html>