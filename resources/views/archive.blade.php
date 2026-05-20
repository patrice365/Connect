<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Archive | Connect</title>
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
.archive-container { width: 100%; max-width: 1200px; }

/* ARCHIVE UI */
.page-title { font-size: 28px; font-weight: 800; margin-bottom: 30px; }
.card { background: #121212; padding: 40px; border-radius: 20px; border: 1px solid #1f1f1f; width: 100%; }
.archive-item { display: flex; justify-content: space-between; align-items: center; padding: 24px 0; border-bottom: 1px solid #1f1f1f; }
.archive-item:last-child { border-bottom: none; }
.archive-meta { display: flex; gap: 15px; font-size: 13px; color: #94a3b8; margin-top: 6px; }
.btn-restore { background: #1a1a1a; border: 1px solid #2d2d2d; color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; }
.btn-restore:hover { background: #262626; border-color: #0ea5e9; color: #0ea5e9; }
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
    <a href="/profile">Profile</a>
    <a href="/archive" class="active">Archive</a>
</nav>

<div class="main-wrapper">
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle">☰</button>
            <div class="logo">CON<span>NECT</span></div>
        </div>
    </header>

    <div class="content-body">
        <div class="archive-container">
            <h1 class="page-title">Content Archive</h1>
            
            <div class="card">
                <div class="archive-item">
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600;">Quarterly Performance Video</h4>
                        <div class="archive-meta">
                            <span style="color: #ff0000;">YouTube</span>
                            <span>•</span>
                            <span>Archived 2 days ago</span>
                        </div>
                    </div>
                    <button class="btn-restore">Restore</button>
                </div>

                <div class="archive-item">
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600;">Product Launch Teaser</h4>
                        <div class="archive-meta">
                            <span style="color: #0ea5e9;">Twitter / X</span>
                            <span>•</span>
                            <span>Archived 1 week ago</span>
                        </div>
                    </div>
                    <button class="btn-restore">Restore</button>
                </div>

                <div class="archive-item">
                    <div>
                        <h4 style="font-size: 18px; font-weight: 600;">Community Thread</h4>
                        <div class="archive-meta">
                            <span style="color: #ff4500;">Reddit</span>
                            <span>•</span>
                            <span>Archived 3 weeks ago</span>
                        </div>
                    </div>
                    <button class="btn-restore">Restore</button>
                </div>
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