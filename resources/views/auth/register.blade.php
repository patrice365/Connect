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
            background: #050505; 
            color: white; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* VIBRANT MESH GRADIENT BACKGROUND */
        .mesh-bg {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: #050505;
            background-image: 
                radial-gradient(at 0% 0%, rgba(14, 165, 233, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(14, 165, 233, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(139, 92, 246, 0.1) 0px, transparent 50%);
            z-index: -1;
        }

        /* FLOATING BLOBS */
        .blob {
            position: absolute;
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

        /* GLASS CARD WITH GLOW BORDER */
        .auth-card { 
            background: rgba(15, 15, 15, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 50px 40px; 
            border-radius: 30px; 
            width: 100%; 
            max-width: 440px; 
            text-align: center;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.6), inset 0 0 20px rgba(255,255,255,0.02);
            animation: fadeIn 0.8s ease-out;
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

        .subtitle { color: #94a3b8; font-size: 15px; margin-bottom: 40px; }

        .form-group { text-align: left; margin-bottom: 22px; }
        .form-group label { display: block; color: #cbd5e1; font-size: 13px; font-weight: 600; margin-bottom: 10px; padding-left: 5px; }
        
        input { 
            width: 100%; 
            background: rgba(255, 255, 255, 0.03); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            padding: 16px; 
            border-radius: 15px; 
            color: white; 
            outline: none;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        input:focus { 
            border-color: #0ea5e9; 
            background: rgba(14, 165, 233, 0.05);
            box-shadow: 0 0 20px rgba(14, 165, 233, 0.2);
        }

        .btn-auth { 
            width: 100%; 
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            color: white; 
            padding: 18px; 
            border: none; 
            border-radius: 15px; 
            font-weight: 800; 
            font-size: 16px;
            cursor: pointer; 
            transition: 0.3s;
            box-shadow: 0 10px 25px rgba(14, 165, 233, 0.4);
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-auth:hover { 
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(14, 165, 233, 0.6);
        }

        .auth-footer { margin-top: 30px; color: #64748b; font-size: 14px; }
        .auth-footer a { color: #0ea5e9; text-decoration: none; font-weight: 700; transition: 0.2s; }
        .auth-footer a:hover { color: #38bdf8; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="mesh-bg"></div>
    <div class="blob"></div>

    <div class="auth-card">
        <a href="/" class="logo">CON<span>NECT</span></a>
        <p class="subtitle">Elevate your social management</p>

        <form action="/register" method="POST">
            @csrf
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" placeholder="e.g. Alex Rivera" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" placeholder="alex@connect.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-auth">Create Account</button>
        </form>

        <div class="auth-footer">
            Part of the hub? <a href="/login">Login here</a>
        </div>
    </div>
</body>
</html>
<!-- AUTH VERSION -->

