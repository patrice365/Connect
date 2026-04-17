<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connect - Create Post</title>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', 'Segoe UI', Arial, sans-serif; }
body { display: flex; background: #0a0a0a; color: #ffffff; height: 100vh; overflow: hidden; }

/* OVERLAY */
.overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1500; display: none; }
.overlay.active { display: block; }

/* SIDEBAR */
.sidebar {
    width: 320px; background: #121212; padding: 30px; border-right: 1px solid #1f1f1f;
    position: fixed; left: -320px; top: 0; bottom: 0; z-index: 2000; transition: 0.3s ease;
}
.sidebar.open { left: 0; }
.sidebar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; }
.logo { font-size: 42px; font-weight: 900; letter-spacing: -2px; color: white; }
.logo span { color: #0ea5e9; }

.sidebar a { display: block; padding: 14px 18px; margin-bottom: 10px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 600; }
.sidebar a:hover { color: white; background: #1a1a1a; }
.sidebar a.active { background: rgba(14,165,233,0.15); color: #0ea5e9; }

/* MAIN WRAPPER */
.main-wrapper { flex: 1; display: flex; flex-direction: column; }
.topbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #1f1f1f; }
.menu-toggle { width: 44px; height: 44px; border-radius: 8px; background: #181818; border: 1px solid #2d2d2d; color: white; cursor: pointer; }

/* CONTENT */
.content-body { padding: 40px; display: flex; justify-content: center; overflow-y: auto; flex: 1; }
.form-container { width: 100%; max-width: 700px; }
.page-title { font-size: 32px; font-weight: 800; margin-bottom: 30px; }

/* CREATE POST BOX */
.create-card { background: #121212; border: 1px solid #1f1f1f; border-radius: 24px; padding: 40px; }
.form-group { margin-bottom: 25px; }
.label { display: block; font-size: 11px; color: #4b5563; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; margin-bottom: 10px; }
.input-main { width: 100%; background: #1a1a1a; border: 1px solid #2d2d2d; padding: 16px; border-radius: 12px; color: white; font-size: 16px; outline: none; }
.input-main:focus { border-color: #0ea5e9; }
.textarea-main { height: 180px; resize: none; }

.platform-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px; }
.platform-opt { 
    background: #1a1a1a; border: 1px solid #2d2d2d; padding: 15px; border-radius: 12px; 
    text-align: center; cursor: pointer; transition: 0.2s; color: #94a3b8; font-weight: 600;
}
.platform-opt:hover { border-color: #3d3d3d; }
.platform-opt.selected { background: rgba(14,165,233,0.1); border-color: #0ea5e9; color: #0ea5e9; }

.btn-publish { 
    width: 100%; padding: 18px; background: #0ea5e9; color: white; border: none; 
    border-radius: 12px; font-weight: 800; font-size: 16px; cursor: pointer; transition: 0.2s;
}
.btn-publish:hover { background: #0284c7; transform: translateY(-2px); }
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
    <a href="/posts/create" class="active">Create Post</a>
    <a href="#">Settings</a>
    <a href="#">Profile</a>
    <a href="#">Archive</a>
</nav>

<div class="main-wrapper">
    <header class="topbar">
        <div style="display:flex; align-items:center; gap:20px;">
            <button class="menu-toggle" id="menuToggle">☰</button>
            <div class="logo">CON<span>NECT</span></div>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <span style="color:#94a3b8;">User Name</span>
            <div style="width:32px; height:32px; border-radius:50%; background:#0ea5e9;"></div>
        </div>
    </header>

    <div class="content-body">
        <div class="form-container">
            <h1 class="page-title">New Post</h1>
            
            <div class="create-card">
                <div class="form-group">
                    <span class="label">Select Platforms</span>
                    <div class="platform-grid">
                        <div class="platform-opt selected">Facebook</div>
                        <div class="platform-opt">Instagram</div>
                        <div class="platform-opt">Twitter / X</div>
                    </div>
                </div>

                <div class="form-group">
                    <span class="label">Post Content</span>
                    <textarea class="input-main textarea-main" placeholder="What's on your mind?"></textarea>
                </div>

                <div class="form-group">
                    <span class="label">Media URL (Image or Video)</span>
                    <input class="input-main" type="text" placeholder="https://...">
                </div>

                <button class="btn-publish">Schedule & Publish</button>
            </div>
        </div>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const hideToggle = document.getElementById('hideToggle');
    const overlay = document.getElementById('overlay');

    menuToggle.onclick = () => { sidebar.classList.add('open'); overlay.classList.add('active'); };
    hideToggle.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('active'); };
    overlay.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('active'); };

    // Toggle platform selection
    document.querySelectorAll('.platform-opt').forEach(opt => {
        opt.onclick = () => opt.classList.toggle('selected');
    });
</script>

</body>
</html>