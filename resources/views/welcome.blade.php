<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Connect | Unified Social Hub</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        html, body {
            height: 100%;
            min-height: 100%;
            background: #05070d;
            color: white;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* =========================
           AURORA BACKGROUND (NEW)
        ========================== */
        .bg-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;

            background:
                radial-gradient(circle at 15% 20%, rgba(14, 165, 233, 0.14), transparent 45%),
                radial-gradient(circle at 85% 75%, rgba(139, 92, 246, 0.14), transparent 50%),
                radial-gradient(circle at 70% 10%, rgba(34, 211, 238, 0.10), transparent 55%),
                radial-gradient(circle at 30% 90%, rgba(99, 102, 241, 0.08), transparent 60%),
                #05070d;
        }

        /* soft vignette for depth */
        .bg-container::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center,
                transparent 35%,
                rgba(0,0,0,0.55) 100%);
        }

        /* =========================
           NAVBAR
        ========================== */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 32px 8%;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;

            backdrop-filter: blur(20px);
            background: rgba(0, 0, 0, 0.55);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .logo {
            font-size: 34px;
            font-weight: 900;
            letter-spacing: -1.5px;
            text-decoration: none;
            color: white;
        }

        .logo span {
            color: #0ea5e9;
        }

        .nav-auth {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            padding: 10px 22px;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            font-size: 14px;
            transition: 0.25s ease;
        }

        .nav-btn-login {
            color: #cbd5e1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-btn-login:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-btn-signup {
            background: #0ea5e9;
            color: white;
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.25);
        }

        .nav-btn-signup:hover {
            background: #38bdf8;
            transform: translateY(-1px);
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.35);
        }

        /* =========================
           HERO
        ========================== */
        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 100vh;
            padding: 120px 20px 0;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: clamp(45px, 8vw, 95px);
            font-weight: 900;
            letter-spacing: -3px;
            line-height: 1.1;
            margin-bottom: 30px;

            background: linear-gradient(to bottom, #ffffff 70%, rgba(255,255,255,0.5));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 20px;
            color: rgba(255, 255, 255, 0.7);
            max-width: 800px;
            margin-bottom: 45px;
            line-height: 1.6;
            font-weight: 500;
        }

        .sources {
            color: white;
            font-weight: 700;
        }

        .hero-btn {
            padding: 18px 55px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 800;
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.25);
        }

        .hero-btn:hover {
            box-shadow: 0 15px 40px rgba(14, 165, 233, 0.35);
        }

        /* =========================
           FOOTER
        ========================== */
        footer {
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(20px);
            padding: 25px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 8%;
        }

        .footer-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .footer-tagline {
            color: #64748b;
            font-size: 13px;
            border-left: 1px solid rgba(255,255,255,0.15);
            padding-left: 25px;
        }

        .footer-center {
            display: flex;
            align-items: center;
            gap: 30px;
            color: #94a3b8;
            font-size: 13px;
        }

        .footer-center b {
            color: #0ea5e9;
            font-size: 16px;
        }

        .footer-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .social-icons {
            display: flex;
            gap: 15px;
        }

        .social-icons a {
            color: #64748b;
            font-size: 18px;
            transition: 0.3s;
        }

        .social-icons a:hover {
            color: #0ea5e9;
            transform: translateY(-2px);
        }

        .copyright {
            font-size: 11px;
            color: #475569;
            margin-left: 20px;
        }

        @media (max-width: 992px) {
            .footer-flex {
                flex-direction: column;
                text-align: center;
            }

            .footer-left {
                flex-direction: column;
                gap: 10px;
            }

            .footer-tagline {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>

<body>

    <div class="bg-container"></div>

    <nav>
        <a href="/" class="logo">CON<span>NECT</span></a>

        <div class="nav-auth">
            <a href="/login" class="nav-btn-login btn">Login</a>
            <a href="/register" class="nav-btn-signup btn">Sign Up</a>
        </div>
    </nav>

    <main class="hero">
        <h1>All Your Socials<br>in One Place</h1>

        <p>
            The elite workspace to manage and amplify your digital presence. Seamlessly integrate
            <span class="sources">Facebook, Instagram, Twitter/X, YouTube and more</span>
            into a single, high-performance hub.
        </p>

        <a href="/register" class="nav-btn-signup btn hero-btn">Get Started</a>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-flex">

                <div class="footer-left">
                    <span class="logo" style="font-size: 22px;">CON<span>NECT</span></span>
                    <span class="footer-tagline">Streamlining your social workflow</span>
                </div>

                <div class="footer-center">
                    <span>Tacloban City, PH</span>
                    <b>+63 995 109 7675</b>
                </div>

                <div class="footer-right">
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                    <span class="copyright">&copy; 2026 Connect Inc.</span>
                </div>

            </div>
        </div>
    </footer>

</body>
</html>
=======
    <title>Welcome</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-4">Welcome</h1>
        <p class="text-lg text-gray-700 mb-8">Welcome to our application!</p>
        
        @auth
            <a href="{{ route('dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Go to Dashboard
            </a>
        @else
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Login
                </a>
                <a href="{{ route('register') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Register
                </a>
            </div>
        @endauth
    </div>
</body>
</html>
>>>>>>> CON-2-Create-database-schema
