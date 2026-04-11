<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>VoltRevive — Admin Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --bg:#161310; --bg-card:#1f1b16; --bg-card2:#2a2318;
      --amber:#d4891a; --amber-lt:#e8a830;
      --cream:#f0e8d8; --muted:#8a7d6a; --border:#332b1f;
    }
    * { box-sizing:border-box; margin:0; padding:0; }
    body {
      background:var(--bg); color:var(--cream);
      font-family:'DM Sans',sans-serif; min-height:100vh;
      display:flex; flex-direction:column; align-items:center; justify-content:center;
      padding:2rem;
    }
    body::before {
      content:''; position:fixed; inset:0;
      background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
      pointer-events:none; z-index:0; opacity:0.4;
    }
    .login-card {
      background:var(--bg-card); border:1px solid var(--border);
      border-radius:20px; width:100%; max-width:420px;
      overflow:hidden; position:relative; z-index:1;
      animation:fadeUp 0.4s ease;
    }
    .login-card::before {
      content:''; display:block; height:3px;
      background:linear-gradient(90deg, var(--amber), var(--amber-lt), var(--amber));
    }
    .login-header {
      padding:2rem 2rem 1.5rem; text-align:center;
      border-bottom:1px solid var(--border);
      background:var(--bg-card2);
    }
    .login-logo { font-family:'Playfair Display',serif; font-size:1.6rem; font-weight:900; letter-spacing:-0.03em; margin-bottom:0.5rem; }
    .login-logo span { color:var(--cream); }
    .admin-badge {
      display:inline-flex; align-items:center; gap:0.4rem;
      background:rgba(212,137,26,0.12); border:1px solid rgba(212,137,26,0.3);
      color:var(--amber-lt); padding:0.3rem 0.9rem; border-radius:20px;
      font-size:0.75rem; font-weight:700; letter-spacing:0.06em;
      text-transform:uppercase; margin-bottom:1rem;
    }
    .login-header h1 { font-family:'Playfair Display',serif; font-size:1.4rem; font-weight:700; margin-bottom:0.3rem; }
    .login-header p { font-size:0.82rem; color:var(--muted); }
    .login-body { padding:1.75rem 2rem 2rem; }
    .form-group { margin-bottom:1.1rem; }
    .form-label { display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.45rem; color:var(--cream); }
    .form-input-wrap { position:relative; }
    .form-input {
      width:100%; background:var(--bg); border:1px solid var(--border);
      border-radius:9px; color:var(--cream); font-family:'DM Sans',sans-serif;
      font-size:0.95rem; padding:0.8rem 1rem 0.8rem 2.8rem;
      outline:none; transition:border-color 0.2s; appearance:none;
    }
    .form-input:focus { border-color:var(--amber); }
    .form-input::placeholder { color:var(--muted); }
    .form-input.is-invalid { border-color:#f09090; }
    .input-icon { position:absolute; left:0.9rem; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none; display:flex; align-items:center; }
    .toggle-pw { position:absolute; right:0.9rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--muted); cursor:pointer; display:flex; align-items:center; transition:color 0.2s; padding:0; }
    .toggle-pw:hover { color:var(--amber-lt); }
    .field-error { font-size:0.75rem; color:#f09090; margin-top:0.35rem; }
    .alert-error { background:rgba(138,42,42,0.15); border:1px solid rgba(200,80,80,0.3); border-radius:8px; padding:0.75rem 1rem; font-size:0.83rem; color:#f0a0a0; margin-bottom:1.25rem; }
    .btn-login {
      width:100%; background:var(--amber); border:none; color:#161310;
      padding:0.85rem; border-radius:9px; font-weight:700;
      font-size:0.95rem; cursor:pointer; font-family:'DM Sans',sans-serif;
      transition:background 0.2s; margin-top:0.5rem;
      display:flex; align-items:center; justify-content:center; gap:0.5rem;
    }
    .btn-login:hover { background:var(--amber-lt); }
    .login-footer { padding:1rem 2rem 1.5rem; border-top:1px solid var(--border); background:var(--bg-card2); text-align:center; font-size:0.78rem; color:var(--muted); line-height:1.6; }
    .login-footer a { color:var(--amber-lt); text-decoration:none; }
    .login-footer a:hover { text-decoration:underline; }
    .security-note { display:flex; align-items:flex-start; gap:0.6rem; background:rgba(212,137,26,0.06); border:1px solid rgba(212,137,26,0.15); border-radius:8px; padding:0.75rem 1rem; margin-bottom:1.25rem; font-size:0.78rem; color:var(--muted); line-height:1.55; }
    .security-note svg { flex-shrink:0; margin-top:0.1rem; color:var(--amber); }
    .back-link { display:inline-flex; align-items:center; gap:0.4rem; color:var(--muted); text-decoration:none; font-size:0.82rem; margin-bottom:1.75rem; transition:color 0.2s; position:relative; z-index:1; }
    .back-link:hover { color:var(--cream); }
    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
  </style>
</head>
<body>

  <a href="/" class="back-link">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    Back to VoltRevive
  </a>

  <div class="login-card">

    <div class="login-header">
      <div class="login-logo">Volt<span>Revive</span></div>
      <div class="admin-badge">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Admin Portal
      </div>
      <h1>Administrator Login</h1>
      <p>Restricted access — authorised personnel only</p>
    </div>

    <div class="login-body">

      <div class="security-note">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        This page is for platform administrators only. Regular users should use the <a href="/login" style="color:var(--amber-lt);">standard login page</a>.
      </div>

      {{-- Server-side errors --}}
      @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
      @endif

      {{-- Real form — POST to AdminAuthController@login --}}
      <form method="POST" action="/admin/login">
        @csrf

        <div class="form-group">
          <label class="form-label" for="admin-email">Admin Email</label>
          <div class="form-input-wrap">
            <span class="input-icon">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </span>
            <input type="email" id="admin-email" name="email"
                   class="form-input @error('email') is-invalid @enderror"
                   placeholder="admin@voltrevive.com"
                   value="{{ old('email') }}"
                   autocomplete="email" required/>
          </div>
          @error('email')<div class="field-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="admin-password">Password</label>
          <div class="form-input-wrap">
            <span class="input-icon">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </span>
            <input type="password" id="admin-password" name="password"
                   class="form-input"
                   placeholder="Enter admin password"
                   autocomplete="current-password" required/>
            <button type="button" class="toggle-pw" onclick="togglePassword()" aria-label="Show password">
              <svg id="pw-eye" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-login">
          Sign In to Admin Panel
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </button>

      </form>
    </div>

    <div class="login-footer">
      Lost access? Contact your platform administrator.<br/>
      <a href="mailto:support@voltrevive.com">support@voltrevive.com</a>
    </div>

  </div>

<script>
  function togglePassword() {
    const input = document.getElementById('admin-password');
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    document.getElementById('pw-eye').innerHTML = isText
      ? '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'
      : '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
  }
</script>
</body>
</html>