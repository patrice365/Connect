<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Post | Connect</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
}

body {
    display: flex;
    background: #0a0a0a;
    color: #ffffff;
    height: 100vh;
    overflow: hidden;
}

/* OVERLAY - Fixed to ensure it doesn't block clicks when hidden */
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.7);
    z-index: 1500;
    display: none;
    pointer-events: none; /* Prevents blocking clicks when display: none */
}
.overlay.active { 
    display: block; 
    pointer-events: auto;
}

/* SIDEBAR - Identical to Dashboard */
.sidebar {
    width: 320px;
    background: #121212;
    padding: 30px;
    border-right: 1px solid #1f1f1f;
    position: fixed;
    left: -320px;
    top: 0;
    bottom: 0;
    z-index: 2000;
    transition: 0.3s ease;
}

.sidebar.open { left: 0; }

.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 50px;
}

.logo {
    font-size: 42px;
    font-weight: 900;
    letter-spacing: -2px;
    color: white;
    text-decoration: none;
}

.logo span {
    color: #0ea5e9;
}

.sidebar a {
    display: block;
    padding: 14px 18px;
    margin-bottom: 10px;
    color: #94a3b8;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: 0.2s;
}

.sidebar a:hover {
    color: white;
    background: #1a1a1a;
}

.sidebar a.active {
    background: rgba(14,165,233,0.15);
    color: #0ea5e9;
}

/* MAIN WRAPPER */
.main-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    margin-left: 0; /* Adjusted for mobile-first sidebar logic */
}

/* TOPBAR */
.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #1f1f1f;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.menu-toggle {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    background: #181818;
    border: 1px solid #2d2d2d;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* CONTENT BODY */
.content-body {
    padding: 40px;
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.composer-container {
    width: 100%;
    max-width: 800px;
}

.page-title-main {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 30px;
    letter-spacing: -1px;
}

.card {
    background: #121212;
    padding: 30px;
    border-radius: 20px;
    border: 1px solid #1f1f1f;
}

textarea {
    width: 100%;
    height: 250px;
    background: #1a1a1a;
    border: 1px solid #2d2d2d;
    border-radius: 15px;
    padding: 20px;
    color: white;
    font-size: 16px;
    line-height: 1.6;
    resize: none;
    outline: none;
    margin-bottom: 20px;
    transition: 0.3s;
}

textarea:focus {
    border-color: #0ea5e9;
    background: #1e1e1e;
}

.btn-publish {
    background: #0ea5e9;
    color: white;
    padding: 16px 32px;
    border-radius: 12px;
    border: none;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: 0.2s;
}

.btn-publish:hover {
    background: #38bdf8;
    transform: translateY(-2px);
}

/* PROFILE TRIGGER */
.profile-trigger {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 8px 16px;
    border-radius: 50px;
    background: #121212;
    border: 1px solid #2d2d2d;
}

.avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0ea5e9;
}
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
    <a href="/posts/create" class="active">Create Post</a>
    <a href="#">Settings</a>
    <a href="#">Profile</a>
    <a href="#">Archive</a>
</nav>

<div class="main-wrapper">

<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" id="menuToggle">☰</button>
        <div class="logo">CON<span>NECT</span></div>
    </div>

    <div class="profile-trigger">
        <span style="color:#94a3b8;">User Name</span>
        <div class="avatar"></div>
    </div>
</header>

<div class="content-body">
    <div class="composer-container">
        <h1 class="page-title-main">Create Post</h1>

        <div class="card">
            <form action="/posts" method="POST">
                <textarea placeholder="What would you like to share?"></textarea>
                
                <div style="display: flex; justify-content: flex-end; gap: 15px; align-items: center;">
                    <span style="color: #4b5563; font-size: 14px;">Draft saved just now</span>
                    <button type="submit" class="btn-publish">Publish Post</button>
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

// Toggle Sidebar Open
menuToggle.onclick = (e) => {
    e.stopPropagation();
    sidebar.classList.add('open');
    overlay.classList.add('active');
};

// Toggle Sidebar Close
hideToggle.onclick = () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
};

// Close when clicking outside on overlay
overlay.onclick = () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
};

// Prevent closing when clicking inside the sidebar
sidebar.onclick = (e) => {
    e.stopPropagation();
};
</script>

</body>
</html>