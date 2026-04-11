{{-- ═══════════════════════════════════════════════════ --}}
{{--  FILE: resources/views/repair-wall.blade.php       --}}
{{-- ═══════════════════════════════════════════════════ --}}
@extends('layout')
@section('title', 'Repair Request Wall')
@section('styles')
<style>
  .wall-hero { max-width:1400px; margin:0 auto; padding:3.5rem 2rem 2rem; position:relative; z-index:1; display:flex; align-items:flex-end; justify-content:space-between; gap:2rem; flex-wrap:wrap; }
  .wall-hero h1 { font-size:clamp(2rem,4vw,3rem); line-height:1.1; }
  .wall-hero h1 em { font-style:italic; color:var(--amber-lt); }
  .wall-hero p { color:var(--muted); font-size:1rem; margin-top:0.5rem; max-width:500px; }
  .btn-post { background:var(--amber); border:none; color:#161310; padding:0.75rem 1.5rem; border-radius:10px; font-weight:700; font-size:0.92rem; cursor:pointer; font-family:'DM Sans',sans-serif; display:flex; align-items:center; gap:0.5rem; transition:background 0.2s; flex-shrink:0; }
  .btn-post:hover { background:var(--amber-lt); }
  .wall-layout { max-width:1400px; margin:0 auto; padding:0 2rem 5rem; display:grid; grid-template-columns:1fr 340px; gap:2rem; position:relative; z-index:1; }
  .section-label { font-size:0.7rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--muted); margin-bottom:1.25rem; display:flex; align-items:center; gap:0.75rem; }
  .section-label::after { content:''; flex:1; height:1px; background:var(--border); }
  .appeals-list { display:flex; flex-direction:column; gap:1rem; }
  .appeal-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; padding:1.5rem; transition:border-color 0.2s; animation:fadeUp 0.4s ease both; position:relative; }
  .appeal-card:hover { border-color:rgba(212,137,26,0.4); }
  .appeal-card.flagged { border-color:var(--amber); }
  .appeal-card:nth-child(1){animation-delay:0.05s}.appeal-card:nth-child(2){animation-delay:0.1s}.appeal-card:nth-child(3){animation-delay:0.15s}
  .appeal-top { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:0.75rem; }
  .appeal-device { font-family:'Playfair Display',serif; font-size:1.1rem; font-weight:700; }
  .appeal-cat { display:inline-flex; align-items:center; gap:0.4rem; background:rgba(212,137,26,0.1); border:1px solid rgba(212,137,26,0.2); color:var(--amber-lt); padding:0.2rem 0.65rem; border-radius:20px; font-size:0.7rem; font-weight:700; letter-spacing:0.05em; }
  .appeal-desc { font-size:0.88rem; color:var(--muted); line-height:1.6; margin-bottom:1.25rem; }
  .appeal-footer { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
  .appeal-meta { display:flex; align-items:center; gap:1rem; }
  .appeal-author { display:flex; align-items:center; gap:0.5rem; font-size:0.8rem; color:var(--muted); }
  .author-avatar { width:28px; height:28px; border-radius:50%; background:var(--bg-card2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:0.72rem; font-weight:700; color:var(--amber); }
  .appeal-date { font-size:0.75rem; color:var(--muted); }
  .btn-help { display:flex; align-items:center; gap:0.5rem; background:transparent; border:1px solid var(--border); color:var(--cream); padding:0.5rem 1rem; border-radius:8px; font-size:0.82rem; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all 0.2s; }
  .btn-help:hover { border-color:var(--amber); color:var(--amber-lt); }
  .btn-help.coll-book { border-color:rgba(212,137,26,0.35); color:var(--amber-lt); }
  .btn-help.coll-book:hover { background:rgba(212,137,26,0.1); }
  /* role visibility on repair wall */
  .rw-tech-action { display:none; }
  .rw-coll-action { display:none; }
  body.role-tech .rw-tech-action { display:flex; }
  body.role-coll .rw-coll-action { display:flex; }
  body.role-tech .rw-coll-action { display:none; }
  body.role-guest .rw-tech-action,
  body.role-guest .rw-coll-action { display:none; }
  .urgent-badge { position:absolute; top:12px; right:12px; background:rgba(200,60,60,0.15); border:1px solid rgba(200,60,60,0.3); color:#f09090; padding:0.15rem 0.55rem; border-radius:20px; font-size:0.65rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; }
  /* SIDEBAR */
  .wall-sidebar { display:flex; flex-direction:column; gap:1.25rem; }
  .side-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; padding:1.5rem; }
  .side-card-title { font-family:'Playfair Display',serif; font-size:1rem; margin-bottom:1rem; }
  .hot-list { display:flex; flex-direction:column; gap:0.6rem; }
  .hot-item { display:flex; align-items:center; justify-content:space-between; gap:1rem; font-size:0.82rem; padding:0.5rem 0; border-bottom:1px solid var(--border); }
  .hot-item:last-child { border-bottom:none; }
  .hot-device { font-weight:600; }
  .hot-count { color:var(--amber-lt); font-weight:700; font-size:0.78rem; white-space:nowrap; }
  .stat-mini { text-align:center; padding:0.75rem 0; border-bottom:1px solid var(--border); }
  .stat-mini:last-child { border-bottom:none; }
  .stat-mini strong { font-family:'Playfair Display',serif; font-size:1.8rem; color:var(--amber-lt); display:block; }
  .stat-mini span { font-size:0.78rem; color:var(--muted); }
  /* POST MODAL */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:999; align-items:center; justify-content:center; padding:1rem; }
  .modal-overlay.open { display:flex; }
  .post-modal { background:var(--bg-card); border:1px solid var(--border); border-radius:18px; padding:2rem; max-width:500px; width:100%; animation:fadeUp 0.3s ease; }
  .post-modal h2 { font-family:'Playfair Display',serif; font-size:1.4rem; margin-bottom:0.5rem; }
  .post-modal p { font-size:0.85rem; color:var(--muted); margin-bottom:1.5rem; }
  .form-group { margin-bottom:1.1rem; }
  .form-label { display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.4rem; }
  .form-input,.form-select,.form-textarea { width:100%; background:var(--bg); border:1px solid var(--border); border-radius:8px; color:var(--cream); font-family:'DM Sans',sans-serif; font-size:0.9rem; padding:0.75rem 1rem; outline:none; appearance:none; transition:border-color 0.2s; }
  .form-input:focus,.form-select:focus,.form-textarea:focus { border-color:var(--amber); }
  .form-input::placeholder,.form-textarea::placeholder { color:var(--muted); }
  .char-count { font-size:0.72rem; color:var(--muted); text-align:right; margin-top:0.2rem; }
  .modal-btns { display:flex; gap:0.75rem; margin-top:1.25rem; }
  .btn-cancel-sm { flex:1; background:transparent; border:1px solid var(--border); color:var(--muted); padding:0.7rem; border-radius:8px; cursor:pointer; font-family:'DM Sans',sans-serif; font-size:0.88rem; }
  .btn-post-sm { flex:2; background:var(--amber); border:none; color:#161310; padding:0.7rem; border-radius:8px; font-weight:700; cursor:pointer; font-family:'DM Sans',sans-serif; font-size:0.88rem; }
  .btn-post-sm:hover { background:var(--amber-lt); }
  .rewrite-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.4rem; }
  .btn-rewrite {
    display:inline-flex; align-items:center; gap:0.35rem;
    background:transparent; border:1px solid rgba(130,170,255,0.35);
    color:#8ab4ff; padding:0.3rem 0.8rem; border-radius:6px;
    font-size:0.75rem; font-weight:700; cursor:pointer;
    font-family:'DM Sans',sans-serif; transition:all 0.2s; white-space:nowrap;
  }
  .btn-rewrite:hover { background:rgba(130,170,255,0.1); border-color:#8ab4ff; }
  .btn-rewrite:disabled { opacity:0.45; cursor:not-allowed; }
  .rewrite-preview {
    background:rgba(130,170,255,0.06); border:1px solid rgba(130,170,255,0.2);
    border-radius:8px; padding:0.75rem 1rem; font-size:0.85rem;
    color:var(--cream); line-height:1.55; margin-bottom:0.75rem;
  }
  .rewrite-actions { display:flex; gap:0.5rem; margin-bottom:0.75rem; }
  .btn-use-rewrite {
    flex:1; background:rgba(130,170,255,0.15); border:1px solid rgba(130,170,255,0.4);
    color:#8ab4ff; padding:0.5rem; border-radius:7px; font-size:0.8rem;
    font-weight:700; cursor:pointer; font-family:'DM Sans',sans-serif;
  }
  .btn-keep-original {
    flex:1; background:transparent; border:1px solid var(--border);
    color:var(--muted); padding:0.5rem; border-radius:7px; font-size:0.8rem;
    cursor:pointer; font-family:'DM Sans',sans-serif;
  }
  @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
  @media(max-width:900px){ .wall-layout{grid-template-columns:1fr} .wall-sidebar{order:-1} }
  @media(max-width:600px){ .wall-hero,.wall-layout{padding-left:1rem;padding-right:1rem} }
</style>
@endsection

@section('content')
<div class="wall-hero">
  <div>
    <h1>Repair Request <em>Wall</em></h1>
    <p>Can't find a technician for your rare device? Post a rescue appeal — the community will respond.</p>
  </div>
  @auth
  @if(auth()->user()->isTechnician())
    <a href="/services/create" class="btn-post">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      List Your Service
    </a>
  @else
    <button class="btn-post" id="btn-post-appeal" onclick="document.getElementById('post-modal').classList.add('open')">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Post a Rescue Appeal
    </button>
  @endif
@else
  <button class="btn-post" id="btn-post-appeal" onclick="document.getElementById('guest-modal').classList.add('open')">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Post a Rescue Appeal
  </button>
@endauth

{{-- Guest sign-in prompt modal --}}
<div class="wall-layout">
  {{-- APPEALS FEED --}}
  <div>
    <div class="section-label">Active Rescue Appeals</div>
    {{-- Flash success --}}
    @if(session('success'))
      <div style="background:rgba(30,160,100,0.1);border:1px solid rgba(74,200,120,0.3);border-radius:10px;padding:0.9rem 1.25rem;margin-bottom:1.25rem;font-size:0.88rem;color:#5de0b0;">
        ✓ {{ session('success') }}
      </div>
    @endif

    <div class="appeals-list" id="appeals-list">
      @forelse($posts as $post)
      <div class="appeal-card" id="appeal-{{ $post->id }}">
        <div class="appeal-top">
          <div>
            <div class="appeal-device">{{ $post->device_name }}</div>
            <div style="margin-top:0.3rem"><span class="appeal-cat">{{ $post->category }}</span></div>
          </div>
        </div>
        <p class="appeal-desc">{{ $post->description }}</p>
        <div class="appeal-footer">
          <div class="appeal-meta">
            <div class="appeal-author">
              <div class="author-avatar">{{ strtoupper(substr($post->user->name ?? '?', 0, 1)) }}</div>
              {{ $post->user->name ?? 'Anonymous' }}
            </div>
            <div class="appeal-date">Posted {{ $post->created_at->diffForHumans() }}</div>
          </div>
          {{-- Technicians see Contact Collector button --}}
          @auth
            @if(auth()->user()->isTechnician())
              <button class="btn-help"
                onclick="openContact('{{ addslashes($post->user->name ?? '') }}', '{{ addslashes($post->user->phone ?? 'Not provided') }}')">
                📞 Contact Collector
              </button>
            @endif
          @endauth
        </div>
      </div>
      @empty
      <div style="text-align:center;padding:3rem;color:var(--muted);">
        <div style="font-size:2.5rem;margin-bottom:1rem;">📋</div>
        <p style="font-weight:600;">No rescue appeals yet</p>
        <p style="font-size:0.85rem;margin-top:0.4rem;">Be the first to post one!</p>
      </div>
      @endforelse
    </div>
  </div>

  {{-- SIDEBAR --}}    </div>
  </div>

  {{-- SIDEBAR --}}
  <aside class="wall-sidebar">
    <div class="side-card">
      <div style="display:grid;grid-template-columns:1fr 1fr;">
        <div class="stat-mini"><strong>{{ $totalPosts }}</strong><span>Open Appeals</span></div>
        <div class="stat-mini"><strong>{{ $recentPosts }}</strong><span>This Week</span></div>
      </div>
    </div>

    <div class="side-card">
      <div class="side-card-title">🔧 Top Devices on the Wall</div>
      <div class="hot-list">
        @php
          $topDevices = $posts->groupBy('device_name')
            ->map->count()
            ->sortDesc()
            ->take(5);
        @endphp
        @forelse($topDevices as $device => $count)
        <div class="hot-item">
          <span class="hot-device">{{ $device }}</span>
          <span class="hot-count">{{ $count }} {{ $count === 1 ? 'appeal' : 'appeals' }}</span>
        </div>
        @empty
        <div style="font-size:0.82rem;color:var(--muted);padding:0.5rem 0;">No posts yet.</div>
        @endforelse
      </div>
      <p style="font-size:0.75rem;color:var(--muted);margin-top:1rem;line-height:1.5;">Devices with the most repair appeals posted by the community.</p>
    </div>

    <div class="side-card" id="tech-recruitment-card">
      <div class="side-card-title">Are You a Technician?</div>
      <p style="font-size:0.83rem;color:var(--muted);line-height:1.6;margin-bottom:1rem;">If you can help with any of these devices, contact the collector directly or list your service officially on the platform.</p>
      <a href="/services/create" style="display:block;text-align:center;background:var(--amber);color:#161310;padding:0.65rem;border-radius:8px;font-weight:700;font-size:0.85rem;text-decoration:none;transition:background 0.2s;" onmouseover="this.style.background='var(--amber-lt)'" onmouseout="this.style.background='var(--amber)'">List Your Service →</a>
    </div>
  </aside>
</div>

{{-- POST MODAL --}}
<div class="modal-overlay" id="post-modal">
  <div class="post-modal">
    <h2>Post a Rescue Appeal</h2>
    <p>Describe your device and the fault. The more detail you give, the more likely a technician will respond.</p>
    <form method="POST" action="/repair-wall" id="appeal-form">
      @csrf
      <div class="form-group">
        <label class="form-label">Device Name *</label>
        <input type="text" class="form-input" id="appeal-device-input" name="device_name" placeholder="e.g. Fairlight CMI Series IIx" required/>
      </div>
      <div class="form-group">
        <label class="form-label">Category *</label>
        <select class="form-select" name="category" required>
          <option value="" disabled selected>Select…</option>
          <option>Synthesizer</option>
          <option>Retro Gaming</option>
          <option>Hi-Fi Audio</option>
          <option>Vintage Radio</option>
          <option>Cameras</option>
          <option>Vintage Computer</option>
          <option>Other</option>
        </select>
      </div>
      <div class="form-group">
        <div class="rewrite-row">
          <label class="form-label">Describe the fault *</label>
          <button type="button" class="btn-rewrite" id="btn-rewrite" onclick="rewriteDescription()" disabled>
            ✨ Rewrite Professionally
          </button>
        </div>
        <textarea class="form-textarea" id="appeal-desc-input" name="description" placeholder="What's wrong with it? What have you already tried? Any service manuals available?" rows="4" maxlength="400" oninput="onDescInput()" required></textarea>
        <div class="char-count" id="char-count">400 chars left</div>
        <div id="rewrite-preview-wrap" style="display:none; margin-top:0.75rem;">
          <div class="rewrite-preview" id="rewrite-preview-text"></div>
          <div class="rewrite-actions">
            <button type="button" class="btn-use-rewrite" onclick="useRewrite()">✓ Use This Version</button>
            <button type="button" class="btn-keep-original" onclick="keepOriginal()">✕ Keep My Version</button>
          </div>
        </div>
      </div>
      <div class="modal-btns">
        <button type="button" class="btn-cancel-sm" onclick="document.getElementById('post-modal').classList.remove('open')">Cancel</button>
        <button type="submit" class="btn-post-sm">Post Appeal →</button>
      </div>
    </form>
  </div>
</div>

{{-- CONTACT MODAL --}}
<div class="modal-overlay" id="contact-modal">
  <div class="modal-box" style="max-width:380px;">
    <h2 style="font-size:1.2rem;margin-bottom:0.5rem;">Contact Collector</h2>
    <p style="font-size:0.85rem;color:var(--muted);margin-bottom:1.25rem;">Reach out directly to the collector about their repair appeal.</p>
    <div style="background:var(--bg-card2);border:1px solid var(--border);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
      <div style="display:flex;align-items:center;gap:0.75rem;">
        <div style="width:40px;height:40px;border-radius:50%;background:var(--amber);display:flex;align-items:center;justify-content:center;font-weight:700;color:#161310;font-size:1rem;flex-shrink:0;" id="contact-initial">?</div>
        <div>
          <div style="font-size:0.9rem;font-weight:700;" id="contact-name">—</div>
          <div style="font-size:0.75rem;color:var(--muted);">Collector</div>
        </div>
      </div>
      <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);">
        <div style="font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);margin-bottom:0.4rem;">Mobile Number</div>
        <div style="font-size:1.05rem;font-weight:700;color:var(--amber-lt);display:flex;align-items:center;gap:0.5rem;">
          📞 <span id="contact-phone">—</span>
        </div>
      </div>
    </div>
    <div style="display:flex;gap:0.75rem;">
      <button onclick="document.getElementById('contact-modal').classList.remove('open')"
        style="width:100%;background:transparent;border:1px solid var(--border);color:var(--muted);padding:0.7rem;border-radius:8px;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.88rem;">
        Close
      </button>
    </div>
  </div>
</div>

<script>
  // Apply role to body so CSS shows correct buttons
  (function() {
    const role = localStorage.getItem('vr_role');
    if (role === 'technician') {
      document.body.classList.add('role-tech');
      // Technicians cannot post rescue appeals — hide the button
      const postBtn = document.getElementById('btn-post-appeal');
      if (postBtn) postBtn.style.display = 'none';
    } else if (role === 'collector') {
      document.body.classList.add('role-coll');
      // Collectors don't need the "Are You a Technician?" recruitment card
      const techCard = document.getElementById('tech-recruitment-card');
      if (techCard) techCard.style.display = 'none';
    } else {
      document.body.classList.add('role-guest');
    }
  })();

  function openContact(el) {
    // el can be a button (reads from parent card) or called with (name, phone)
    var name, phone;
    if (typeof el === 'string') { name = el; phone = arguments[1]; }
    else {
      var card = el.closest('.appeal-card');
      name = card ? card.dataset.name : '—';
      phone = card ? card.dataset.phone : '—';
    }
    document.getElementById('contact-name').textContent = name;
    document.getElementById('contact-phone').textContent = phone;
    document.getElementById('contact-initial').textContent = name.charAt(0);
    document.getElementById('contact-modal').classList.add('open');
  }

  function submitAppeal(e) {
    e.preventDefault();
    const device = document.getElementById('appeal-device-input').value;
    const desc = document.getElementById('appeal-desc-input').value;
    const list = document.getElementById('appeals-list');
    const id = Date.now();
    const card = document.createElement('div');
    card.className = 'appeal-card';
    card.id = 'appeal-' + id;
    card.style.borderColor = 'var(--amber)';
    card.innerHTML = `
      <div class="appeal-top">
        <div>
          <div class="appeal-device">${device}</div>
          <div style="margin-top:0.3rem"><span class="appeal-cat">New Appeal</span></div>
        </div>
      </div>
      <p class="appeal-desc">${desc}</p>
      <div class="appeal-footer">
        <div class="appeal-meta">
          <div class="appeal-author"><div class="author-avatar">Y</div> You</div>
          <div class="appeal-date">Just now</div>
        </div>
        <button class="btn-help rw-tech-action" onclick="openContact('You', 'your number')">
          📞 Contact Collector
        </button>
        <button class="btn-help rw-coll-action" onclick="openContact(this)">📞 Contact Collector</button>
      </div>`;
    list.insertBefore(card, list.firstChild);
    document.getElementById('post-modal').classList.remove('open');
    e.target.reset();
    document.getElementById('char-count').textContent = '400 chars left';
  }

  const csrfToken2 = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

  // Enable rewrite button only when textarea has ≥20 chars
  function onDescInput() {
    const ta = document.getElementById('appeal-desc-input');
    document.getElementById('char-count').textContent = (400 - ta.value.length) + ' chars left';
    const btn = document.getElementById('btn-rewrite');
    if (btn) btn.disabled = ta.value.trim().length < 20;
    // Hide stale preview on edit
    document.getElementById('rewrite-preview-wrap').style.display = 'none';
  }

  let _rewriteText = '';

  function rewriteDescription() {
    const desc   = document.getElementById('appeal-desc-input').value.trim();
    const device = document.getElementById('appeal-device-input') ? document.getElementById('appeal-device-input').value.trim() : '';
    const btn    = document.getElementById('btn-rewrite');
    if (!desc) return;
    btn.disabled = true;
    btn.textContent = '⏳ Rewriting…';

    fetch('/api/rewrite-description', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken2 },
      body: JSON.stringify({ description: desc, device_name: device }),
    })
    .then(r => r.json())
    .then(data => {
      btn.disabled = false;
      btn.textContent = '✨ Rewrite Professionally';
      if (data.success && data.rewritten) {
        _rewriteText = data.rewritten;
        document.getElementById('rewrite-preview-text').textContent = data.rewritten;
        document.getElementById('rewrite-preview-wrap').style.display = 'block';
      } else {
        alert(data.message || 'Could not rewrite. Please try again.');
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = '✨ Rewrite Professionally';
      alert('Network error. Please try again.');
    });
  }

  function useRewrite() {
    if (!_rewriteText) return;
    const ta = document.getElementById('appeal-desc-input');
    ta.value = _rewriteText;
    document.getElementById('char-count').textContent = (400 - ta.value.length) + ' chars left';
    document.getElementById('rewrite-preview-wrap').style.display = 'none';
    _rewriteText = '';
  }

  function keepOriginal() {
    document.getElementById('rewrite-preview-wrap').style.display = 'none';
    _rewriteText = '';
  }

    document.getElementById('contact-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
  });

  document.getElementById('post-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
  });
  const guestModal = document.getElementById('guest-modal');
  if (guestModal) {
    guestModal.addEventListener('click', function(e) {
      if (e.target === this) this.classList.remove('open');
    });
  }
</script>
@guest
<div class="modal-overlay" id="guest-modal">
  <div class="post-modal" style="max-width:420px;text-align:center;">
    <div style="font-size:2.5rem;margin-bottom:1rem;">🔒</div>
    <h2 style="margin-bottom:0.5rem;">Sign In to Post</h2>
    <p style="margin-bottom:1.5rem;">You need a Collector account to post a Rescue Appeal. Join free or sign in.</p>
    <div style="display:flex;flex-direction:column;gap:0.75rem;">
      <a href="/register" class="btn-post-sm" style="text-align:center;text-decoration:none;display:block;padding:0.75rem;">
        Join as Collector &mdash; it's free
      </a>
      <a href="/login" style="text-align:center;font-size:0.88rem;color:var(--amber-lt);text-decoration:none;padding:0.5rem;">
        Already have an account? Sign in &rarr;
      </a>
    </div>
    <button type="button" onclick="document.getElementById('guest-modal').classList.remove('open')"
      style="margin-top:1rem;background:transparent;border:none;color:var(--muted);font-size:0.82rem;cursor:pointer;font-family:'DM Sans',sans-serif;">
      Cancel
    </button>
  </div>
</div>
@endguest
</div>


@endsection