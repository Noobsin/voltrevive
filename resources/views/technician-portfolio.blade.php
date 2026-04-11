@extends('layout')
@section('title', '{{ $user->name }} — Full Portfolio')

@section('styles')
<style>
  /* ── PORTFOLIO HERO ── */
  .port-hero {
    background: var(--bg-card); border-bottom: 1px solid var(--border);
    position: relative; z-index: 1;
  }
  .port-hero-inner {
    max-width: 1400px; margin: 0 auto; padding: 2.5rem 2rem 2rem;
  }
  .back-link {
    display: inline-flex; align-items: center; gap: 0.4rem;
    color: var(--muted); text-decoration: none; font-size: 0.85rem;
    margin-bottom: 2rem; transition: color 0.2s;
  }
  .back-link:hover { color: var(--cream); }
  .port-header {
    display: flex; align-items: center; gap: 2rem;
    flex-wrap: wrap;
  }
  .port-avatar {
    width: 90px; height: 90px; border-radius: 50%;
    background: linear-gradient(135deg, #2a1f0e, #3d2b14);
    border: 3px solid var(--amber);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif;
    font-size: 2.2rem; font-weight: 900; color: var(--amber-lt);
    flex-shrink: 0; position: relative;
  }
  .port-avatar-verified {
    position: absolute; bottom: 2px; right: 2px;
    width: 24px; height: 24px; border-radius: 50%;
    background: var(--amber); border: 2px solid var(--bg-card);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.65rem;
  }
  .port-info { flex: 1; }
  .port-info h1 { font-size: clamp(1.6rem, 3vw, 2.2rem); margin-bottom: 0.25rem; }
  .port-info p { color: var(--muted); font-size: 0.9rem; margin-bottom: 0.75rem; }
  .port-tags { display: flex; flex-wrap: wrap; gap: 0.4rem; }
  .port-tag {
    background: rgba(212,137,26,0.1); border: 1px solid rgba(212,137,26,0.25);
    color: var(--amber-lt); padding: 0.2rem 0.65rem;
    border-radius: 20px; font-size: 0.72rem; font-weight: 600;
  }
  .port-stats {
    display: flex; gap: 1px; background: var(--border);
    border: 1px solid var(--border); border-radius: 12px; overflow: hidden;
    flex-shrink: 0;
  }
  .port-stat {
    background: var(--bg-card2); padding: 1rem 1.5rem;
    text-align: center; min-width: 100px;
  }
  .port-stat strong {
    display: block; font-family: 'Playfair Display', serif;
    font-size: 1.6rem; color: var(--amber-lt); line-height: 1;
  }
  .port-stat span { font-size: 0.72rem; color: var(--muted); }

  /* ── FILTER BAR ── */
  .filter-bar {
    max-width: 1400px; margin: 0 auto;
    padding: 1.5rem 2rem; position: relative; z-index: 1;
    display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
    border-bottom: 1px solid var(--border);
    background: var(--bg-card);
  }
  .filter-bar-label {
    font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--muted); flex-shrink: 0;
  }
  .filter-chips { display: flex; gap: 0.5rem; flex-wrap: wrap; flex: 1; }
  .filter-chip {
    padding: 0.4rem 1rem; border-radius: 20px;
    border: 1px solid var(--border); font-size: 0.82rem;
    cursor: pointer; transition: all 0.15s; color: var(--muted);
    background: transparent; font-family: 'DM Sans', sans-serif;
  }
  .filter-chip:hover { border-color: var(--amber); color: var(--amber-lt); }
  .filter-chip.active {
    background: rgba(212,137,26,0.12); border-color: var(--amber);
    color: var(--amber-lt); font-weight: 600;
  }
  .sort-select {
    background: var(--bg); border: 1px solid var(--border);
    color: var(--cream); padding: 0.45rem 0.9rem; border-radius: 7px;
    font-size: 0.82rem; font-family: 'DM Sans', sans-serif;
    cursor: pointer; outline: none; appearance: none; flex-shrink: 0;
  }

  /* ── GALLERY GRID ── */
  .gallery-wrap {
    max-width: 1400px; margin: 0 auto;
    padding: 2.5rem 2rem 5rem; position: relative; z-index: 1;
  }
  .gallery-results {
    font-size: 0.85rem; color: var(--muted); margin-bottom: 1.5rem;
  }
  .gallery-results strong { color: var(--cream); }

  .portfolio-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
  }
  .portfolio-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
    transition: border-color 0.25s, transform 0.25s;
    animation: fadeUp 0.4s ease both;
    cursor: pointer;
  }
  .portfolio-card:hover { border-color: var(--amber); transform: translateY(-4px); }

  /* staggered animation */
  .portfolio-card:nth-child(1){animation-delay:0.04s}
  .portfolio-card:nth-child(2){animation-delay:0.08s}
  .portfolio-card:nth-child(3){animation-delay:0.12s}
  .portfolio-card:nth-child(4){animation-delay:0.16s}
  .portfolio-card:nth-child(5){animation-delay:0.20s}
  .portfolio-card:nth-child(6){animation-delay:0.24s}
  .portfolio-card:nth-child(7){animation-delay:0.28s}
  .portfolio-card:nth-child(8){animation-delay:0.32s}
  .portfolio-card:nth-child(9){animation-delay:0.36s}

  /* Before/After slider on card */
  .card-ba {
    position: relative; height: 190px; overflow: hidden;
    cursor: ew-resize;
  }
  .card-ba-before, .card-ba-after {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
  }
  .card-ba-after {
    clip-path: inset(0 50% 0 0);
    transition: clip-path 0.3s ease;
  }
  .portfolio-card:hover .card-ba-after { clip-path: inset(0 25% 0 0); }
  .card-ba-line {
    position: absolute; left: 50%; top: 0; bottom: 0;
    width: 2px; background: var(--amber);
    transform: translateX(-50%); pointer-events: none;
    transition: left 0.3s ease;
  }
  .portfolio-card:hover .card-ba-line { left: 75%; }
  .ba-pill {
    position: absolute; bottom: 10px;
    background: rgba(22,19,16,0.82); backdrop-filter: blur(4px);
    padding: 0.18rem 0.55rem; border-radius: 4px;
    font-size: 0.62rem; font-weight: 700; letter-spacing: 0.08em;
    text-transform: uppercase;
  }
  .ba-pill.before { left: 10px; color: var(--muted); }
  .ba-pill.after  { right: 10px; color: var(--amber-lt); }
  .cat-badge {
    position: absolute; top: 10px; left: 50%;
    transform: translateX(-50%);
    background: rgba(22,19,16,0.8); backdrop-filter: blur(6px);
    border: 1px solid rgba(212,137,26,0.3);
    color: var(--amber-lt); padding: 0.18rem 0.65rem;
    border-radius: 20px; font-size: 0.65rem; font-weight: 700;
    white-space: nowrap;
  }

  .card-body { padding: 1.1rem 1.25rem; }
  .card-device { font-family: 'Playfair Display', serif; font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem; }
  .card-desc { font-size: 0.78rem; color: var(--muted); line-height: 1.5; margin-bottom: 0.85rem; }
  .card-footer-row {
    display: flex; align-items: center; justify-content: space-between;
    padding-top: 0.75rem; border-top: 1px solid var(--border);
  }
  .card-date { font-size: 0.75rem; color: var(--muted); }
  .card-rating { font-size: 0.8rem; color: var(--amber); font-weight: 600; }

  /* image colour sets */
  .im-sb { background: linear-gradient(135deg,#2a1f0e,#3d2b0f); }
  .im-sa { background: linear-gradient(135deg,#1a2a1a,#233520); }
  .im-rb { background: linear-gradient(135deg,#1e1a2a,#2a2038); }
  .im-ra { background: linear-gradient(135deg,#0e1e2a,#122438); }
  .im-hb { background: linear-gradient(135deg,#201a0a,#30250e); }
  .im-ha { background: linear-gradient(135deg,#0a1a20,#0e2530); }
  .im-gb { background: linear-gradient(135deg,#2a1a1a,#3a1e1e); }
  .im-ga { background: linear-gradient(135deg,#1a2a28,#1e3530); }

  /* ── LIGHTBOX ── */
  .lightbox {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.93); z-index: 999;
    align-items: center; justify-content: center;
    padding: 1rem;
  }
  .lightbox.open { display: flex; }
  .lightbox-inner {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 18px; max-width: 700px; width: 100%;
    animation: fadeUp 0.3s ease; overflow: hidden;
  }
  .lightbox-ba { display: grid; grid-template-columns: 1fr 1fr; height: 280px; gap: 3px; }
  .lightbox-img { background-size: cover; background-position: center; position: relative; }
  .lightbox-label {
    position: absolute; bottom: 10px; left: 10px;
    background: rgba(22,19,16,0.85); padding: 0.25rem 0.65rem;
    border-radius: 5px; font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
  }
  .lightbox-label.after { left: auto; right: 10px; color: var(--amber-lt); }
  .lightbox-body { padding: 1.5rem 2rem; }
  .lightbox-device { font-family: 'Playfair Display', serif; font-size: 1.3rem; margin-bottom: 0.4rem; }
  .lightbox-meta { display: flex; gap: 1.5rem; font-size: 0.82rem; color: var(--muted); margin-bottom: 1rem; }
  .lightbox-desc { font-size: 0.85rem; color: var(--muted); line-height: 1.65; }
  .lightbox-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; margin-top: 1rem; border-top: 1px solid var(--border); }
  .lightbox-rating { color: var(--amber); font-size: 0.9rem; }
  .lightbox-close {
    position: absolute; top: 1rem; right: 1rem;
    background: var(--bg-card); border: 1px solid var(--border);
    color: var(--cream); width: 36px; height: 36px; border-radius: 50%;
    font-size: 1.1rem; cursor: pointer; display: flex;
    align-items: center; justify-content: center; z-index: 10;
    transition: border-color 0.2s;
  }
  .lightbox-close:hover { border-color: var(--amber); }
  .lightbox-wrap { position: relative; }

  /* ── BOOK CTA STRIP ── */


  @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

  @media(max-width:768px) {
    .port-header { flex-direction: column; align-items: flex-start; gap: 1.25rem; }
    .port-stats { width: 100%; }
    .filter-bar { padding: 1rem; }
    .gallery-wrap { padding: 1.5rem 1rem 3rem; }
    .lightbox-ba { height: 180px; }
    .lightbox-body { padding: 1.25rem; }
  }
</style>
@endsection

@section('content')

{{-- PORTFOLIO HERO --}}
<div class="port-hero">
  <div class="port-hero-inner">
    <a href="/technicians/{{ $user->id }}" class="back-link">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Profile
    </a>
    <div class="port-header">
      <div class="port-avatar">
        {{ strtoupper(substr($user->name, 0, 1)) }}
        <div class="port-avatar-verified">✓</div>
      </div>
      <div class="port-info">
        <h1>{{ $user->name }} — Full Portfolio</h1>
        <p>{{ $profile->specialisation ?? 'Vintage Electronics Specialist' }}@if($profile->location) · {{ $profile->location }}@endif</p>
        <div class="port-tags">
          @foreach($catCounts->keys() as $cat)<span class="port-tag">{{ $cat }}</span> @endforeach
          <span class="port-tag">Verified Technician</span>
          <span class="port-tag">Member since {{ $user->created_at->format('Y') }}</span>
        </div>
      </div>
      <div class="port-stats">
        <div class="port-stat"><strong>{{ $profile->completed_jobs_count }}</strong><span>Jobs Done</span></div>
        <div class="port-stat"><strong>{{ number_format($profile->avg_rating,1) }}★</strong><span>Avg Rating</span></div>
        <div class="port-stat"><strong>{{ now()->year - $user->created_at->year }}y</strong><span>On Platform</span></div>
      </div>
    </div>
  </div>
</div>

{{-- FILTER BAR --}}
<div class="filter-bar">
  <span class="filter-bar-label">Filter:</span>
  <div class="filter-chips">
    <button class="filter-chip active" onclick="filterPortfolio(this,'all')">All ({{ $completedJobs->count() }})</button>
    @foreach($catCounts as $cat => $cnt)
    @php $catSlug = strtolower(str_replace(' ', '-', $cat)); @endphp
    <button class="filter-chip" onclick="filterPortfolio(this,'{{ $catSlug }}')">{{ $cat }} ({{ $cnt }})</button>
    @endforeach
  </div>
  <select class="sort-select" onchange="sortPortfolio(this.value)">
    <option value="recent">Most Recent</option>
    <option value="rating">Highest Rated</option>
  </select>
</div>

{{-- GALLERY --}}
<div class="gallery-wrap">
  <div class="gallery-results"><strong id="port-count">{{ $completedJobs->count() }}</strong> restorations shown</div>

  <div class="portfolio-grid" id="portfolio-grid">

    @forelse($completedJobs as $job)
    @php
      $svc     = $job->booking->serviceListing;
      $catRaw  = $svc->category ?? 'Other';
      $catSlug = strtolower(str_replace(' ', '-', $catRaw));
      $review  = $job->review;
      $stars   = $review ? str_repeat('★', $review->rating).str_repeat('☆', 5-$review->rating) : '—';
      $dateStr = $job->updated_at->format('F Y');
      $dateKey = $job->updated_at->format('Y-m');
      $beforeSrc = ($svc && $svc->before_image) ? asset('storage/'.$svc->before_image) : '';
      $afterSrc  = ($svc && $svc->after_image)  ? asset('storage/'.$svc->after_image)  : '';
      $desc    = $svc->description ?? 'Restoration completed.';
    @endphp
    <div class="portfolio-card" data-cat="{{ $catSlug }}" data-rating="{{ $review->rating ?? 0 }}" data-date="{{ $dateKey }}"
         onclick="openLightbox(
           '{{ addslashes($job->booking->device_name) }}',
           '{{ addslashes($catRaw) }}',
           '{{ $dateStr }}',
           '{{ $stars }}',
           '{{ addslashes(Str::limit($desc,200)) }}',
           '{{ $beforeSrc }}',
           '{{ $afterSrc }}'
         )">
      <div class="card-ba">
        @if($beforeSrc)
          <div class="card-ba-before" style="background:url('{{ $beforeSrc }}') center/cover;"></div>
          <div class="card-ba-after"  style="background:url('{{ $afterSrc }}') center/cover;"></div>
        @else
          <div class="card-ba-before im-sb"></div>
          <div class="card-ba-after  im-sa"></div>
        @endif
        <div class="card-ba-line"></div>
        <span class="ba-pill before">Before</span>
        <span class="ba-pill after">After</span>
        <span class="cat-badge">{{ $catRaw }}</span>
      </div>
      <div class="card-body">
        <div class="card-device">{{ $job->booking->device_name }}</div>
        <p class="card-desc">{{ Str::limit($desc, 100) }}</p>
        <div class="card-footer-row">
          <span class="card-date">{{ $dateStr }}</span>
          <span class="card-rating">{{ $stars }}</span>
        </div>
      </div>
    </div>
    @empty
    <div style="grid-column:1/-1;padding:3rem;text-align:center;color:var(--muted);">No completed jobs yet.</div>
    @endforelse

  </div>{{-- end portfolio-grid --}}
</div>

{{-- LIGHTBOX --}}
<div class="lightbox" id="lightbox" onclick="if(event.target===this)closeLightbox()">
  <div class="lightbox-wrap">
    <button class="lightbox-close" onclick="closeLightbox()">✕</button>
    <div class="lightbox-inner">
      <div class="lightbox-ba">
        <div class="lightbox-img" id="lb-before">
          <span class="lightbox-label">Before</span>
        </div>
        <div class="lightbox-img" id="lb-after">
          <span class="lightbox-label after">After</span>
        </div>
      </div>
      <div class="lightbox-body">
        <div class="lightbox-device" id="lb-device">—</div>
        <div class="lightbox-meta">
          <span id="lb-cat">—</span>
          <span id="lb-date">—</span>
        </div>
        <p class="lightbox-desc" id="lb-desc">—</p>
        <div class="lightbox-footer">
          <span class="lightbox-rating" id="lb-rating">—</span>
          <span style="font-size:0.78rem;color:var(--muted);">by {{ $user->name }}@if($profile->location) · {{ $profile->location }}@endif</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function filterPortfolio(btn, cat) {
    document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
    btn.classList.add('active');
    const cards = document.querySelectorAll('.portfolio-card');
    let count = 0;
    cards.forEach(card => {
      const show = cat === 'all' || card.dataset.cat === cat;
      card.style.display = show ? '' : 'none';
      if (show) count++;
    });
    document.getElementById('port-count').textContent = count;
  }

  function sortPortfolio(val) {
    const grid = document.getElementById('portfolio-grid');
    const cards = [...grid.querySelectorAll('.portfolio-card')];
    cards.sort((a, b) => {
      if (val === 'rating') return parseInt(b.dataset.rating) - parseInt(a.dataset.rating);
      return b.dataset.date.localeCompare(a.dataset.date);
    });
    cards.forEach(c => grid.appendChild(c));
  }

  function openLightbox(device, cat, date, rating, desc, beforeSrc, afterSrc) {
      document.getElementById('lb-device').textContent = device;
      document.getElementById('lb-cat').textContent    = cat;
      document.getElementById('lb-date').textContent   = date;
      document.getElementById('lb-rating').textContent = rating;
      document.getElementById('lb-desc').textContent   = desc;
      const lb  = document.getElementById('lightbox');
      const bEl = lb.querySelector('.lb-before');
      const aEl = lb.querySelector('.lb-after');
      bEl.className = 'lb-before'; aEl.className = 'lb-after';
      if (beforeSrc) { bEl.style.backgroundImage="url('"+beforeSrc+"')"; bEl.style.backgroundSize='cover'; }
      else           { bEl.style.backgroundImage=''; }
      if (afterSrc)  { aEl.style.backgroundImage="url('"+afterSrc+"')"; aEl.style.backgroundSize='cover'; }
      else           { aEl.style.backgroundImage=''; }
      lb.classList.add('open');
  }

  function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
  }

  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>
@endsection