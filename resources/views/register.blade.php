@extends('layout')
@section('title', 'Create Account')
@section('styles')
<style>
  .auth-wrap { min-height: calc(100vh - 64px); display: flex; align-items: center; justify-content: center; padding: 2rem; position: relative; z-index: 1; }
  .auth-wrap::before { content: ''; position: fixed; inset: 0; background: radial-gradient(ellipse 60% 60% at 50% 40%, rgba(212,137,26,0.06), transparent); pointer-events: none; }
  .auth-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 2.5rem; width: 100%; max-width: 480px; animation: fadeUp 0.4s ease; }
  .auth-logo { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 900; color: var(--amber-lt); text-align: center; margin-bottom: 0.25rem; }
  .auth-logo span { color: var(--cream); }
  .auth-tagline { text-align: center; font-size: 0.82rem; color: var(--muted); margin-bottom: 2rem; }
  .auth-title { font-family: 'Playfair Display', serif; font-size: 1.6rem; text-align: center; margin-bottom: 0.4rem; }
  .auth-subtitle { text-align: center; font-size: 0.88rem; color: var(--muted); margin-bottom: 2rem; }
  .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  .form-group { margin-bottom: 1.1rem; }
  .form-label { display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.4rem; }
  .form-label .req { color: var(--amber); }
  .form-input { width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: 9px; color: var(--cream); font-family: 'DM Sans', sans-serif; font-size: 0.92rem; padding: 0.8rem 1rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
  .form-input:focus { border-color: var(--amber); box-shadow: 0 0 0 3px rgba(212,137,26,0.12); }
  .form-input::placeholder { color: var(--muted); }
  .input-wrap { position: relative; }
  .input-icon { position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--muted); pointer-events: none; }
  .input-wrap .form-input { padding-left: 2.5rem; }
  .input-toggle { position: absolute; right: 0.9rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--muted); cursor: pointer; padding: 0; }
  .hint { font-size: 0.73rem; color: var(--muted); margin-top: 0.25rem; }
  .strength-bar-wrap { height: 4px; background: var(--border); border-radius: 2px; margin-top: 0.5rem; }
  .strength-bar { height: 100%; border-radius: 2px; transition: width 0.3s, background 0.3s; width: 0%; }
  .strength-label { font-size: 0.72rem; margin-top: 0.3rem; }
  .role-note { background: rgba(212,137,26,0.07); border: 1px solid rgba(212,137,26,0.2); border-radius: 8px; padding: 0.85rem 1rem; font-size: 0.8rem; color: var(--muted); margin-bottom: 1.25rem; display: flex; align-items: flex-start; gap: 0.6rem; }
  .role-note span:first-child { color: var(--amber); flex-shrink: 0; }
  .terms-row { display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 1.25rem; }
  .terms-row input[type="checkbox"] { accent-color: var(--amber); margin-top: 2px; flex-shrink: 0; width: 16px; height: 16px; }
  .terms-row label { font-size: 0.82rem; color: var(--muted); line-height: 1.5; }
  .terms-row a { color: var(--amber-lt); text-decoration: none; }
  .btn-auth { width: 100%; background: var(--amber); border: none; color: #161310; padding: 0.85rem; border-radius: 9px; font-weight: 700; font-size: 0.95rem; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background 0.2s; }
  .btn-auth:hover { background: var(--amber-lt); }
  .auth-switch { text-align: center; font-size: 0.85rem; color: var(--muted); margin-top: 1.5rem; }
  .auth-switch a { color: var(--amber-lt); text-decoration: none; font-weight: 600; }
  .error-msg { font-size: 0.75rem; color: #f09090; margin-top: 0.25rem; display: none; }
  @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:480px) { .form-row-2 { grid-template-columns: 1fr; } }
</style>
@endsection
@section('content')
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">Volt<span>Revive</span></div>
    <div class="auth-tagline">The Vintage Electronics Restoration Marketplace</div>
    <h1 class="auth-title">Create Your Account</h1>
    <p class="auth-subtitle">
      @if(request('role') === 'technician')
        Create your account — apply to become a Technician right after
      @else
        Create your account — list devices, book restorers, join the community
      @endif
    </p>

    @if(request('role') === 'technician')
    <div class="role-note" style="border-color:rgba(93,224,176,0.25);background:rgba(93,224,176,0.06);">
      <span style="color:#5de0b0;">&#x1F527;</span>
      <span>You're on the path to becoming a <strong style="color:var(--cream)">Technician</strong>. Create your account first — you'll find the Technician application in your dashboard immediately after.</span>
    </div>
    @else
    <div class="role-note">
      <span>&#x1F4E6;</span>
      <span>Your account gives you access to the full marketplace — book technicians, track devices, post repair appeals, and RSVP to community events.</span>
    </div>
    @endif

    <form method="POST" action="/register" id="reg-form">
      @csrf
      {{-- Carry the role through the POST body --}}
      <input type="hidden" name="role" value="{{ request('role') }}">
      <div class="form-row-2">
        <div class="form-group">
          <label class="form-label">First Name <span class="req">*</span></label>
          <input type="text" class="form-input" id="fname" name="first_name" placeholder="Sarah" required/>
        </div>
        <div class="form-group">
          <label class="form-label">Last Name <span class="req">*</span></label>
          <input type="text" class="form-input" name="last_name" placeholder="Khan" required/>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Email Address <span class="req">*</span></label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
          <input type="email" class="form-input" name="email" placeholder="you@example.com" required/>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Phone Number <span class="req">*</span></label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <input type="tel" class="form-input" name="phone" placeholder="+880 1XXX-XXXXXX" required/>
        </div>
        <p class="hint">Used for urgent SMS alerts about your repair slots.</p>
      </div>
      <div class="form-group">
        <label class="form-label">Password <span class="req">*</span></label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" class="form-input" id="pw1" name="password" placeholder="Min. 8 characters" oninput="checkStrength(this.value)" required/>
          <button type="button" class="input-toggle" onclick="togglePw('pw1', this)">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <div class="strength-bar-wrap"><div class="strength-bar" id="strength-bar"></div></div>
        <div class="strength-label" id="strength-label" style="color:var(--muted)"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Confirm Password <span class="req">*</span></label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" class="form-input" id="pw2" name="password_confirmation" placeholder="Repeat your password" required/>
          <button type="button" class="input-toggle" onclick="togglePw('pw2', this)">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <div class="error-msg" id="pw-match-err">Passwords do not match.</div>
      </div>

      <div class="terms-row">
        <input type="checkbox" id="terms" required/>
        <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I understand my phone number will only be used for urgent repair notifications.</label>
      </div>

      <button type="submit" class="btn-auth">Create My Account →</button>
    </form>

    <div class="auth-switch">
      Already have an account? <a href="/login">Sign in →</a>
    </div>
  </div>
</div>
<script>
  function togglePw(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
  }
  function checkStrength(val) {
    const bar = document.getElementById('strength-bar');
    const label = document.getElementById('strength-label');
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['#e06060','#e09030','#d4891a','#4a9a60'];
    const labels = ['Weak','Fair','Good','Strong'];
    bar.style.width = (score * 25) + '%';
    bar.style.background = colors[score - 1] || 'var(--border)';
    label.textContent = score > 0 ? labels[score - 1] : '';
    label.style.color = colors[score - 1] || 'var(--muted)';
  }
  // Client-side password match check before real POST submit
  document.getElementById('reg-form').addEventListener('submit', function(e) {
    const p1 = document.getElementById('pw1').value;
    const p2 = document.getElementById('pw2').value;
    const errEl = document.getElementById('pw-match-err');
    if (p1 !== p2) {
      e.preventDefault();
      errEl.style.display = 'block';
      return;
    }
    errEl.style.display = 'none';
    // Form submits normally to POST /register -> AuthController@register
  });
</script>
@endsection