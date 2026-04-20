@extends('layout')
@section('title', 'Edit Profile')
@section('styles')
<style>
  .profile-wrap {
    min-height: calc(100vh - 64px);
    display: flex; align-items: flex-start; justify-content: center;
    padding: 3rem 1.5rem; position: relative; z-index: 1;
  }
  .profile-wrap::before {
    content: ''; position: fixed; inset: 0;
    background: radial-gradient(ellipse 60% 60% at 50% 30%, rgba(212,137,26,0.05), transparent);
    pointer-events: none;
  }
  .profile-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 20px; width: 100%; max-width: 560px;
    animation: fadeUp 0.4s ease; overflow: hidden;
  }
  .profile-header {
    background: var(--bg-card2); border-bottom: 1px solid var(--border);
    padding: 2rem 2.5rem; display: flex; align-items: center; gap: 1.5rem;
  }
  .profile-avatar-big {
    width: 68px; height: 68px; border-radius: 50%;
    border: 2px solid var(--amber);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif; font-weight: 900;
    font-size: 1.8rem; color: var(--amber-lt);
    flex-shrink: 0; overflow: hidden;
  }
  .profile-avatar-big img { width: 100%; height: 100%; object-fit: cover; }
  .profile-header-info h2 { font-family: 'Playfair Display', serif; font-size: 1.3rem; }
  .profile-header-info p  { font-size: 0.82rem; color: var(--muted); margin-top: 0.2rem; }
  .profile-role-badge {
    display: inline-block; margin-top: 0.4rem;
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.07em;
    text-transform: uppercase; color: var(--amber);
    background: rgba(212,137,26,0.1); border: 1px solid rgba(212,137,26,0.25);
    border-radius: 20px; padding: 0.15rem 0.65rem;
  }
  .profile-body { padding: 2.5rem; }
  .section-title {
    font-family: 'Playfair Display', serif; font-size: 1.1rem;
    margin-bottom: 1.4rem; display: flex; align-items: center; gap: 0.5rem;
  }
  .section-title::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
  }
  .form-group { margin-bottom: 1.25rem; }
  .form-label { display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.45rem; }
  .form-label .optional { color: var(--muted); font-weight: 400; font-size: 0.75rem; }
  .form-input {
    width: 100%; background: var(--bg); border: 1px solid var(--border);
    border-radius: 9px; color: var(--cream); font-family: 'DM Sans', sans-serif;
    font-size: 0.92rem; padding: 0.8rem 1rem; outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .form-input:focus { border-color: var(--amber); box-shadow: 0 0 0 3px rgba(212,137,26,0.12); }
  .form-input::placeholder { color: var(--muted); }
  .form-input.is-invalid { border-color: rgba(200,80,80,0.6); }
  .input-wrap { position: relative; }
  .input-icon { position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--muted); pointer-events: none; }
  .input-wrap .form-input { padding-left: 2.5rem; }
  .input-toggle { position: absolute; right: 0.9rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--muted); cursor: pointer; padding: 0; }
  .field-error { font-size: 0.78rem; color: #f09090; margin-top: 0.3rem; }
  .avatar-upload-row { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.4rem; }
  .avatar-preview {
    width: 52px; height: 52px; border-radius: 50%;
    border: 2px solid var(--border); overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif; font-weight: 900;
    font-size: 1.2rem; color: var(--amber-lt); flex-shrink: 0;
  }
  .avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
  .btn-upload {
    background: transparent; border: 1px solid var(--border); color: var(--cream);
    padding: 0.45rem 0.9rem; border-radius: 7px; font-size: 0.82rem;
    font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.15s;
  }
  .btn-upload:hover { border-color: var(--amber); color: var(--amber-lt); }
  #avatar-input { display: none; }
  .btn-save {
    width: 100%; background: var(--amber); border: none; color: #161310;
    padding: 0.85rem; border-radius: 9px; font-weight: 700; font-size: 0.95rem;
    cursor: pointer; font-family: 'DM Sans', sans-serif; margin-top: 0.25rem;
    transition: background 0.2s;
  }
  .btn-save:hover { background: var(--amber-lt); }
  .section-divider { border: none; border-top: 1px solid var(--border); margin: 2rem 0; }
  .alert-success {
    background: rgba(42,138,72,0.15); border: 1px solid rgba(80,200,100,0.25);
    border-radius: 9px; padding: 0.8rem 1rem; font-size: 0.85rem;
    color: #90f0a8; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;
  }
  .alert-error {
    background: rgba(138,42,42,0.15); border: 1px solid rgba(200,80,80,0.3);
    border-radius: 9px; padding: 0.8rem 1rem; font-size: 0.83rem;
    color: #f0a0a0; margin-bottom: 1.5rem;
  }
  .back-link {
    display: inline-flex; align-items: center; gap: 0.4rem;
    color: var(--muted); font-size: 0.82rem; text-decoration: none;
    margin-bottom: 1.5rem; transition: color 0.15s;
  }
  .back-link:hover { color: var(--cream); }
  @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
</style>
@endsection

@section('content')
<div class="profile-wrap">
  <div style="width:100%;max-width:560px;">

    {{-- Back link --}}
    @php
      $backUrl = auth()->user()->isTechnician() ? '/technician-dashboard' : '/collector-dashboard';
    @endphp
    <a href="{{ $backUrl }}" class="back-link">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Dashboard
    </a>

    <div class="profile-card">

      {{-- Header --}}
      <div class="profile-header">
        <div class="profile-avatar-big">
          @if($user->avatar)
            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"/>
          @else
            {{ $user->initial() }}
          @endif
        </div>
        <div class="profile-header-info">
          <h2>{{ $user->name }}</h2>
          <p>{{ $user->email }}</p>
          <span class="profile-role-badge">{{ $user->roleLabel() }}</span>
        </div>
      </div>

      <div class="profile-body">

        {{-- Flash messages --}}
        @if(session('success'))
          <div class="alert-success">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
          </div>
        @endif
        @if($errors->any())
          <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        {{-- ── SECTION 1: Personal Info ── --}}
        <p class="section-title">Personal Information</p>

        <form method="POST" action="/profile/update" enctype="multipart/form-data">
          @csrf
          @method('PATCH')

          {{-- Avatar --}}
          <div class="avatar-upload-row">
            <div class="avatar-preview" id="avatar-preview">
              @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" id="avatar-img" alt="Avatar"/>
              @else
                <span id="avatar-initial">{{ $user->initial() }}</span>
              @endif
            </div>
            <div>
              <button type="button" class="btn-upload" onclick="document.getElementById('avatar-input').click()">
                📷 Change Photo
              </button>
              <p style="font-size:0.73rem;color:var(--muted);margin-top:0.3rem;">JPG, PNG · max 2MB</p>
            </div>
            <input type="file" name="avatar" id="avatar-input" accept="image/*" onchange="previewAvatar(event)"/>
          </div>

          {{-- Name --}}
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <div class="input-wrap">
              <svg class="input-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input type="text" name="name" class="form-input @error('name') is-invalid @enderror"
                     value="{{ old('name', $user->name) }}" placeholder="Your full name" required/>
            </div>
            @error('name')<p class="field-error">{{ $message }}</p>@enderror
          </div>

          {{-- Email --}}
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-wrap">
              <svg class="input-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,12 2,6"/></svg>
              <input type="email" name="email" class="form-input @error('email') is-invalid @enderror"
                     value="{{ old('email', $user->email) }}" placeholder="you@example.com" required/>
            </div>
            @error('email')<p class="field-error">{{ $message }}</p>@enderror
          </div>

          {{-- Phone --}}
          <div class="form-group">
            <label class="form-label">Phone Number <span class="optional">(optional)</span></label>
            <div class="input-wrap">
              <svg class="input-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.46 2 2 0 0 1 3.59 1.28h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6.13 6.13l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              <input type="tel" name="phone" class="form-input @error('phone') is-invalid @enderror"
                     value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000"/>
            </div>
            @error('phone')<p class="field-error">{{ $message }}</p>@enderror
          </div>

          <button type="submit" class="btn-save">Save Changes</button>
        </form>

        <hr class="section-divider"/>

        {{-- ── SECTION 2: Change Password ── --}}
        <p class="section-title">Change Password</p>

        <form method="POST" action="/profile/password">
          @csrf
          @method('PATCH')

          <div class="form-group">
            <label class="form-label">Current Password</label>
            <div class="input-wrap">
              <svg class="input-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input type="password" name="current_password" id="pw-current"
                     class="form-input @error('current_password') is-invalid @enderror"
                     placeholder="Enter current password"/>
              <button type="button" class="input-toggle" onclick="togglePw('pw-current')">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            @error('current_password')<p class="field-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">New Password</label>
            <div class="input-wrap">
              <svg class="input-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input type="password" name="password" id="pw-new"
                     class="form-input @error('password') is-invalid @enderror"
                     placeholder="Min. 8 characters"/>
              <button type="button" class="input-toggle" onclick="togglePw('pw-new')">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            @error('password')<p class="field-error">{{ $message }}</p>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">Confirm New Password</label>
            <div class="input-wrap">
              <svg class="input-icon" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input type="password" name="password_confirmation" id="pw-confirm"
                     class="form-input" placeholder="Repeat new password"/>
              <button type="button" class="input-toggle" onclick="togglePw('pw-confirm')">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
          </div>

          <button type="submit" class="btn-save" style="background:var(--bg-card2);color:var(--cream);border:1px solid var(--border);"
            onmouseover="this.style.borderColor='var(--amber)';this.style.color='var(--amber-lt)'"
            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--cream)'">
            Update Password
          </button>
        </form>

      </div>{{-- /profile-body --}}
    </div>{{-- /profile-card --}}
  </div>
</div>

<script>
  function togglePw(id) {
    var el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
  }
  function previewAvatar(e) {
    var file = e.target.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(ev) {
      var preview = document.getElementById('avatar-preview');
      preview.innerHTML = '<img src="' + ev.target.result + '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"/>';
    };
    reader.readAsDataURL(file);
  }
</script>
@endsection
