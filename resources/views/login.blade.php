@extends('layout')
@section('title', 'Sign In')
@section('styles')
<style>
  .auth-wrap { min-height: calc(100vh - 64px); display: flex; align-items: center; justify-content: center; padding: 2rem; position: relative; z-index: 1; }
  .auth-wrap::before { content: ''; position: fixed; inset: 0; background: radial-gradient(ellipse 60% 60% at 50% 40%, rgba(212,137,26,0.06), transparent); pointer-events: none; }
  .auth-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 2.5rem; width: 100%; max-width: 440px; animation: fadeUp 0.4s ease; }
  .auth-logo { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 900; color: var(--amber-lt); text-align: center; margin-bottom: 0.25rem; }
  .auth-logo span { color: var(--cream); }
  .auth-tagline { text-align: center; font-size: 0.82rem; color: var(--muted); margin-bottom: 2rem; }
  .auth-title { font-family: 'Playfair Display', serif; font-size: 1.6rem; text-align: center; margin-bottom: 0.4rem; }
  .auth-subtitle { text-align: center; font-size: 0.88rem; color: var(--muted); margin-bottom: 2rem; }
  .form-group { margin-bottom: 1.25rem; }
  .form-label { display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.45rem; }
  .form-input { width: 100%; background: var(--bg); border: 1px solid var(--border); border-radius: 9px; color: var(--cream); font-family: 'DM Sans', sans-serif; font-size: 0.92rem; padding: 0.8rem 1rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
  .form-input:focus { border-color: var(--amber); box-shadow: 0 0 0 3px rgba(212,137,26,0.12); }
  .form-input::placeholder { color: var(--muted); }
  .form-input.is-invalid { border-color: rgba(200,80,80,0.6); }
  .input-wrap { position: relative; }
  .input-icon { position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--muted); pointer-events: none; }
  .input-wrap .form-input { padding-left: 2.5rem; }
  .input-toggle { position: absolute; right: 0.9rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--muted); cursor: pointer; padding: 0; }
  .forgot-link { display: block; text-align: right; font-size: 0.78rem; color: var(--muted); text-decoration: none; margin-top: 0.3rem; transition: color 0.2s; }
  .forgot-link:hover { color: var(--amber-lt); }
  .btn-auth { width: 100%; background: var(--amber); border: none; color: #161310; padding: 0.85rem; border-radius: 9px; font-weight: 700; font-size: 0.95rem; cursor: pointer; font-family: 'DM Sans', sans-serif; margin-top: 0.5rem; transition: background 0.2s; }
  .btn-auth:hover { background: var(--amber-lt); }
  .auth-divider { display: flex; align-items: center; gap: 1rem; margin: 1.5rem 0; }
  .auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
  .auth-divider span { font-size: 0.78rem; color: var(--muted); white-space: nowrap; }
  .auth-switch { text-align: center; font-size: 0.85rem; color: var(--muted); margin-top: 1.5rem; }
  .auth-switch a { color: var(--amber-lt); text-decoration: none; font-weight: 600; }
  .auth-switch a:hover { text-decoration: underline; }
  .alert-error { background: rgba(138,42,42,0.15); border: 1px solid rgba(200,80,80,0.3); border-radius: 8px; padding: 0.75rem 1rem; font-size: 0.83rem; color: #f0a0a0; margin-bottom: 1.25rem; }
  @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
</style>
@endsection

@section('content')
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">Volt<span>Revive</span></div>
    <div class="auth-tagline">The Vintage Electronics Restoration Marketplace</div>
    <h1 class="auth-title">Welcome Back</h1>
    <p class="auth-subtitle">Sign in to your account to continue</p>

    {{-- Server-side validation error --}}
    @if ($errors->any())
      <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    {{-- Real form — POST to AuthController@login --}}
    <form method="POST" action="/login">
      @csrf

      <div class="form-group">
        <label class="form-label">Email Address</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
          <input type="email" name="email" class="form-input @error('email') is-invalid @enderror"
                 placeholder="you@example.com" value="{{ old('email') }}" required autocomplete="email"/>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-wrap">
          <svg class="input-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          <input type="password" name="password" class="form-input" id="pw-field"
                 placeholder="Your password" required autocomplete="current-password"/>
          <button type="button" class="input-toggle" onclick="togglePw('pw-field')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
        <a href="#" class="forgot-link">Forgot password?</a>
      </div>

      <button type="submit" class="btn-auth">Sign In to VoltRevive</button>
    </form>

    <div class="auth-divider"><span>New to VoltRevive?</span></div>
    <div class="auth-switch">
      Don't have an account? <a href="/register">Create one free →</a>
    </div>
  </div>
</div>

<script>
  function togglePw(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
  }
</script>
@endsection