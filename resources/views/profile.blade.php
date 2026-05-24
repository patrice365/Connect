<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile | Connect</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', 'Segoe UI', Arial, sans-serif; }
body { display: flex; background: #0a0a0a; color: #ffffff; height: 100vh; overflow: hidden; }

/* SIDEBAR & OVERLAY */
.overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1500; display: none; pointer-events: none; }
.overlay.active { display: block; pointer-events: auto; }
.sidebar { width: 320px; background: #121212; padding: 30px; border-right: 1px solid #1f1f1f; position: fixed; left: -320px; top: 0; bottom: 0; z-index: 2000; transition: 0.3s ease; }
.sidebar.open { left: 0; }
.sidebar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; }
.logo { font-size: 42px; font-weight: 900; letter-spacing: -2px; color: white; text-decoration: none; }
.logo span { color: #0ea5e9; }
.sidebar a { display: block; padding: 14px 18px; margin-bottom: 10px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 600; }
.sidebar a:hover { color: white; background: #1a1a1a; }
.sidebar a.active { background: rgba(14,165,233,0.15); color: #0ea5e9; }

/* LAYOUT */
.main-wrapper { flex: 1; display: flex; flex-direction: column; }
.topbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #1f1f1f; }
.topbar-left { display: flex; align-items: center; gap: 20px; }
.menu-toggle { width: 44px; height: 44px; border-radius: 8px; background: #181818; border: 1px solid #2d2d2d; color: white; cursor: pointer; }

/* FULL WIDTH CONTENT */
.content-body { padding: 40px; flex: 1; overflow-y: auto; display: flex; justify-content: center; }
.profile-container { width: 100%; max-width: 1200px; }

/* PROFILE UI */
.page-title { font-size: 28px; font-weight: 800; margin-bottom: 30px; }
.card { background: #121212; padding: 40px; border-radius: 20px; border: 1px solid #1f1f1f; width: 100%; }
.profile-avatar-section { display: flex; align-items: center; gap: 25px; margin-bottom: 40px; }
.large-avatar { width: 100px; height: 100px; border-radius: 50%; background: #0ea5e9; }
.avatar-info h2 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
.avatar-info p { color: #94a3b8; }

/* FORM */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; }
.form-group { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
.form-group label { font-size: 11px; color: #4b5563; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; }
.form-control { width: 100%; padding: 16px; background: #181818; border: 1px solid #2d2d2d; border-radius: 12px; color: white; font-size: 15px; outline: none; }
.form-control:focus { border-color: #0ea5e9; }
.btn-update { background: #0ea5e9; color: white; border: none; padding: 16px 32px; border-radius: 12px; font-weight: 700; cursor: pointer; float: right; transition: 0.2s; }
.btn-update:hover { background: #38bdf8; }
</style>
</head>
<body>

<div class="overlay" id="overlay"></div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">CON<span>NECT</span></div>
        <button class="menu-toggle" id="hideToggle">☰</button>
    </div>
    <a href="/">Home</a>
    <a href="/dashboard">Dashboard</a>
    <a href="/feed">Activity Feed</a>
    <a href="/posts/create">Create Post</a>
    <a href="/settings">Settings</a>
    <a href="/profile" class="active">Profile</a>
    <a href="/archive">Archive</a>
</nav>

<div class="main-wrapper">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle">☰</button>
            <div class="logo">CON<span>NECT</span></div>
        </div>
    </header>

    <div class="content-body">
        <div class="profile-container">
            <h1 class="page-title">Profile Settings</h1>
            
            <div class="card">
                <div class="profile-avatar-section">
                    <div class="large-avatar"></div>
                    <div class="avatar-info">
                        <h2>User Name</h2>
                        <p>Manage your personal profile and account settings.</p>
                    </div>
                </div>

                <form action="/profile" method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" value="User">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" value="Name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" value="user@connect.app">
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <label>Biography</label>
                        <input type="text" class="form-control" placeholder="Tell us about yourself...">
                    </div>

                    <div style="overflow: hidden;">
                        <button type="submit" class="btn-update">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const menuToggle = document.getElementById('menuToggle');
const hideToggle = document.getElementById('hideToggle');

menuToggle.onclick = () => { sidebar.classList.add('open'); overlay.classList.add('active'); };
hideToggle.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('active'); };
overlay.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('active'); };
</script>
</body>
</html>