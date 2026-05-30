<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect | Login</title>
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
        .auth-card { background: rgba(15,15,15,0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); padding: 50px 40px; border-radius: 30px; width: 100%; max-width: 440px; text-align: center; }
        .logo { font-size: 38px; font-weight: 900; color: #fff; letter-spacing: -2px; text-decoration: none; }
        .logo span { color: #0ea5e9; }
        .subtitle { color: #94a3b8; font-size: 15px; margin-bottom: 40px; }
        .form-group { text-align: left; margin-bottom: 22px; }
        .form-group label { display: block; color: #cbd5e1; font-size: 13px; font-weight: 600; margin-bottom: 10px; }
        input { width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); padding: 16px; border-radius: 15px; color: white; outline: none; }
        input:focus { border-color: #0ea5e9; background: rgba(14,165,233,0.05); }
        .btn-auth { width: 100%; background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); color: white; padding: 18px; border: none; border-radius: 15px; font-weight: 800; font-size: 16px; cursor: pointer; margin-top: 15px; }
        .auth-footer { margin-top: 30px; color: #64748b; font-size: 14px; }
        .auth-footer a { color: #0ea5e9; text-decoration: none; font-weight: 700; }
        .social-divider { display: flex; align-items: center; gap: 15px; margin: 25px 0 15px; }
        .social-divider hr { flex: 1; border-color: #2d2d2d; }
        .social-divider span { color: #64748b; font-size: 13px; text-transform: uppercase; }
        .two-buttons { display: flex; gap: 15px; }
        .social-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 14px; border-radius: 12px; font-weight: 700; font-size: 15px; text-decoration: none; transition: 0.2s; }
        .social-btn.youtube { background: #FF0000; color: white; }
        .social-btn.github { background: #333; color: white; }
        .social-btn:hover { transform: scale(1.02); filter: brightness(1.1); }
    </style>
</head>
<body>
    <div class="mesh-bg"></div><div class="blob"></div>
    <div class="auth-card">
        <a href="/" class="logo">CON<span>NECT</span></a>
        <p class="subtitle">Welcome back to your hub</p>
        <?php if($errors->any()): ?>
            <div style="background: rgba(239,68,68,0.2); border:1px solid #ef4444; color:#fca5a5; padding:12px; border-radius:10px; margin-bottom:20px; text-align:left;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <p><?php echo e($error); ?></p> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group"><label for="email">Email Address</label><input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required autofocus></div>
            <div class="form-group"><label for="password">Password</label><input type="password" name="password" id="password" required></div>
            <button type="submit" class="btn-auth">Sign In</button>
        </form>
        <div class="social-divider"><hr><span>or sign in using</span><hr></div>
        <div class="two-buttons">
            <a href="<?php echo e(route('social.redirect', 'youtube')); ?>" class="social-btn youtube"><i class="fab fa-youtube"></i> YouTube</a>
            <a href="<?php echo e(route('social.redirect', 'github')); ?>" class="social-btn github"><i class="fab fa-github"></i> GitHub</a>
        </div>
        <div class="auth-footer">New here? <a href="<?php echo e(route('register')); ?>">Create an account</a></div>
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\Connect\resources\views/auth/login.blade.php ENDPATH**/ ?>