@extends('layout')
@section('title')
{{ $user->name }} — Technician Profile
@endsection

@section('styles')
<style>
  /* ── PROFILE HERO ── */
  .profile-hero {
    position: relative; z-index: 1;
    background: var(--bg-card);
    border-bottom: 1px solid var(--border);
  }
  .profile-hero-inner {
    max-width: 1400px; margin: 0 auto;
    padding: 3rem 2rem 0;
  }
  .back-link {
    display: inline-flex; align-items: center; gap: 0.4rem;
    color: var(--muted); text-decoration: none; font-size: 0.85rem;
    margin-bottom: 2rem; transition: color 0.2s;
  }
  .back-link:hover { color: var(--cream); }

  .profile-header {
    display: grid; grid-template-columns: auto 1fr auto;
    gap: 2rem; align-items: start;
    padding-bottom: 2rem;
  }
  .profile-avatar-wrap { position: relative; }
  .profile-avatar {
    width: 110px; height: 110px; border-radius: 50%;
    background: linear-gradient(135deg, #2a1f0e, #3d2b14);
    border: 3px solid var(--amber);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif;
    font-size: 2.8rem; font-weight: 900; color: var(--amber-lt);
  }
  .verified-badge {
    position: absolute; bottom: 4px; right: 4px;
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--amber); border: 2px solid var(--bg-card);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem;
  }
  .profile-info h1 {
    font-size: 1.9rem; font-weight: 900; margin-bottom: 0.3rem;
  }
  .profile-tagline { color: var(--muted); font-size: 0.95rem; margin-bottom: 1rem; }
  .profile-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem; }
  .profile-tag {
    background: rgba(212,137,26,0.1); border: 1px solid rgba(212,137,26,0.25);
    color: var(--amber-lt); padding: 0.25rem 0.75rem;
    border-radius: 20px; font-size: 0.75rem; font-weight: 600;
  }
  .profile-meta-row {
    display: flex; flex-wrap: wrap; gap: 1.5rem;
    font-size: 0.85rem; color: var(--muted);
  }
  .profile-meta-item { display: flex; align-items: center; gap: 0.4rem; }
  .profile-meta-item strong { color: var(--cream); }

  .profile-actions { display: flex; flex-direction: column; gap: 0.75rem; align-items: flex-end; }
  .rating-big {
    text-align: center;
    background: rgba(212,137,26,0.08); border: 1px solid rgba(212,137,26,0.2);
    border-radius: 12px; padding: 1rem 1.5rem;
  }
  .rating-big .stars { color: var(--amber); font-size: 1.1rem; letter-spacing: 0.05em; }
  .rating-big .score { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 900; color: var(--amber-lt); line-height: 1; }
  .rating-big .reviews { font-size: 0.75rem; color: var(--muted); margin-top: 0.2rem; }

  /* ── TAB NAV ── */
  .tab-nav {
    max-width: 1400px; margin: 0 auto;
    display: flex; gap: 0; border-top: 1px solid var(--border);
  }
  .tab-nav-item {
    padding: 1rem 1.75rem; font-size: 0.88rem; font-weight: 600;
    color: var(--muted); cursor: pointer; border: none;
    background: transparent; font-family: 'DM Sans', sans-serif;
    border-bottom: 3px solid transparent; margin-bottom: -1px;
    transition: all 0.2s;
  }
  .tab-nav-item:hover { color: var(--cream); }
  .tab-nav-item.active { color: var(--amber-lt); border-bottom-color: var(--amber); }

  /* ── MAIN LAYOUT ── */
  .profile-layout {
    max-width: 1400px; margin: 0 auto;
    padding: 2.5rem 2rem 5rem;
    display: grid; grid-template-columns: 1fr 360px;
    gap: 2.5rem; position: relative; z-index: 1;
  }

  /* ── TAB PANELS ── */
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }

  /* ── SERVICES LISTING ── */
  .section-title-sm {
    font-family: 'Playfair Display', serif; font-size: 1.2rem;
    margin-bottom: 1.25rem;
  }
  .services-list { display: flex; flex-direction: column; gap: 1.25rem; }
  .service-item {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
    transition: border-color 0.2s;
    animation: fadeUp 0.4s ease both;
  }
  .service-item:hover { border-color: rgba(212,137,26,0.5); }
  .service-item:nth-child(1){animation-delay:0.05s}
  .service-item:nth-child(2){animation-delay:0.1s}
  .service-item:nth-child(3){animation-delay:0.15s}
  .service-item-inner { display: grid; grid-template-columns: 180px 1fr auto; gap: 0; }
  .service-ba {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 2px; height: 120px;
  }
  .service-ba-img {
    background-size: cover; background-position: center;
    position: relative;
  }
  .service-ba-label {
    position: absolute; bottom: 4px; left: 4px;
    font-size: 0.58rem; font-weight: 700; letter-spacing: 0.06em;
    text-transform: uppercase; background: rgba(22,19,16,0.8);
    padding: 0.1rem 0.35rem; border-radius: 3px;
  }
  .service-ba-label.after { left: auto; right: 4px; color: var(--amber-lt); }
  .service-body { padding: 1.1rem 1.25rem; }
  .service-cat { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--amber); margin-bottom: 0.3rem; }
  .service-title { font-family: 'Playfair Display', serif; font-size: 1rem; margin-bottom: 0.4rem; line-height: 1.3; }
  .service-desc { font-size: 0.8rem; color: var(--muted); line-height: 1.55; margin-bottom: 0.75rem; }
  .service-models { display: flex; flex-wrap: wrap; gap: 0.35rem; }
  .model-tag { background: var(--bg-card2); border: 1px solid var(--border); color: var(--muted); padding: 0.15rem 0.55rem; border-radius: 4px; font-size: 0.72rem; }
  .service-price-col { padding: 1.1rem 1.25rem; display: flex; flex-direction: column; align-items: flex-end; justify-content: space-between; border-left: 1px solid var(--border); min-width: 130px; }
  .service-price strong { font-size: 1.2rem; color: var(--amber-lt); }
  .service-price span { font-size: 0.75rem; color: var(--muted); }
  .btn-book-service {
    background: var(--amber); border: none; color: #161310;
    padding: 0.5rem 1rem; border-radius: 7px;
    font-size: 0.8rem; font-weight: 700; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: background 0.2s;
    white-space: nowrap;
  }
  .btn-book-service:hover { background: var(--amber-lt); }

  /* ── GALLERY TAB ── */
  .gallery-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.25rem;
  }
  .gallery-item {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 12px; overflow: hidden;
    transition: border-color 0.2s, transform 0.2s;
    animation: fadeUp 0.4s ease both;
  }
  .gallery-item:hover { border-color: var(--amber); transform: translateY(-3px); }
  .gallery-ba { display: grid; grid-template-columns: 1fr 1fr; height: 140px; gap: 2px; }
  .gallery-ba-img { background-size: cover; background-position: center; position: relative; }
  .gallery-caption { padding: 0.9rem 1rem; }
  .gallery-device { font-size: 0.88rem; font-weight: 600; margin-bottom: 0.2rem; }
  .gallery-meta { font-size: 0.75rem; color: var(--muted); display: flex; justify-content: space-between; }

  /* ── REVIEWS TAB ── */
  .reviews-summary {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; padding: 1.5rem;
    display: grid; grid-template-columns: auto 1fr;
    gap: 2rem; align-items: center; margin-bottom: 1.5rem;
  }
  .reviews-score-big {
    text-align: center; padding-right: 2rem;
    border-right: 1px solid var(--border);
  }
  .reviews-score-big .num { font-family: 'Playfair Display', serif; font-size: 3.5rem; font-weight: 900; color: var(--amber-lt); line-height: 1; }
  .reviews-score-big .stars { color: var(--amber); font-size: 1rem; margin: 0.25rem 0; }
  .reviews-score-big .total { font-size: 0.78rem; color: var(--muted); }
  .rating-bars { display: flex; flex-direction: column; gap: 0.5rem; }
  .rating-bar-row { display: flex; align-items: center; gap: 0.75rem; font-size: 0.8rem; }
  .rating-bar-label { color: var(--muted); width: 30px; text-align: right; flex-shrink: 0; }
  .rating-bar-track { flex: 1; height: 6px; background: var(--border); border-radius: 3px; overflow: hidden; }
  .rating-bar-fill { height: 100%; background: var(--amber); border-radius: 3px; }
  .rating-bar-count { color: var(--muted); width: 24px; flex-shrink: 0; }
  .reviews-list { display: flex; flex-direction: column; gap: 1rem; }
  .review-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 12px; padding: 1.25rem;
    animation: fadeUp 0.4s ease both;
  }
  .review-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.75rem; }
  .review-author { display: flex; align-items: center; gap: 0.6rem; }
  .review-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--bg-card2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; color: var(--amber); }
  .review-name { font-size: 0.88rem; font-weight: 600; }
  .review-device { font-size: 0.75rem; color: var(--muted); }
  .review-rating { color: var(--amber); font-size: 0.85rem; }
  .review-date { font-size: 0.75rem; color: var(--muted); }
  .review-text { font-size: 0.85rem; color: var(--muted); line-height: 1.65; }
  .review-verified { display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.7rem; color: #5de0b0; background: rgba(30,160,120,0.1); border: 1px solid rgba(30,160,120,0.2); padding: 0.15rem 0.5rem; border-radius: 20px; margin-top: 0.75rem; }

  /* ── BOOKING SIDEBAR ── */
  .sidebar { display: flex; flex-direction: column; gap: 1.25rem; }
  .side-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
    animation: fadeUp 0.4s ease both;
  }
  .side-card-header {
    padding: 1rem 1.25rem;
    background: var(--bg-card2); border-bottom: 1px solid var(--border);
    font-family: 'Playfair Display', serif; font-size: 1rem;
  }
  .side-card-body { padding: 1.25rem; }
  .avail-days { display: flex; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 1rem; }
  .avail-day {
    padding: 0.35rem 0.7rem; border-radius: 6px;
    border: 1px solid var(--border); font-size: 0.78rem;
    color: var(--muted);
  }
  .avail-day.open { border-color: rgba(212,137,26,0.4); color: var(--amber-lt); background: rgba(212,137,26,0.08); }
  .date-select-label { font-size: 0.78rem; font-weight: 600; margin-bottom: 0.4rem; display: block; color: var(--cream); }
  .date-select {
    width: 100%; background: var(--bg); border: 1px solid var(--border);
    border-radius: 8px; color: var(--cream); font-family: 'DM Sans', sans-serif;
    font-size: 0.88rem; padding: 0.7rem 1rem; outline: none; appearance: none;
    cursor: pointer; transition: border-color 0.2s; margin-bottom: 1rem;
  }
  .date-select:focus { border-color: var(--amber); }
  .turnaround-note { font-size: 0.78rem; color: var(--muted); margin-bottom: 1.25rem; line-height: 1.5; }
  .turnaround-note strong { color: var(--cream); }
  .btn-book-main {
    width: 100%; background: var(--amber); border: none; color: #161310;
    padding: 0.85rem; border-radius: 9px; font-weight: 700; font-size: 0.95rem;
    cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
  }
  .btn-book-main:hover { background: var(--amber-lt); }
  .btn-portfolio-main {
    width: 100%; background: transparent; border: 1px solid var(--border);
    color: var(--cream); padding: 0.75rem; border-radius: 9px;
    font-weight: 600; font-size: 0.88rem; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: all 0.2s; margin-top: 0.75rem;
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
  }
  .btn-portfolio-main:hover { border-color: var(--amber); color: var(--amber-lt); }
  .quick-stats { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0; }
  .quick-stat { text-align: center; padding: 1rem; border-right: 1px solid var(--border); }
  .quick-stat:last-child { border-right: none; }
  .quick-stat strong { display: block; font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--amber-lt); }
  .quick-stat span { font-size: 0.72rem; color: var(--muted); }

  /* image placeholders */
  .ba-synth-b { background: linear-gradient(135deg,#2a1f0e,#3d2b0f); }
  .ba-synth-a { background: linear-gradient(135deg,#1a2a1a,#233520); }
  .ba-radio-b  { background: linear-gradient(135deg,#1e1a2a,#2a2038); }
  .ba-radio-a  { background: linear-gradient(135deg,#0e1e2a,#122438); }
  .ba-hifi-b   { background: linear-gradient(135deg,#201a0a,#30250e); }
  .ba-hifi-a   { background: linear-gradient(135deg,#0a1a20,#0e2530); }
  .ba-game-b   { background: linear-gradient(135deg,#2a1a1a,#3a1e1e); }
  .ba-game-a   { background: linear-gradient(135deg,#1a2a28,#1e3530); }

  /* modal */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:999; align-items:center; justify-content:center; padding:1rem; }
  .modal-overlay.open { display:flex; }
  .modal-box { background:var(--bg-card); border:1px solid var(--amber); border-radius:16px; padding:2rem; max-width:440px; width:100%; animation:fadeUp 0.3s ease; }
  .modal-box h2 { font-family:'Playfair Display',serif; font-size:1.3rem; margin-bottom:0.5rem; }
  .modal-box p { font-size:0.85rem; color:var(--muted); margin-bottom:1.5rem; line-height:1.6; }
  .modal-btns { display:flex; gap:0.75rem; }
  .btn-modal-cancel { flex:1; background:transparent; border:1px solid var(--border); color:var(--muted); padding:0.7rem; border-radius:8px; cursor:pointer; font-family:'DM Sans',sans-serif; }
  .btn-modal-confirm { flex:2; background:var(--amber); border:none; color:#161310; padding:0.7rem; border-radius:8px; font-weight:700; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all 0.2s; }
  .btn-modal-confirm:hover { background:var(--amber-lt); }
  .btn-modal-confirm:disabled { opacity:0.7; cursor:not-allowed; }

  @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:768px) { .profile-layout{grid-template-columns:1fr} .service-item-inner{grid-template-columns:1fr} .service-ba{height:120px} .service-price-col{border-left:none;border-top:1px solid var(--border);flex-direction:row;align-items:center;justify-content:space-between} }
  @media(max-width:768px) { .profile-header{grid-template-columns:auto 1fr;} .profile-actions{display:none} .profile-hero-inner{padding:2rem 1rem 0} .profile-layout{padding:1.5rem 1rem 3rem} }
  @media(max-width:600px) { .tab-nav-item{padding:0.75rem 1rem;font-size:0.8rem} }
</style>
@endsection

@section('content')

{{-- PROFILE HERO --}}
<div class="profile-hero">
  <div class="profile-hero-inner">
    <a href="/browse" class="back-link">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Browse
    </a>

    <div class="profile-header">
      {{-- AVATAR --}}
      <div class="profile-avatar-wrap">
        <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
        <div class="verified-badge">✓</div>
      </div>

      {{-- INFO --}}
      <div class="profile-info">
        <h1>{{ $user->name }}</h1>
        <p class="profile-tagline">{{ $profile->specialisation ?? 'Electronics Restoration Specialist' }}{{ $profile->location ? ' · '.$profile->location : '' }}</p>
        <div class="profile-tags">
          @foreach($listings->pluck('category')->unique() as $cat)
          <span class="profile-tag">{{ $cat }}</span>
          @endforeach
          <span class="profile-tag">Verified Technician</span>
          <span class="profile-tag">Member since {{ $user->created_at->format('Y') }}</span>
        </div>
        <div class="profile-meta-row">
          <div class="profile-meta-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ $profile->location ?? 'Location not set' }}
          </div>
          <div class="profile-meta-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Member since <strong>{{ $user->created_at->format('Y') }}</strong>
          </div>
          <div class="profile-meta-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <strong>{{ $profile->completed_jobs_count }}</strong> jobs completed
          </div>
        </div>
      </div>

      {{-- RATING + ACTIONS --}}
      <div class="profile-actions">
        <div class="rating-big">
          <div class="stars">{{ str_repeat('★', (int)round($profile->avg_rating)) }}{{ str_repeat('☆', 5 - (int)round($profile->avg_rating)) }}</div>
          <div class="score">{{ number_format($profile->avg_rating, 1) }}</div>
          <div class="reviews">from {{ $reviews->count() }} reviews</div>
        </div>
      </div>
    </div>

    {{-- TAB NAV --}}
    <div class="tab-nav">
      <button class="tab-nav-item active" onclick="switchTab('services', this)">Services</button>
      <button class="tab-nav-item" onclick="switchTab('gallery', this)">Portfolio Gallery</button>
      <button class="tab-nav-item" onclick="switchTab('reviews', this)">Reviews ({{ $reviews->count() }})</button>
    </div>
  </div>
</div>

{{-- MAIN LAYOUT --}}
<div class="profile-layout">

  {{-- LEFT: TAB PANELS --}}
  <div>

    {{-- SERVICES TAB --}}
    <div class="tab-panel active" id="tab-services">
      <div class="section-title-sm">Active Service Listings</div>
      <div class="services-list">

        @forelse($listings as $listing)
        <div class="service-item">
          <div class="service-item-inner">
            <div class="service-ba">
              <div class="service-ba-img" style="background-image:url('{{ asset('storage/' . $listing->before_image) }}');background-size:cover;background-position:center;"><span class="service-ba-label">Before</span></div>
              <div class="service-ba-img" style="background-image:url('{{ asset('storage/' . $listing->after_image) }}');background-size:cover;background-position:center;"><span class="service-ba-label after">After</span></div>
            </div>
            <div class="service-body">
              <div class="service-cat">{{ $listing->category }}</div>
              <div class="service-title">{{ $listing->title }}</div>
              <p class="service-desc">{{ Str::limit($listing->description, 160) }}</p>
              <div class="service-models">
                @foreach($listing->supported_models ?? [] as $model)
                  <span class="model-tag">{{ $model }}</span>
                @endforeach
              </div>
            </div>
            <div class="service-price-col">
              <div class="service-price"><strong>৳{{ number_format($listing->price_min, 0) }}</strong><span><br/>– ৳{{ number_format($listing->price_max, 0) }}</span></div>
              @auth
              @if(!auth()->user()->isTechnician())
              <button class="btn-book-service" onclick="selectService({{ $listing->id }}, '{{ addslashes($listing->title) }}', {{ $listing->price_min }}, {{ $listing->price_max }}, {!! json_encode($listing->available_days ?? []) !!})">Book Slot</button>
              @endif
              @endauth
            </div>
          </div>
        </div>
        @empty
        <div style="padding:2rem;text-align:center;color:var(--muted);font-size:0.88rem;">No active listings yet.</div>
        @endforelse
      </div>
      <div style="text-align:center;margin-top:2rem;">
        <a href="/technicians/{{ $user->id }}/portfolio" style="display:inline-flex;align-items:center;gap:0.5rem;border:1px solid var(--border);color:var(--cream);padding:0.75rem 1.75rem;border-radius:10px;font-size:0.88rem;font-weight:600;text-decoration:none;transition:all 0.2s;" onmouseover="this.style.borderColor='var(--amber)';this.style.color='var(--amber-lt)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--cream)'">
          View Full Portfolio ({{ $profile->completed_jobs_count }} jobs) →
        </a>
      </div>
    </div>

    {{-- PORTFOLIO GALLERY TAB --}}
    <div class="tab-panel" id="tab-gallery">
      @php
        $galJobs = \App\Models\Job::whereHas('booking', fn($q) => $q->where('technician_profile_id', $profile->id))
            ->where('status', 'completed')
            ->with(['booking' => fn($q) => $q->with('serviceListing')])
            ->latest('updated_at')
            ->get();
      @endphp
      @if($galJobs->isEmpty())
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:14px;padding:3rem;text-align:center;color:var(--muted);">
          <div style="font-size:2rem;margin-bottom:0.75rem;">🔧</div>
          <p style="font-size:0.9rem;">No completed restorations yet.</p>
        </div>
      @else
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.25rem;">
        @foreach($galJobs as $gJob)
        @php
          $gListing  = $gJob->booking->serviceListing ?? null;
          $gCategory = $gListing->category ?? 'Restoration';
          $gDevice   = $gJob->booking->device_name ?? 'Device';
          $gBefore   = ($gListing && $gListing->before_image)
              ? 'url(' . json_encode(asset('storage/'.$gListing->before_image)) . ')'
              : 'none';
          $gAfter    = ($gListing && $gListing->after_image)
              ? 'url(' . json_encode(asset('storage/'.$gListing->after_image)) . ')'
              : 'none';
        @endphp
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;overflow:hidden;">
          <div style="display:grid;grid-template-columns:1fr 1fr;height:140px;">
            <div style="background:var(--bg-card2);background-image:{{ $gBefore }};background-size:cover;background-position:center;position:relative;">
              <span style="position:absolute;bottom:4px;left:6px;font-size:0.6rem;font-weight:700;background:rgba(0,0,0,0.6);color:var(--muted);padding:2px 6px;border-radius:4px;">BEFORE</span>
            </div>
            <div style="background:var(--bg-card2);background-image:{{ $gAfter }};background-size:cover;background-position:center;position:relative;">
              <span style="position:absolute;bottom:4px;right:6px;font-size:0.6rem;font-weight:700;background:rgba(212,137,26,0.7);color:#161310;padding:2px 6px;border-radius:4px;">AFTER</span>
            </div>
          </div>
          <div style="padding:0.9rem 1rem;">
            <div style="font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--amber-lt);margin-bottom:0.25rem;">{{ $gCategory }}</div>
            <div style="font-size:0.88rem;font-weight:600;color:var(--cream);">{{ $gDevice }}</div>
            <div style="font-size:0.75rem;color:var(--muted);margin-top:0.2rem;">{{ $gJob->updated_at->format('M Y') }}</div>
          </div>
        </div>
        @endforeach
      </div>
      <div style="text-align:center;margin-top:1.5rem;">
        <a href="/technicians/{{ $user->id }}/portfolio"
           style="display:inline-flex;align-items:center;gap:0.5rem;border:1px solid var(--border);color:var(--cream);padding:0.65rem 1.5rem;border-radius:9px;font-size:0.85rem;font-weight:600;text-decoration:none;"
           onmouseover="this.style.borderColor='var(--amber)';this.style.color='var(--amber-lt)'"
           onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--cream)'">
          View Full Portfolio &rarr;
        </a>
      </div>
      @endif
    </div>

    {{-- REVIEWS TAB --}}
    <div class="tab-panel" id="tab-reviews">
      <div class="reviews-summary">
        <div class="reviews-score-big">
          <div class="num">{{ number_format($profile->avg_rating, 1) }}</div>
          <div class="stars">{{ str_repeat('★',(int)round($profile->avg_rating)).str_repeat('☆',5-(int)round($profile->avg_rating)) }}</div>
          <div class="total">{{ $reviews->count() }} reviews</div>
        </div>
        <div class="rating-bars">
          @foreach([5,4,3,2,1] as $star)
          @php $rb = $ratingBreakdown[$star]; $barColor = $star>=4?'var(--amber)':($star==3?'#e09030':'#e06060'); @endphp
          <div class="rating-bar-row">
            <span class="rating-bar-label">{{ $star }}★</span>
            <div class="rating-bar-track"><div class="rating-bar-fill" style="width:{{ $rb['pct'] }}%;background:{{ $barColor }}"></div></div>
            <span class="rating-bar-count">{{ $rb['count'] }}</span>
          </div>
          @endforeach
        </div>
      </div>

      <div class="reviews-list">
        @forelse($reviews as $review)
        @php
          $collName   = $review->collector->name ?? 'Collector';
          $collInit   = strtoupper(substr($collName, 0, 1));
          $deviceName = $review->job->booking->device_name ?? '';
          $stars      = str_repeat('★',$review->rating).str_repeat('☆',5-$review->rating);
          $dateStr    = $review->created_at->format('F Y');
        @endphp
        <div class="review-card">
          <div class="review-header">
            <div class="review-author">
              <div class="review-avatar">{{ $collInit }}</div>
              <div>
                <div class="review-name">{{ $collName }}</div>
                @if($deviceName)<div class="review-device">{{ $deviceName }}</div>@endif
              </div>
            </div>
            <div style="text-align:right;">
              <div class="review-rating">{{ $stars }}</div>
              <div class="review-date">{{ $dateStr }}</div>
            </div>
          </div>
          <p class="review-text">{{ $review->comment }}</p>
          <div class="review-verified">✓ Verified Purchase</div>
        </div>
        @empty
        <div style="padding:2rem;text-align:center;color:var(--muted);font-size:0.88rem;">No reviews yet.</div>
        @endforelse
      
    </div>

  </div>

  {{-- RIGHT: BOOKING SIDEBAR --}}
  <aside class="sidebar" id="profile-sidebar">

    {{-- QUICK STATS --}}
    <div class="side-card">
      <div class="quick-stats">
        <div class="quick-stat"><strong>{{ $profile->completed_jobs_count }}</strong><span>Jobs Done</span></div>
        <div class="quick-stat"><strong>{{ number_format($profile->avg_rating, 1) }}★</strong><span>Rating</span></div>
        <div class="quick-stat"><strong>{{ $listings->count() }}</strong><span>Listings</span></div>
      </div>
    </div>

    {{-- BOOK A SLOT: only for logged-in collectors --}}
    @auth
    @if(!auth()->user()->isTechnician())
    <div class="side-card" id="booking-card">
      <div class="side-card-header">📅 Book a Repair Slot</div>
      <div class="side-card-body">
        <div style="margin-bottom:0.75rem;">
          <span style="font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:0.5rem;">Available Days</span>
          <div class="avail-days">
            @php $availDays = $profile->availability_windows['days'] ?? []; @endphp
            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
              <span class="avail-day{{ in_array($day, $availDays) ? ' open' : '' }}">{{ $day }}</span>
            @endforeach
          </div>
        </div>
        <label class="date-select-label">Select a Start Date</label>
        <select class="date-select" id="booking-date">
          <option value="" disabled selected>Choose available date…</option>
          @forelse($availableDates as $date)
            <option value="{{ $date }}">{{ $date }}</option>
          @empty
            <option disabled>No available dates in next 30 days</option>
          @endforelse
        </select>
        <div id="date-error" style="display:none;font-size:0.78rem;color:#f09090;margin-bottom:0.5rem;padding:0.5rem 0.75rem;background:rgba(200,60,60,0.08);border:1px solid rgba(200,60,60,0.2);border-radius:6px;">
          ⚠️ Please select an available date before requesting a slot.
        </div>
        <div style="margin-bottom:0.9rem;">
          <label class="date-select-label">Your Device Name *</label>
          <input type="text" id="booking-device-name" placeholder="e.g. Roland Juno-106"
            style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--cream);font-family:'DM Sans',sans-serif;font-size:0.88rem;padding:0.7rem 1rem;outline:none;transition:border-color 0.2s;box-sizing:border-box;"
            onfocus="this.style.borderColor='var(--amber)'" onblur="this.style.borderColor=''"/>
        </div>
        <div style="margin-bottom:1rem;">
          <label class="date-select-label">Fault Description (optional)</label>
          <textarea id="booking-device-desc" rows="2" placeholder="Briefly describe the fault…"
            style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--cream);font-family:'DM Sans',sans-serif;font-size:0.85rem;padding:0.7rem 1rem;outline:none;resize:vertical;transition:border-color 0.2s;box-sizing:border-box;"
            onfocus="this.style.borderColor='var(--amber)'" onblur="this.style.borderColor=''"></textarea>
        </div>
        <button class="btn-book-main" onclick="openBooking()">
          Request Booking Slot
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <a href="/technicians/{{ $user->id }}/portfolio" class="btn-portfolio-main">
          View Full Portfolio
        </a>
      </div>
    </div>

    @endif
    @endauth

    {{-- RESPONSE NOTE --}}
    <div class="side-card">
      <div class="side-card-body" style="padding:1rem 1.25rem;">
        <div style="display:flex;align-items:flex-start;gap:0.75rem;">
          <span style="font-size:1.2rem;flex-shrink:0;">⚡</span>
          <div>
            <div style="font-size:0.82rem;font-weight:600;margin-bottom:0.25rem;">Fast Responder</div>
            <div style="font-size:0.78rem;color:var(--muted);line-height:1.5;">{{ $user->name }} typically responds within <strong style="color:var(--cream)">24 hours</strong>. You'll receive an SMS alert if your confirmed slot is cancelled.</div>
          </div>
        </div>
      </div>
    </div>

  </aside>
</div>

{{-- BOOKING CONFIRMATION MODAL --}}
<div class="modal-overlay" id="booking-modal">
  <div class="modal-box">
    <div id="modal-step-confirm">
      <h2>Confirm Booking Request</h2>
      <p style="font-size:0.88rem;color:var(--muted);margin-bottom:1.25rem;">You're sending a repair slot request to <strong style="color:var(--cream)">{{ $user->name }}</strong></p>
      <div style="background:var(--bg-card2);border:1px solid var(--border);border-radius:10px;padding:1rem;margin-bottom:1.25rem;">
        <div style="font-size:0.78rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);margin-bottom:0.75rem;">Request Summary</div>
        <div style="font-size:0.85rem;display:flex;flex-direction:column;gap:0.4rem;">
          <div style="display:flex;justify-content:space-between;"><span style="color:var(--muted);">Technician</span><span style="font-weight:600;">{{ $user->name }}{{ $profile->location ? ' · '.$profile->location : '' }}</span></div>
          <div style="display:flex;justify-content:space-between;"><span style="color:var(--muted);">Requested Date</span><span style="font-weight:600;" id="modal-date">—</span></div>
          <div style="display:flex;justify-content:space-between;"><span style="color:var(--muted);">Service</span><span style="font-weight:600;font-size:0.82rem;text-align:right;max-width:180px;" id="modal-service">—</span></div>
          <div style="display:flex;justify-content:space-between;"><span style="color:var(--muted);">Device</span><span style="font-weight:600;" id="modal-device-name">—</span></div>
          <div style="display:flex;justify-content:space-between;"><span style="color:var(--muted);">Est. Price Range</span><span style="font-weight:600;" id="modal-price">—</span></div>
        </div>
      </div>
      <div style="background:rgba(212,137,26,0.06);border:1px solid rgba(212,137,26,0.2);border-radius:8px;padding:0.85rem;margin-bottom:1.5rem;font-size:0.8rem;color:var(--muted);line-height:1.6;">
        ⏳ <strong style="color:var(--amber-lt);">Awaiting Technician Approval</strong> — {{ explode(' ', $user->name)[0] }} will review your request and either accept or decline based on workbench availability. Payment is only taken after acceptance.
      </div>
      <div class="modal-btns">
        <button class="btn-modal-cancel" onclick="document.getElementById('booking-modal').classList.remove('open')">Go Back</button>
        <button class="btn-modal-confirm" id="btn-confirm-booking" onclick="confirmBooking()">Send Request →</button>
      </div>
    </div>

    {{-- STEP: CONFLICT CHECKING --}}
    <div id="modal-step-checking" style="display:none;text-align:center;padding:1rem 0;">
      <div style="width:48px;height:48px;border:4px solid var(--border);border-top-color:var(--amber);border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 1.25rem;"></div>
      <div style="font-family:'Playfair Display',serif;font-size:1.1rem;margin-bottom:0.4rem;">Checking availability…</div>
      <div style="font-size:0.82rem;color:var(--muted);">Running conflict check against existing bookings</div>
    </div>

    {{-- STEP: PENDING SENT --}}
    <div id="modal-step-pending" style="display:none;text-align:center;">
      <div style="font-size:2.5rem;margin-bottom:0.75rem;">📋</div>
      <div style="font-family:'Playfair Display',serif;font-size:1.25rem;margin-bottom:0.5rem;color:var(--amber-lt);">Request Sent — Pending Approval</div>
      <p style="font-size:0.82rem;color:var(--muted);line-height:1.6;margin-bottom:1.25rem;">
        No date conflicts found. Your request has been saved as <strong style="color:var(--amber-lt);">Pending</strong>. {{ $user->name }} will respond within 24 hours. You'll be notified once they respond.
      </p>
      <div style="background:var(--bg-card2);border:1px solid var(--border);border-radius:10px;padding:1rem;margin-bottom:1.5rem;text-align:left;">
        <div style="font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);margin-bottom:0.6rem;">Booking Status</div>
        <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.88rem;">
          <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--amber);animation:pulse 2s infinite;"></span>
          <strong style="color:var(--amber-lt);">PENDING</strong>
          <span style="color:var(--muted);">— awaiting {{ explode(' ', $user->name)[0] }}'s approval</span>
        </div>
        <div style="font-size:0.78rem;color:var(--muted);margin-top:0.5rem;">Requested Date: <strong style="color:var(--cream)" id="pending-date-display">—</strong></div>
      </div>
      <button onclick="document.getElementById('booking-modal').classList.remove('open')"
        style="background:var(--amber);border:none;color:#161310;padding:0.75rem 2rem;border-radius:8px;font-weight:700;font-size:0.9rem;cursor:pointer;font-family:'DM Sans',sans-serif;">
        OK, I'll Wait
      </button>
    </div>
  </div>
</div>

<script>
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // Hidden field to track which service listing was selected
  let selectedListingId   = null;
  let selectedListingTitle = null;
  let selectedPriceMin    = null;
  let selectedPriceMax    = null;

  // Day-name map used to generate available dates client-side
  const DAY_MAP = { Sun:0, Mon:1, Tue:2, Wed:3, Thu:4, Fri:5, Sat:6 };

  // Build date dropdown options from a days array (e.g. ['Tue','Thu'])
  function buildDateOptions(days) {
    const select = document.getElementById('booking-date');
    select.innerHTML = '<option value="" disabled selected>Choose available date…</option>';
    if (!days || days.length === 0) {
      select.innerHTML += '<option disabled>No available dates set</option>';
      return;
    }
    const dayNums = days.map(d => DAY_MAP[d]).filter(n => n !== undefined);
    const today = new Date();
    let added = 0;
    for (let i = 1; i <= 60 && added < 20; i++) {
      const d = new Date(today);
      d.setDate(today.getDate() + i);
      if (dayNums.includes(d.getDay())) {
        const label = d.toLocaleDateString('en-US', { weekday:'long', month:'short', day:'numeric', year:'numeric' });
        const val   = d.toLocaleDateString('en-US', { weekday:'long', month:'short', day:'numeric', year:'numeric' });
        const opt   = document.createElement('option');
        opt.value   = val;
        opt.textContent = label;
        select.appendChild(opt);
        added++;
      }
    }
    if (added === 0) select.innerHTML += '<option disabled>No available dates in next 60 days</option>';
  }

  // Update the avail-day chips in the booking sidebar
  function updateDayChips(days) {
    document.querySelectorAll('.avail-day').forEach(chip => {
      const d = chip.textContent.trim();
      if (days && days.includes(d)) {
        chip.classList.add('open');
      } else {
        chip.classList.remove('open');
      }
    });
  }

  // Called when collector clicks "Book Slot" on a service card
  function selectService(id, title, priceMin, priceMax, days) {
    selectedListingId    = id;
    selectedListingTitle = title;
    selectedPriceMin     = priceMin;
    selectedPriceMax     = priceMax;

    // Update booking form for this specific listing
    updateDayChips(days || []);
    buildDateOptions(days || []);

    // Scroll to sidebar booking widget smoothly
    document.getElementById('booking-card').scrollIntoView({ behavior: 'smooth', block: 'center' });
    // Highlight the date selector
    const ds = document.getElementById('booking-date');
    ds.style.borderColor = 'var(--amber)';
    setTimeout(() => ds.style.borderColor = '', 2000);
  }

  function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-nav-item').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
    // Only show sidebar on Services tab
    const sidebar = document.getElementById('profile-sidebar');
    const layout  = document.querySelector('.profile-layout');
    const isDesktop = window.innerWidth > 1024;
    if (name === 'services') {
      sidebar.style.display = '';
      if (isDesktop) layout.style.gridTemplateColumns = '';
    } else {
      sidebar.style.display = 'none';
      layout.style.gridTemplateColumns = '1fr';
    }
  }

  function openBooking() {
    const dateSelect  = document.getElementById('booking-date');
    const deviceName  = document.getElementById('booking-device-name');
    const errorEl     = document.getElementById('date-error');

    // Validate date
    if (!dateSelect.value) {
      errorEl.style.display = 'block';
      dateSelect.style.borderColor = 'rgba(200,60,60,0.5)';
      return;
    }
    // Validate device name
    if (!deviceName.value.trim()) {
      deviceName.style.borderColor = 'rgba(200,60,60,0.5)';
      deviceName.focus();
      return;
    }
    errorEl.style.display   = 'none';
    dateSelect.style.borderColor = '';
    deviceName.style.borderColor = '';

    // Populate modal summary
    document.getElementById('modal-date').textContent        = dateSelect.value;
    document.getElementById('modal-device-name').textContent = deviceName.value.trim();
    document.getElementById('pending-date-display').textContent = dateSelect.value;

    if (selectedListingTitle) {
      document.getElementById('modal-service').textContent = selectedListingTitle;
      document.getElementById('modal-price').textContent =
        '৳' + Math.round(selectedPriceMin).toLocaleString() + ' – ৳' + Math.round(selectedPriceMax).toLocaleString();
    } else {
      document.getElementById('modal-service').textContent = 'Any available service';
      document.getElementById('modal-price').textContent   = 'To be confirmed';
    }

    // Reset modal to confirm step
    document.getElementById('modal-step-confirm').style.display   = '';
    document.getElementById('modal-step-checking').style.display  = 'none';
    document.getElementById('modal-step-pending').style.display   = 'none';
    const btn = document.getElementById('btn-confirm-booking');
    btn.disabled    = false;
    btn.textContent = 'Send Request →';

    document.getElementById('booking-modal').classList.add('open');
  }

  function confirmBooking() {
    const btn         = document.getElementById('btn-confirm-booking');
    const dateSelect  = document.getElementById('booking-date');
    const deviceName  = document.getElementById('booking-device-name');
    const deviceDesc  = document.getElementById('booking-device-desc');

    btn.disabled = true;

    // Show spinner
    document.getElementById('modal-step-confirm').style.display  = 'none';
    document.getElementById('modal-step-checking').style.display = 'block';

    // Parse date back to Y-m-d for the server
    const rawDate     = dateSelect.value;
    const parsedDate  = new Date(rawDate);
    const yyyy = parsedDate.getFullYear();
    const mm   = String(parsedDate.getMonth() + 1).padStart(2, '0');
    const dd   = String(parsedDate.getDate()).padStart(2, '0');
    const isoDate = yyyy + '-' + mm + '-' + dd;

    fetch('/bookings', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({
        service_listing_id: selectedListingId,
        requested_date:     isoDate,
        device_name:        deviceName.value.trim(),
        device_description: deviceDesc ? deviceDesc.value.trim() : '',
      }),
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('modal-step-checking').style.display = 'none';
      if (data.success) {
        document.getElementById('modal-step-pending').style.display = 'block';
      } else if (data.conflict) {
        // Date conflict — go back to confirm step and show error
        document.getElementById('modal-step-confirm').style.display = '';
        btn.disabled = false;
        btn.textContent = 'Send Request →';
        const errorEl = document.getElementById('date-error');
        errorEl.textContent = '⚠️ ' + data.message;
        errorEl.style.display = 'block';
        document.getElementById('booking-modal').classList.remove('open');
      } else {
        alert(data.message || 'Could not send booking request. Please try again.');
        document.getElementById('modal-step-confirm').style.display = '';
        btn.disabled = false;
      }
    })
    .catch(() => {
      document.getElementById('modal-step-checking').style.display = 'none';
      document.getElementById('modal-step-confirm').style.display = '';
      btn.disabled = false;
      alert('Network error. Please try again.');
    });
  }

  document.getElementById('booking-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
  });

  document.getElementById('booking-date').addEventListener('change', function() {
    document.getElementById('date-error').style.display = 'none';
    this.style.borderColor = '';
  });

  // ── AUTO-SELECT LISTING FROM URL (?listing=ID) ───────────
  // Embed all listing data for client-side lookup
  const listingData = {
    @foreach($listings as $l)
    {{ $l->id }}: { title: '{{ addslashes($l->title) }}', priceMin: {{ $l->price_min }}, priceMax: {{ $l->price_max }}, days: {!! json_encode($l->available_days ?? []) !!} },
    @endforeach
  };

  (function autoSelectFromUrl() {
    const params = new URLSearchParams(window.location.search);
    const lid    = parseInt(params.get('listing'));
    if (!lid || !listingData[lid]) return;
    const d = listingData[lid];
    // Slight delay so the page has fully rendered
    setTimeout(() => selectService(lid, d.title, d.priceMin, d.priceMax, d.days), 300);
  })();
</script>
@endsection