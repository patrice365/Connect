<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connect Dashboard</title>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', 'Segoe UI', Arial, sans-serif; }

body { display: flex; background: #0a0a0a; color: #ffffff; height: 100vh; overflow: hidden; }

/* OVERLAYS */
.overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1500; display: none; }
.overlay.active { display: block; }

/* POST PREVIEW MODAL */
.post-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 3000;
    display: none; align-items: center; justify-content: center; backdrop-filter: blur(8px);
}
.post-overlay.active { display: flex; }
.post-modal {
    background: #121212; width: 90%; max-width: 550px; border: 1px solid #1f1f1f;
    border-radius: 24px; padding: 30px; position: relative;
}
.close-post {
    position: absolute; top: 20px; right: 20px; background: #1f1f1f; border: none;
    color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;
}

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

/* MAIN */
.main-wrapper { flex: 1; display: flex; flex-direction: column; }
.topbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #1f1f1f; }
.topbar-left { display: flex; align-items: center; gap: 20px; }
.menu-toggle { width: 44px; height: 44px; border-radius: 8px; background: #181818; border: 1px solid #2d2d2d; color: white; cursor: pointer; }
.search { width: 320px; padding: 12px 20px; border-radius: 8px; background: #181818; border: 1px solid #2d2d2d; color: white; outline: none; }

/* CONTENT */
.content-body { display: flex; padding: 25px; gap: 25px; flex: 1; overflow-y: auto; }
.feed { flex: 2; }
.page-title-main { font-size: 28px; font-weight: 800; margin-bottom: 25px; }

/* DASHBOARD CARDS */
.card { background: #121212; padding: 25px; border-radius: 20px; border: 1px solid #1f1f1f; margin-bottom: 25px; }
.analytics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px; }
.stat-label { font-size: 11px; color: #4b5563; text-transform: uppercase; font-weight: 800; letter-spacing: 1px; }
.stat-value { font-size: 28px; font-weight: 800; margin: 8px 0; }

/* ACTIVITY FEED CARDS */
.activity-card {
    background: #121212; border: 1px solid #1f1f1f; border-radius: 20px;
    padding: 20px; margin-bottom: 15px; cursor: pointer; transition: 0.2s;
}
.activity-card:hover { border-color: #0ea5e9; transform: translateY(-2px); }

.card-media {
    width: 100%; height: 160px; background: #1a1a1a; border-radius: 12px; 
    margin: 12px 0; overflow: hidden; border: 1px solid #1f1f1f;
}
.card-media img, .card-media video { width: 100%; height: 100%; object-fit: cover; }

/* RIGHT PANEL */
.right-panel { flex: 1; background: #121212; padding: 25px; border-radius: 20px; max-width: 340px; border: 1px solid #1f1f1f; position: sticky; top: 0; }
.panel-section { margin-bottom: 30px; }
.panel-title { font-size: 11px; color: #4b5563; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; margin-bottom: 15px; }
.date-select { width: 100%; padding: 12px; background: #1a1a1a; border: 1px solid #2d2d2d; border-radius: 10px; color: white; outline: none; }
.source-label { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; color: #94a3b8; }
.btn-new { display: block; width: 100%; padding: 16px; text-align: center; background: #0ea5e9; color: white; text-decoration: none; border-radius: 12px; font-weight: 700; transition: 0.2s ease; }

/* PROFILE */
.profile-trigger { display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 8px 16px; border-radius: 50px; background: #121212; border: 1px solid #2d2d2d; }
.avatar { width: 32px; height: 32px; border-radius: 50%; background: #0ea5e9; }
.user-dropdown { position: absolute; top: 60px; right: 0; width: 220px; background: #181818; border: 1px solid #2d2d2d; border-radius: 12px; display: none; z-index: 1000; }
.user-dropdown.show { display: block; }
.user-dropdown a { display: flex; justify-content: space-between; padding: 14px 20px; color: #cbd5e1; text-decoration: none; }
</style>
</head>

<body>

<div class="post-overlay" id="postOverlay">
    <div class="post-modal">
        <button class="close-post" onclick="closePost()">✕</button>
        <div style="display:flex; gap:12px; align-items:center; margin-bottom:20px;">
            <div class="avatar"></div>
            <div>
                <p id="modalUser" style="font-weight:700;">User Name</p>
                <p id="modalMeta" style="font-size:12px; color:#4b5563;">Platform • Time</p>
            </div>
        </div>
        <p id="modalBody" style="font-size:18px; line-height:1.6; color:#cbd5e1; margin-bottom:20px;"></p>
        <img id="modalImg" src="" style="width:100%; border-radius:12px; display:none; border: 1px solid #1f1f1f; margin-bottom:10px;">
        <video id="modalVid" controls style="width:100%; border-radius:12px; display:none; border: 1px solid #1f1f1f; margin-bottom:10px;"></video>
    </div>
</div>

<div class="overlay" id="overlay"></div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">CON<span>NECT</span></div>
        <button class="menu-toggle" id="hideToggle">☰</button>
    </div>
    <a href="/">Home</a>
    <a href="/dashboard" class="active">Dashboard</a>
    <a href="/feed">Activity Feed</a>
    <a href="/posts/create">Create Post</a>
    <a href="#">Settings</a>
    <a href="#">Profile</a>
    <a href="#">Archive</a>
</nav>

<div class="main-wrapper">
<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" id="menuToggle">☰</button>
        <div class="logo">CON<span>NECT</span></div>
        <input class="search" placeholder="Search...">
    </div>
    <div class="user-menu-container" style="position:relative;">
        <div class="profile-trigger" id="profileTrigger">
            <span style="color:#94a3b8;">User Name</span>
            <div class="avatar"></div>
        </div>
        <div class="user-dropdown" id="userDropdown">
            <a href="#">Profile</a>
            <a href="#">Drafts <span>4</span></a>
            <a href="#">Archive</a>
            <a href="#">Settings</a>
            <a href="/" style="color:#f87171;">Log-out</a>
        </div>
    </div>
</header>

<div class="content-body">
<section class="feed">
    <h1 class="page-title-main">Dashboard</h1>

    <div class="analytics-grid">
        <div class="card">
            <span class="stat-label">Summary Analytics</span>
            <p class="stat-value">29,385</p>
            <div style="height:2px; background:linear-gradient(90deg, #0ea5e9 40%, transparent 100%);"></div>
        </div>
        <div class="card">
            <span class="stat-label">Total Engagement</span>
            <p class="stat-value">11.33K</p>
            <div style="display:flex; gap:3px; align-items:flex-end; height:20px;">
                <div style="height:40%; width:4px; background:#0ea5e9;"></div>
                <div style="height:70%; width:4px; background:#0ea5e9;"></div>
                <div style="height:50%; width:4px; background:#0ea5e9;"></div>
                <div style="height:90%; width:4px; background:#0ea5e9;"></div>
            </div>
        </div>
        <div class="card">
            <span class="stat-label">Follower Growth</span>
            <p class="stat-value">+2.4K</p>
            <span style="font-size:11px; color:#0ea5e9;">↑ 12.5% this month</span>
        </div>
    </div>

    <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 20px;">Recent Activity</h2>

    <div class="activity-card" onclick="openPost('User Name', 'YouTube • 1h ago', 'Check out the new channel intro!', null, 'https://www.w3schools.com/html/mov_bbb.mp4')">
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <div style="display:flex; gap:10px; align-items:center;">
                <div class="avatar" style="width:24px; height:24px; background:#ff0000;"></div>
                <span style="font-size:13px; font-weight:700;">User Name</span>
            </div>
            <span style="color:#ff0000; font-weight:900;">y</span>
        </div>
        <p style="font-size:14px; color:#cbd5e1; line-height: 1.5;">Check out the new channel intro!</p>
        <div class="card-media">
            <video muted loop autoplay style="pointer-events: none;">
                <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">
            </video>
        </div>
    </div>

    <div class="activity-card" onclick="openPost('User Name', 'Facebook • 2h ago', 'New UI update is live!', 'https://images.unsplash.com/photo-1551288049-bbbda5366a7a?auto=format&fit=crop&q=80&w=1000')">
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <div style="display:flex; gap:10px; align-items:center;">
                <div class="avatar" style="width:24px; height:24px;"></div>
                <span style="font-size:13px; font-weight:700;">User Name</span>
            </div>
            <span style="color:#0ea5e9; font-weight:900;">f</span>
        </div>
        <p style="font-size:14px; color:#cbd5e1; line-height: 1.5;">New UI update is live!</p>
        <div class="card-media">
            <img src="https://images.unsplash.com/photo-1551288049-bbbda5366a7a?auto=format&fit=crop&q=80&w=1000">
        </div>
    </div>
</section>

<aside class="right-panel">
    <div class="panel-section">
        <h4 class="panel-title">Date Filter</h4>
        <select class="date-select">
            <option>Past 3 hours</option>
            <option>Past 24 hours</option>
            <option>Past 3 days</option>
            <option>Past 7 days</option>
            <option>Past 14 days</option>
            <option>Past 30 days</option>
        </select>
    </div>
    <div class="panel-section">
        <h4 class="panel-title">Sources</h4>
        <label class="source-label"><input type="checkbox" checked> Facebook</label>
        <label class="source-label"><input type="checkbox" checked> Instagram</label>
        <label class="source-label"><input type="checkbox" checked> Twitter / X</label>
        <label class="source-label"><input type="checkbox" checked> YouTube</label>
        <label class="source-label"><input type="checkbox" checked> Reddit</label>
        <label class="source-label"><input type="checkbox" checked> Spotify</label>
    </div>
    <a href="/posts/create" class="btn-new">+ Create Post</a>
</aside>
</div>
</div>

<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
const menuToggle = document.getElementById('menuToggle');
const hideToggle = document.getElementById('hideToggle');
const profileTrigger = document.getElementById('profileTrigger');
const userDropdown = document.getElementById('userDropdown');
const postOverlay = document.getElementById('postOverlay');
const modalImg = document.getElementById('modalImg');
const modalVid = document.getElementById('modalVid');

menuToggle.onclick = () => { sidebar.classList.add('open'); overlay.classList.add('active'); };
hideToggle.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('active'); };
overlay.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('active'); };

function openPost(user, meta, body, image = null, video = null) {
    document.getElementById('modalUser').innerText = user;
    document.getElementById('modalMeta').innerText = meta;
    document.getElementById('modalBody').innerText = body;
    
    if (image) {
        modalImg.src = image;
        modalImg.style.display = 'block';
    } else {
        modalImg.style.display = 'none';
        modalImg.src = '';
    }

    if (video) {
        modalVid.src = video;
        modalVid.style.display = 'block';
        modalVid.play();
    } else {
        modalVid.pause();
        modalVid.style.display = 'none';
        modalVid.src = '';
    }
    
    postOverlay.classList.add('active');
}

function closePost() { 
    postOverlay.classList.remove('active'); 
    modalVid.pause();
}

postOverlay.onclick = (e) => { if (e.target === postOverlay) closePost(); };

profileTrigger.onclick = (e) => { e.stopPropagation(); userDropdown.classList.toggle('show'); };
window.onclick = () => { userDropdown.classList.remove('show'); };
</script>

</body>
</html>