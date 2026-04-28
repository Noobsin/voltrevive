<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>VoltRevive &mdash; @yield('title')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  @vite(['resources/css/app.css'])

  @auth
    @php
      $authUser    = auth()->user();
      $authRole    = $authUser->isAdmin() ? 'admin' : ($authUser->isTechnician() ? 'technician' : 'collector');
      $authName    = $authUser->name;
      $authInitial = $authUser->initial();
    @endphp
    <meta name="user-role"    content="{{ $authRole }}"/>
    <meta name="user-name"    content="{{ $authName }}"/>
    <meta name="user-initial" content="{{ $authInitial }}"/>
  @endauth

  {{--
    ROLE SYNC: runs in <head> BEFORE any page script.
    Writes the server-determined role into localStorage so ALL existing
    page scripts (repair-wall, home, dashboards) that read
    localStorage.getItem('vr_role') continue to work without changes.
    On logout: serverRole is empty -> localStorage is cleared.
  --}}
  <script>
    (function() {
      var serverRole = '{{ auth()->check() ? (auth()->user()->isAdmin() ? "admin" : (auth()->user()->isTechnician() ? "technician" : "collector")) : "" }}';
      if (serverRole) {
        localStorage.setItem('vr_role', serverRole);
      } else {
        localStorage.removeItem('vr_role');
      }
    })();
  </script>

  <style>
    :root {
      --bg:#161310; --bg-card:#1f1b16; --bg-card2:#2a2318;
      --amber:#d4891a; --amber-lt:#e8a830;
      --cream:#f0e8d8; --muted:#8a7d6a; --border:#332b1f;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: var(--bg); color: var(--cream); font-family: 'DM Sans', sans-serif; min-height: 100vh; }
    body::before {
      content: ''; position: fixed; inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
      pointer-events: none; z-index: 0; opacity: 0.4;
    }
    h1,h2,h3,h4 { font-family: 'Playfair Display', serif; }
    nav#main-nav {
      position: sticky; top: 0; z-index: 100;
      background: rgba(22,19,16,0.92); backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border);
      padding: 0 2rem; display: flex; align-items: center;
      justify-content: space-between; height: 64px;
    }
    .nav-logo { font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:900; color:var(--amber-lt); letter-spacing:-0.03em; text-decoration:none; }
    .nav-logo span { color:var(--cream); }
    .nav-links { display:flex; gap:1.75rem; align-items:center; }
    .nav-link { color:var(--muted); text-decoration:none; font-size:0.88rem; font-weight:500; transition:color 0.15s; white-space:nowrap; display:flex; align-items:center; gap:0.3rem; }
    .nav-link:hover { color:var(--cream); }
    .nav-link.active { color:var(--amber-lt); }
    .nav-right { display:flex; align-items:center; gap:0.75rem; position:relative; }
    .join-dropdown-wrap { position:relative; }
    .btn-join { background:var(--amber); color:#161310; padding:0.45rem 1.15rem; border-radius:7px; font-weight:700; font-size:0.85rem; border:none; cursor:pointer; font-family:'DM Sans',sans-serif; display:flex; align-items:center; gap:0.4rem; transition:background 0.15s; }
    .btn-join:hover { background:var(--amber-lt); }
    .join-dropdown { display:none; position:absolute; right:0; top:calc(100% + 8px); background:var(--bg-card); border:1px solid var(--border); border-radius:10px; overflow:hidden; min-width:190px; box-shadow:0 12px 40px rgba(0,0,0,0.5); z-index:200; }
    .join-dropdown.open { display:block; animation:ddFade 0.15s ease; }
    @keyframes ddFade { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
    .join-opt { display:flex; align-items:center; gap:0.75rem; padding:0.85rem 1.1rem; cursor:pointer; transition:background 0.15s; border:none; background:transparent; color:var(--cream); width:100%; text-align:left; font-family:'DM Sans',sans-serif; font-size:0.88rem; text-decoration:none; }
    .join-opt:hover { background:var(--bg-card2); }
    .join-opt-icon { font-size:1.1rem; }
    .join-opt-label { font-weight:600; display:block; }
    .join-opt-sub { font-size:0.72rem; color:var(--muted); display:block; margin-top:0.1rem; }
    .avatar-wrap { position:relative; }
    .nav-avatar { width:36px; height:36px; border-radius:50%; border:2px solid var(--amber); cursor:pointer; display:flex; align-items:center; justify-content:center; font-family:'Playfair Display',serif; font-weight:900; font-size:0.95rem; color:var(--amber-lt); transition:all 0.15s; }
    .nav-avatar:hover { border-color:var(--amber-lt); }
    .avatar-dropdown { display:none; position:absolute; right:0; top:calc(100% + 8px); background:var(--bg-card); border:1px solid var(--border); border-radius:10px; overflow:hidden; min-width:200px; box-shadow:0 12px 40px rgba(0,0,0,0.5); z-index:200; }
    .avatar-dropdown.open { display:block; animation:ddFade 0.15s ease; }
    .av-header { padding:0.85rem 1.1rem; border-bottom:1px solid var(--border); background:var(--bg-card2); }
    .av-name { font-size:0.88rem; font-weight:700; }
    .av-role { font-size:0.72rem; color:var(--amber); text-transform:uppercase; letter-spacing:0.06em; font-weight:700; margin-top:0.1rem; }
    .av-opt { display:flex; align-items:center; gap:0.65rem; padding:0.7rem 1.1rem; cursor:pointer; transition:background 0.15s; border:none; background:transparent; color:var(--cream); width:100%; text-align:left; font-family:'DM Sans',sans-serif; font-size:0.85rem; }
    .av-opt:hover { background:var(--bg-card2); }
    .av-opt.danger { color:#f09090; border-top:1px solid var(--border); }
    .av-opt.danger:hover { background:rgba(200,60,60,0.08); }
    .btn-list-svc { background:var(--amber); color:#161310; padding:0.45rem 1.15rem; border-radius:7px; font-weight:700; font-size:0.85rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem; transition:background 0.15s; }
    .btn-list-svc:hover { background:var(--amber-lt); }
    #logout-form { display:none; }
  </style>
  @yield('styles')
</head>
<body>

<form id="logout-form" method="POST" action="/logout">
  @csrf
</form>

<nav id="main-nav">
  <a href="/" class="nav-logo">Volt<span>Revive</span></a>

  <div class="nav-links">
    <a href="/browse"      class="nav-link">Browse</a>
    <a href="/comparison"  class="nav-link" style="display:flex;align-items:center;gap:0.3rem;">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      Compare
    </a>
    <a href="/repair-wall" class="nav-link">Repair Wall</a>
    <a href="/events"      class="nav-link">Events</a>
    @guest
      <a href="/contact" class="nav-link">Contact</a>
    @endguest
  </div>

  <div class="nav-right">

    @guest
      <div class="join-dropdown-wrap">
        <button class="btn-join" onclick="toggleJoinDd()">
          Join VoltRevive
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="join-dropdown" id="join-dropdown">
          <a href="/register" class="join-opt">
            <span class="join-opt-icon">&#x1F3AE;</span>
            <span>
              <span class="join-opt-label">Join as Collector</span>
              <span class="join-opt-sub">Browse &amp; book restoration services</span>
            </span>
          </a>
          <a href="/register?role=technician" class="join-opt">
            <span class="join-opt-icon">&#x1F527;</span>
            <span>
              <span class="join-opt-label">Join as Technician</span>
              <span class="join-opt-sub">Register first, apply after</span>
            </span>
          </a>
        </div>
      </div>
    @endguest

    @auth
      @if($authRole === 'collector')
        <a href="/collector-portfolio" class="nav-link">&#x1F5BC;&#xFE0F; My Portfolio</a>
        <a href="/my-devices"          class="nav-link">&#x1F4E6; My Devices</a>
        <a href="/collector-dashboard" class="nav-link">My Dashboard</a>
        <div class="avatar-wrap">
          <div class="nav-avatar" onclick="toggleAvatarDd('coll-dd')">{{ $authInitial }}</div>
          <div class="avatar-dropdown" id="coll-dd">
            <div class="av-header">
              <div class="av-name">{{ $authName }}</div>
              <div class="av-role">Collector</div>
            </div>
            <button class="av-opt" onclick="window.location='/collector-portfolio'">&#x1F5BC;&#xFE0F; My Portfolio</button>
            <button class="av-opt" onclick="window.location='/collector-dashboard'">&#x1F4CA; My Dashboard</button>
            <button class="av-opt" onclick="window.location='/my-devices'">&#x1F4E6; My Devices</button>
            <button class="av-opt danger" onclick="signOut()">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Sign Out
            </button>
          </div>
        </div>
      @endif

      @if($authRole === 'technician')
        <a href="/services/create" class="btn-list-svc">+ List Service</a>
        <div class="avatar-wrap">
          <div class="nav-avatar" onclick="toggleAvatarDd('tech-dd')"
               style="background:linear-gradient(135deg,#0e1a10,#122014);color:#5de0b0;border-color:#5de0b0;">{{ $authInitial }}</div>
          <div class="avatar-dropdown" id="tech-dd">
            <div class="av-header">
              <div class="av-name">{{ $authName }}</div>
              <div class="av-role" style="color:#5de0b0;">Technician</div>
            </div>
            <button class="av-opt" onclick="window.location='/technician-portfolio'">&#x1F5BC;&#xFE0F; My Portfolio</button>
            <button class="av-opt" onclick="window.location='/technician-dashboard'">&#x1F4CA; Dashboard</button>
            <button class="av-opt danger" onclick="signOut()">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Sign Out
            </button>
          </div>
        </div>
      @endif

      @if($authRole === 'admin')
        <a href="/admin" class="nav-link">&#x2699;&#xFE0F; Admin Panel</a>
        <div class="avatar-wrap">
          <div class="nav-avatar" onclick="toggleAvatarDd('admin-dd')"
               style="background:linear-gradient(135deg,#1a0e10,#2a1015);color:#f09090;border-color:#f09090;">{{ $authInitial }}</div>
          <div class="avatar-dropdown" id="admin-dd">
            <div class="av-header">
              <div class="av-name">{{ $authName }}</div>
              <div class="av-role" style="color:#f09090;">Admin</div>
            </div>
            <button class="av-opt" onclick="window.location='/admin'">&#x2699;&#xFE0F; Admin Panel</button>
            <button class="av-opt danger" onclick="signOut()">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Sign Out
            </button>
          </div>
        </div>
      @endif
    @endauth

  </div>
</nav>

@yield('content')

<script>
  function getRole() {
    var meta = document.querySelector('meta[name="user-role"]');
    return meta ? meta.content : null;
  }
  function toggleJoinDd() {
    var dd = document.getElementById('join-dropdown');
    if (dd) dd.classList.toggle('open');
  }
  function toggleAvatarDd(id) {
    var dd = document.getElementById(id);
    if (dd) dd.classList.toggle('open');
  }
  function signOut() {
    document.getElementById('logout-form').submit();
  }
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.join-dropdown-wrap')) {
      var dd = document.getElementById('join-dropdown');
      if (dd) dd.classList.remove('open');
    }
    if (!e.target.closest('.avatar-wrap')) {
      document.querySelectorAll('.avatar-dropdown').forEach(function(d) { d.classList.remove('open'); });
    }
  });
</script>
</body>
</html>