<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect Dashboard</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', 'Segoe UI', Arial, sans-serif; }
        body { display: flex; background: #0a0a0a; color: #ffffff; height: 100vh; overflow: hidden; }
        .overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1500; display: none; }
        .overlay.active { display: block; }
        .sidebar { width: 320px; background: #121212; padding: 30px; border-right: 1px solid #1f1f1f; position: fixed; left: -320px; top: 0; bottom: 0; z-index: 2000; transition: 0.3s ease; }
        .sidebar.open { left: 0; }
        .sidebar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; }
        .logo { font-size: 42px; font-weight: 900; letter-spacing: -2px; color: white; }
        .logo span { color: #0ea5e9; }
        .sidebar a { display: block; padding: 14px 18px; margin-bottom: 10px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 600; }
        .sidebar a:hover { color: white; background: #1a1a1a; }
        .sidebar a.active { background: rgba(14,165,233,0.15); color: #0ea5e9; }
        .main-wrapper { flex: 1; display: flex; flex-direction: column; }
        .topbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #1f1f1f; }
        .topbar-left { display: flex; align-items: center; gap: 20px; }
        .menu-toggle { width: 44px; height: 44px; border-radius: 8px; background: #181818; border: 1px solid #2d2d2d; color: white; cursor: pointer; }
        .search { width: 320px; padding: 12px 20px; border-radius: 8px; background: #181818; border: 1px solid #2d2d2d; color: white; outline: none; }
        .content-body { display: flex; padding: 25px; gap: 25px; flex: 1; overflow-y: auto; }
        .feed { flex: 2; }
        .page-title-main { font-size: 28px; font-weight: 800; margin-bottom: 25px; }
        .card { background: #121212; padding: 30px; border-radius: 20px; border: 1px solid #1f1f1f; margin-bottom: 25px; }
        .right-panel { flex: 1; background: #121212; padding: 25px; border-radius: 20px; max-width: 340px; border: 1px solid #1f1f1f; position: sticky; top: 0; }
        .panel-section { margin-bottom: 30px; }
        .panel-title { font-size: 11px; color: #4b5563; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; margin-bottom: 15px; }
        .date-select { width: 100%; padding: 12px; background: #1a1a1a; border: 1px solid #2d2d2d; border-radius: 10px; color: white; }
        .source-label { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; color: #94a3b8; }
        .source-label input { accent-color: #0ea5e9; }
        .btn-new { display: block; width: 100%; padding: 16px; text-align: center; background: #0ea5e9; color: white; text-decoration: none; border-radius: 12px; font-weight: 700; margin-top: 10px; transition: 0.2s ease; }
        .btn-new:hover { background: #38bdf8; transform: translateY(-1px); }
        .user-menu-container { position: relative; }
        .profile-trigger { display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 8px 16px; border-radius: 50px; background: #121212; border: 1px solid #2d2d2d; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; }   /* updated to allow img */
        .avatar-placeholder { width: 32px; height: 32px; border-radius: 50%; background: #0ea5e9; }  /* fallback circle */
        .user-dropdown { position: absolute; top: 60px; right: 0; width: 220px; background: #181818; border: 1px solid #2d2d2d; border-radius: 12px; display: none; z-index: 1000; }
        .user-dropdown.show { display: block; }
        .user-dropdown a { display: flex; justify-content: space-between; padding: 14px 20px; color: #cbd5e1; text-decoration: none; }
        .user-dropdown a:hover { background: #262626; color: #7dd3fc; }

        /* Grid helpers */
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .gap-4 { gap: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        @media (min-width: 640px) {
            .sm\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (min-width: 1024px) {
            .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="overlay" id="overlay"></div>

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">CON<span>NECT</span></div>
        <button class="menu-toggle" id="hideToggle">☰</button>
    </div>

    {{-- Dynamic sidebar links --}}
    <a href="{{ route('dashboard') }}"
       class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>

    <a href="{{ route('posts.create') }}"
       class="{{ request()->routeIs('posts.create') ? 'active' : '' }}">Create Post</a>

    <a href="{{ route('settings') }}"
       class="{{ request()->routeIs('settings') ? 'active' : '' }}">Settings</a>

    <a href="{{ route('profile.edit') }}"
       class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">Profile</a>

    <a href="{{ route('posts.archive') }}"
       class="{{ request()->routeIs('posts.archive') ? 'active' : '' }}">Archive</a>

    {{-- Logout button --}}
    <form method="POST" action="{{ route('logout') }}" style="margin-top: 20px;">
        @csrf
        <button type="submit"
                style="width:100%; text-align:left; background:none; border:none; color:#f87171; font-weight:600; font-size:inherit; cursor:pointer; padding:14px 18px; border-radius:12px; font-family:inherit;">
            Logout
        </button>
    </form>
</nav>

<div class="main-wrapper">

<header class="topbar">
    <div class="topbar-left">
        <button class="menu-toggle" id="menuToggle">☰</button>
        <div class="logo">CON<span>NECT</span></div>
        <form action="{{ route('posts.index') }}" method="GET" style="display: inline;">
            <input class="search" name="search" placeholder="Search posts..." value="{{ request('search') }}">
        </form>
    </div>
    <div class="user-menu-container">
        <div class="profile-trigger" id="profileTrigger">
            <span style="color:#94a3b8;">{{ Auth::user()->name ?? 'User Name' }}</span>
            @if(Auth::user()->profile_picture)
                <img src="{{ Auth::user()->profile_picture }}" class="avatar">
            @else
                <div class="avatar-placeholder"></div>
            @endif
        </div>
        <div class="user-dropdown" id="userDropdown">
            <a href="{{ route('profile.edit') }}">Profile</a>
            <a href="{{ route('posts.drafts') }}">Drafts</a>
            <a href="{{ route('posts.archive') }}">Archive</a>
            <a href="{{ route('settings') }}">Settings</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();" style="color:#f87171;">Log-out</a>
            </form>
        </div>
    </div>
</header>

<div class="content-body">
    @yield('content')
</div>

</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuToggle = document.getElementById('menuToggle');
    const hideToggle = document.getElementById('hideToggle');
    const profileTrigger = document.getElementById('profileTrigger');
    const userDropdown = document.getElementById('userDropdown');

    menuToggle.onclick = () => { sidebar.classList.add('open'); overlay.classList.add('active'); };
    hideToggle.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('active'); };
    overlay.onclick = () => { sidebar.classList.remove('open'); overlay.classList.remove('active'); };
    profileTrigger.onclick = (e) => { e.stopPropagation(); userDropdown.classList.toggle('show'); };
    window.onclick = () => { userDropdown.classList.remove('show'); };
</script>
@stack('scripts')
</body>
</html>