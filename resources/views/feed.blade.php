<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connect - Activity Feed</title>

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
.content-body { display: flex; padding: 25px; gap: 25px; flex: 1; overflow-y: auto; }
.feed-column { flex: 2; }
.page-title-main { font-size: 28px; font-weight: 800; margin-bottom: 25px; }

/* FEED GRID & CARDS */
.feed-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
.post-card { 
    background: #121212; border: 1px solid #1f1f1f; border-radius: 20px; padding: 20px; 
    cursor: pointer; transition: 0.2s; 
}
.post-card:hover { border-color: #3d3d3d; transform: translateY(-2px); }

/* Media Container for Feed Cards */
.post-media {
    width: 100%; height: 180px; background: #1a1a1a; border-radius: 12px; 
    margin-bottom: 15px; border: 1px solid #262626; overflow: hidden;
}
.post-media img, .post-media video { width: 100%; height: 100%; object-fit: cover; }

.post-header { display: flex; justify-content: space-between; margin-bottom: 15px; }
.user-info { display: flex; gap: 12px; align-items: center; }
.avatar-circle { width: 36px; height: 36px; border-radius: 50%; background: #0ea5e9; }
.post-text { font-size: 14px; color: #cbd5e1; line-height: 1.5; margin-bottom: 15px; }

.post-stats { display: flex; justify-content: space-between; padding-top: 15px; border-top: 1px solid #1f1f1f; }
.stat-label { font-size: 10px; color: #4b5563; text-transform: uppercase; font-weight: 800; }
.stat-val { font-size: 14px; font-weight: 700; color: #fff; }

/* RIGHT PANEL */
.right-panel { flex: 1; background: #121212; padding: 25px; border-radius: 20px; max-width: 340px; border: 1px solid #1f1f1f; position: sticky; top: 0; }
.panel-section { margin-bottom: 30px; }
.panel-title { font-size: 11px; color: #4b5563; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; margin-bottom: 15px; }
.date-select { width: 100%; padding: 12px; background: #1a1a1a; border: 1px solid #2d2d2d; border-radius: 10px; color: white; outline: none; }
.source-label { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; color: #94a3b8; font-size: 14px; }

/* MODAL / DETAIL VIEW */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.8); display: none; justify-content: flex-end; z-index: 3000; }
.modal-overlay.active { display: flex; }
.modal-content { 
    width: 480px; height: 100%; background: #121212; border-left: 1px solid #1f1f1f; 
    padding: 40px; animation: slideIn 0.3s ease-out; display: flex; flex-direction: column;
}
@keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }

.btn-new { display: block; width: 100%; padding: 16px; text-align: center; background: #0ea5e9; color: white; text-decoration: none; border-radius: 12px; font-weight: 700; }

/* COMMENTS STYLES */
.comments-section {
    flex: 1; overflow-y: auto; margin-top: 20px; padding-right: 5px;
}
.comment-item {
    background: #181818; padding: 15px; border-radius: 12px; margin-bottom: 12px; border: 1px solid #262626;
}
.comment-user { display: flex; gap: 10px; align-items: center; margin-bottom: 8px; }
.comment-avatar { width: 24px; height: 24px; border-radius: 50%; background: #4b5563; }
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
    <a href="/feed" class="active">Activity Feed</a>
    <a href="/posts/create">Create Post</a>
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
            <div class="avatar-circle" style="width:32px; height:32px;"></div>
        </div>
    </header>

    <div class="content-body">
        <section class="feed-column">
            <h1 class="page-title-main">Activity Feed</h1>

            <div class="feed-grid">
                <div class="post-card" onclick="openPostDetail('Facebook Post', 'Engagement is spiking on the latest trends post! 🚀', '1,248', '8.4%', 'https://images.unsplash.com/photo-1551288049-bbbda5366a7a?auto=format&fit=crop&q=80&w=1000', null)">
                    <div class="post-header">
                        <div class="user-info">
                            <div class="avatar-circle"></div>
                            <div><p style="font-size:13px; font-weight:600;">User Name</p><p style="font-size:11px; color:#4b5563;">2h ago</p></div>
                        </div>
                        <span style="color:#0ea5e9; font-weight:900;">f</span>
                    </div>
                    <p class="post-text">Check out the latest engagement trends for this week!</p>
                    <div class="post-media">
                        <img src="https://images.unsplash.com/photo-1551288049-bbbda5366a7a?auto=format&fit=crop&q=80&w=1000">
                    </div>
                    <div class="post-stats">
                        <div class="stat-box"><span class="stat-label">Reach</span><span class="stat-val">12.4k</span></div>
                        <div class="stat-box"><span class="stat-label">Growth</span><span class="stat-val" style="color:#0ea5e9;">+5.2%</span></div>
                    </div>
                </div>

                <div class="post-card" onclick="openPostDetail('YouTube Update', 'New intro sequence finalized.', '952', '12.1%', null, 'https://www.w3schools.com/html/mov_bbb.mp4')">
                    <div class="post-header">
                        <div class="user-info">
                            <div class="avatar-circle" style="background:#ff0000;"></div>
                            <div><p style="font-size:13px; font-weight:600;">User Name</p><p style="font-size:11px; color:#4b5563;">5h ago</p></div>
                        </div>
                        <span style="color:#ff0000; font-weight:900;">y</span>
                    </div>
                    <p class="post-text">"The best way to predict the future is to create it." - UI Update incoming.</p>
                    <div class="post-media">
                        <video muted loop autoplay><source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4"></video>
                    </div>
                    <div class="post-stats">
                        <div class="stat-box"><span class="stat-label">Reach</span><span class="stat-val">8.1k</span></div>
                        <div class="stat-box"><span class="stat-label">Growth</span><span class="stat-val" style="color:#0ea5e9;">+12%</span></div>
                    </div>
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

<div class="modal-overlay" id="postModal">
    <div class="modal-content">
        <div style="display:flex; justify-content:space-between; margin-bottom:25px;">
            <button id="closeModal" style="background:none; border:none; color:#94a3b8; cursor:pointer; font-weight:700;">✕ CLOSE</button>
            <span style="color:#0ea5e9; font-size:11px; font-weight:800; text-transform:uppercase;">Post Analysis</span>
        </div>
        
        <h2 id="modalTitle" style="margin-bottom:10px;">Post Breakdown</h2>
        <p id="modalText" style="color:#94a3b8; font-size:14px; line-height:1.6; margin-bottom:20px;"></p>
        
        <img id="modalImg" src="" style="width:100%; border-radius:12px; display:none; border: 1px solid #1f1f1f; margin-bottom:20px;">
        <video id="modalVid" controls style="width:100%; border-radius:12px; display:none; border: 1px solid #1f1f1f; margin-bottom:20px;"></video>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px;">
            <div style="background:#181818; padding:15px; border-radius:12px; border:1px solid #262626; text-align:center;">
                <span class="stat-label">Total Likes</span>
                <p id="modalLikes" style="font-size:22px; margin-top:5px; color:#e11d48; font-weight:800;">❤️ 0</p>
            </div>
            <div style="background:#181818; padding:15px; border-radius:12px; border:1px solid #262626; text-align:center;">
                <span class="stat-label">Eng. Rate</span>
                <p id="modalEngRate" style="font-size:22px; margin-top:5px; font-weight:800; color:#0ea5e9;">0%</p>
            </div>
        </div>

        <span class="stat-label">Recent Comments</span>
        <div class="comments-section">
            <div class="comment-item">
                <div class="comment-user">
                    <div class="comment-avatar"></div>
                    <span style="font-size:12px; font-weight:700;">Sarah Jenkins</span>
                    <span style="font-size:10px; color:#4b5563;">• 1h ago</span>
                </div>
                <p style="font-size:13px; color:#cbd5e1; line-height:1.4;">This is exactly what I was looking for! Great insights.</p>
            </div>
        </div>

        <div style="margin-top:20px; border-top: 1px solid #1f1f1f; padding-top:20px;">
            <button class="btn-new" style="background:transparent; border:1px solid #f87171; color:#f87171; padding:12px;">Archive Post</button>
        </div>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const hideToggle = document.getElementById('hideToggle');
    const overlay = document.getElementById('overlay');
    const postModal = document.getElementById('postModal');
    const modalImg = document.getElementById('modalImg');
    const modalVid = document.getElementById('modalVid');

    menuToggle.onclick = () => { sidebar.classList.add('open'); overlay.classList.add('active'); };
    hideToggle.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('active'); };
    
    overlay.onclick = () => { 
        sidebar.classList.remove('open'); 
        overlay.classList.remove('active'); 
        closeModalFunc();
    };

    function openPostDetail(title, text, likes, rate, imgSrc = null, vidSrc = null) {
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalText').innerText = text;
        document.getElementById('modalLikes').innerText = "❤️ " + likes;
        document.getElementById('modalEngRate').innerText = rate;
        
        if(imgSrc) {
            modalImg.src = imgSrc;
            modalImg.style.display = 'block';
        } else {
            modalImg.style.display = 'none';
        }

        if(vidSrc) {
            modalVid.src = vidSrc;
            modalVid.style.display = 'block';
            modalVid.play();
        } else {
            modalVid.style.display = 'none';
        }

        postModal.classList.add('active');
        overlay.classList.add('active');
    }

    function closeModalFunc() {
        postModal.classList.remove('active');
        modalVid.pause();
        if(!sidebar.classList.contains('open')) overlay.classList.remove('active');
    }

    document.getElementById('closeModal').onclick = closeModalFunc;

    postModal.onclick = (e) => {
        if (e.target === postModal) {
            closeModalFunc();
        }
    };
</script>

</body>
</html>