@extends('layout')
@section('title', 'Browse Restorations')

@section('styles')
<style>
  .page-header { padding:3.5rem 2rem 2rem; max-width:1400px; margin:0 auto; position:relative; z-index:1; }
  .page-header h1 { font-size:clamp(2rem,4vw,3rem); line-height:1.1; margin-bottom:0.5rem; }
  .page-header h1 em { font-style:italic; color:var(--amber-lt); }
  .page-header p { color:var(--muted); font-size:1rem; max-width:480px; }
  .search-wrap { max-width:1400px; margin:0 auto; padding:0 2rem 2rem; position:relative; z-index:1; }
  .search-bar { display:flex; background:var(--bg-card); border:1px solid var(--border); border-radius:10px; overflow:hidden; max-width:680px; transition:border-color 0.2s; }
  .search-bar:focus-within { border-color:var(--amber); }
  .search-icon { display:flex; align-items:center; padding:0 1rem; color:var(--muted); }
  .search-bar input { flex:1; background:transparent; border:none; outline:none; color:var(--cream); font-family:'DM Sans',sans-serif; font-size:0.95rem; padding:0.85rem 0; }
  .search-bar input::placeholder { color:var(--muted); }
  .search-bar button { background:var(--amber); border:none; cursor:pointer; padding:0 1.5rem; color:#161310; font-weight:600; font-size:0.9rem; font-family:'DM Sans',sans-serif; }
  .search-bar button:hover { background:var(--amber-lt); }
  .main-layout { max-width:1400px; margin:0 auto; padding:0 2rem 4rem; display:grid; grid-template-columns:260px 1fr; gap:2rem; position:relative; z-index:1; }
  .filter-sidebar { position:sticky; top:80px; align-self:start; }
  .filter-panel { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:1.5rem; }
  .filter-title { font-family:'Playfair Display',serif; font-size:1.1rem; font-weight:700; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem; }
  .filter-title::after { content:''; flex:1; height:1px; background:var(--border); }
  .filter-section { margin-bottom:1.75rem; }
  .filter-label { font-size:0.72rem; font-weight:600; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted); margin-bottom:0.75rem; display:block; }
  .category-list { display:flex; flex-direction:column; gap:0.4rem; }
  .cat-item { display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0.75rem; border-radius:7px; cursor:pointer; transition:background 0.15s; font-size:0.9rem; }
  .cat-item:hover { background:var(--bg-card2); }
  .cat-item.active { background:rgba(212,137,26,0.12); color:var(--amber-lt); }
  .cat-item input[type="radio"] { display:none; }
  .cat-dot { width:8px; height:8px; border-radius:50%; border:2px solid var(--muted); flex-shrink:0; transition:all 0.15s; }
  .cat-item.active .cat-dot { background:var(--amber); border-color:var(--amber); }
  .cat-count { margin-left:auto; font-size:0.75rem; color:var(--muted); background:var(--bg-card2); padding:0.1rem 0.5rem; border-radius:99px; }
  .star-filter { display:flex; flex-direction:column; gap:0.4rem; }
  .star-row { display:flex; align-items:center; gap:0.5rem; padding:0.45rem 0.75rem; border-radius:7px; cursor:pointer; transition:background 0.15s; }
  .star-row:hover { background:var(--bg-card2); }
  .star-row.active { background:rgba(212,137,26,0.12); }
  .stars { color:var(--amber); font-size:0.85rem; }
  .star-row span:last-child { font-size:0.8rem; color:var(--muted); margin-left:auto; }
  .price-range input[type="range"] { width:100%; accent-color:var(--amber); cursor:pointer; }
  .price-labels { display:flex; justify-content:space-between; margin-top:0.5rem; font-size:0.8rem; color:var(--muted); }
  .filter-reset { display:block; width:100%; background:transparent; border:1px solid var(--border); color:var(--muted); padding:0.6rem; border-radius:7px; cursor:pointer; font-size:0.85rem; font-family:'DM Sans',sans-serif; transition:all 0.2s; margin-top:0.5rem; }
  .filter-reset:hover { border-color:var(--amber); color:var(--amber); }
  .results-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
  .results-count { color:var(--muted); font-size:0.9rem; }
  .results-count strong { color:var(--cream); }
  .sort-select { background:var(--bg-card); border:1px solid var(--border); color:var(--cream); padding:0.45rem 0.9rem; border-radius:7px; font-size:0.85rem; font-family:'DM Sans',sans-serif; cursor:pointer; outline:none; }
  .cards-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:1.5rem; }
  .card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; overflow:hidden; transition:border-color 0.25s,transform 0.25s; position:relative; animation:fadeUp 0.4s ease both; }
  .card:hover { border-color:var(--amber); transform:translateY(-3px); }
  .card:nth-child(1){animation-delay:0.05s} .card:nth-child(2){animation-delay:0.1s} .card:nth-child(3){animation-delay:0.15s}
  .card:nth-child(4){animation-delay:0.2s} .card:nth-child(5){animation-delay:0.25s} .card:nth-child(6){animation-delay:0.3s}
  .ba-container { position:relative; height:200px; overflow:hidden; cursor:pointer; }
  .ba-before,.ba-after { position:absolute; inset:0; background-size:cover; background-position:center; }
  .ba-after { clip-path:inset(0 50% 0 0); transition:clip-path 0.3s ease; }
  .ba-container:hover .ba-after { clip-path:inset(0 30% 0 0); }
  .ba-divider { position:absolute; left:50%; top:0; bottom:0; width:2px; background:var(--amber); transform:translateX(-50%); pointer-events:none; }
  .ba-label { position:absolute; bottom:10px; font-size:0.65rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; background:rgba(22,19,16,0.75); backdrop-filter:blur(4px); padding:0.2rem 0.5rem; border-radius:4px; }
  .ba-label.before { left:10px; } .ba-label.after { right:10px; color:var(--amber-lt); }
  .device-badge { position:absolute; top:10px; left:50%; transform:translateX(-50%); background:rgba(212,137,26,0.15); border:1px solid rgba(212,137,26,0.3); color:var(--amber-lt); padding:0.2rem 0.7rem; border-radius:20px; font-size:0.7rem; font-weight:600; white-space:nowrap; }
  .card-body { padding:1.25rem; }
  .card-meta { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem; }
  .tech-info { display:flex; align-items:center; gap:0.6rem; }
  .tech-avatar { width:34px; height:34px; border-radius:50%; background:var(--bg-card2); border:2px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:0.9rem; font-weight:700; color:var(--amber); flex-shrink:0; }
  .tech-name { font-size:0.85rem; font-weight:600; }
  .tech-location { font-size:0.72rem; color:var(--muted); }
  .rating-pill { display:flex; align-items:center; gap:0.3rem; background:rgba(212,137,26,0.1); border:1px solid rgba(212,137,26,0.2); padding:0.25rem 0.65rem; border-radius:20px; }
  .rating-pill span:first-child { color:var(--amber); font-size:0.8rem; }
  .rating-pill span:last-child { font-size:0.8rem; font-weight:700; }
  .card-title { font-family:'Playfair Display',serif; font-size:1.05rem; font-weight:700; margin-bottom:0.4rem; line-height:1.3; }
  .card-desc { font-size:0.82rem; color:var(--muted); line-height:1.5; margin-bottom:1rem; }
  .card-footer { display:flex; align-items:center; justify-content:space-between; padding-top:1rem; border-top:1px solid var(--border); }
  .price-tag strong { font-size:1.05rem; color:var(--amber-lt); }
  .price-tag span { color:var(--muted); font-size:0.75rem; }
  .card-actions { display:flex; gap:0.5rem; }
  .btn-ghost { background:transparent; border:1px solid var(--border); color:var(--cream); padding:0.45rem 0.9rem; border-radius:7px; font-size:0.8rem; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all 0.2s; }
  .btn-ghost:hover { border-color:var(--amber); color:var(--amber-lt); }
  .btn-primary { background:var(--amber); border:none; color:#161310; padding:0.45rem 0.9rem; border-radius:7px; font-size:0.8rem; font-weight:700; cursor:pointer; font-family:'DM Sans',sans-serif; transition:background 0.2s; }
  .btn-primary:hover { background:var(--amber-lt); }
  /* tech view: hide Book Slot */
  body.tech-view .btn-primary { display: none; }
  body.tech-view .btn-ghost { border-color: var(--amber); color: var(--amber-lt); }
  .img-synth-a  { background:linear-gradient(135deg,#1a2a1a,#233520); }
  .img-radio-b  { background:linear-gradient(135deg,#1e1a2a,#2a2038); }
  .img-radio-a  { background:linear-gradient(135deg,#0e1e2a,#122438); }
  .img-game-b   { background:linear-gradient(135deg,#2a1a1a,#3a1e1e); }
  .img-game-a   { background:linear-gradient(135deg,#1a2a28,#1e3530); }
  .img-hifi-b   { background:linear-gradient(135deg,#201a0a,#30250e); }
  .img-hifi-a   { background:linear-gradient(135deg,#0a1a20,#0e2530); }
  .img-cam-b    { background:linear-gradient(135deg,#1a1a20,#25252e); }
  .img-cam-a    { background:linear-gradient(135deg,#101a10,#152016); }
  .usd-toggle-bar {
    display: flex; align-items: center; gap: 0.75rem;
    margin-bottom: 1rem; padding: 0.75rem 1rem;
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 10px;
  }
  .usd-toggle-bar span { font-size: 0.82rem; color: var(--muted); flex: 1; }
  .usd-toggle-bar strong { color: var(--amber-lt); font-size: 0.82rem; }
  .btn-usd-toggle {
    background: transparent; border: 1px solid var(--amber);
    color: var(--amber-lt); padding: 0.4rem 1rem; border-radius: 7px;
    font-size: 0.78rem; font-weight: 700; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: all 0.2s; white-space: nowrap;
  }
  .btn-usd-toggle.active { background: var(--amber); color: #161310; }
  .btn-usd-toggle:disabled { opacity: 0.5; cursor: not-allowed; }
  @keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:900px){ .main-layout{grid-template-columns:1fr} .filter-sidebar{position:static} }
  @media(max-width:600px){ .main-layout{padding:0 1rem 3rem} .page-header,.search-wrap{padding-left:1rem;padding-right:1rem} }
</style>
@endsection

@section('content')

<div class="page-header">
  <h1>Browse <em>Restorations</em></h1>
  <p>Discover verified technicians and their craft — every listing is a proven portfolio piece.</p>
</div>

<div class="search-wrap">
  <div class="search-bar">
    <div class="search-icon">
      <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
    </div>
    <input type="text" id="search-input" placeholder="Search by device, technician, or keyword…" oninput="filterCards()"/>
    <button>Search</button>
  </div>
</div>

<div class="main-layout">

  {{-- FILTER SIDEBAR --}}
  @php
  $catCounts = $listings->groupBy('category')->map->count();
  $totalCount = $listings->count();
  $maxPrice = $listings->max('price_max') ?: 500;
  $maxPrice = ceil($maxPrice / 50) * 50; // round up to nearest 50
@endphp
<aside class="filter-sidebar">
    <div class="filter-panel">
      <div class="filter-title">Filters</div>

      <div class="filter-section">
        <span class="filter-label">Device Category</span>
        <div class="category-list">
          <label class="cat-item active" onclick="setCategory(this,'all')">
            <input type="radio" name="cat" value="all" checked/>
            <div class="cat-dot"></div>All Categories<span class="cat-count" id="count-all">{{ $totalCount }}</span>
          </label>
          <label class="cat-item" onclick="setCategory(this,'synthesizers')">
            <input type="radio" name="cat" value="synthesizers"/>
            <div class="cat-dot"></div>Synthesizers<span class="cat-count" id="count-synthesizers">{{ $catCounts['Synthesizers'] ?? 0 }}</span>
          </label>
          <label class="cat-item" onclick="setCategory(this,'retro-gaming')">
            <input type="radio" name="cat" value="retro-gaming"/>
            <div class="cat-dot"></div>Retro Gaming<span class="cat-count" id="count-retro-gaming">{{ $catCounts['Retro Gaming'] ?? 0 }}</span>
          </label>
          <label class="cat-item" onclick="setCategory(this,'hifi-audio')">
            <input type="radio" name="cat" value="hifi-audio"/>
            <div class="cat-dot"></div>Hi-Fi Audio<span class="cat-count" id="count-hifi-audio">{{ $catCounts['Hi-Fi Audio'] ?? 0 }}</span>
          </label>
          <label class="cat-item" onclick="setCategory(this,'vintage-radio')">
            <input type="radio" name="cat" value="vintage-radio"/>
            <div class="cat-dot"></div>Vintage Radio<span class="cat-count" id="count-vintage-radio">{{ $catCounts['Vintage Radio'] ?? 0 }}</span>
          </label>
          <label class="cat-item" onclick="setCategory(this,'cameras')">
            <input type="radio" name="cat" value="cameras"/>
            <div class="cat-dot"></div>Cameras<span class="cat-count" id="count-cameras">{{ $catCounts['Cameras'] ?? 0 }}</span>
          </label>
        </div>
      </div>

      <div class="filter-section">
        <span class="filter-label">Minimum Rating</span>
        <div class="star-filter">
          <div class="star-row" onclick="setRating(this,0)">
            <span class="stars">★★★★★</span><span>Any rating</span>
          </div>
          <div class="star-row" onclick="setRating(this,4)">
            <span class="stars">★★★★☆</span><span>4+ stars</span>
          </div>
          <div class="star-row active" onclick="setRating(this,4.5)">
            <span class="stars">★★★★★</span><span>4.5+ stars</span>
          </div>
        </div>
      </div>

      <div class="filter-section">
        <span class="filter-label">Max Price (BDT)</span>
        <div>
          <input type="range" min="500" max="100000" value="100000" id="price-slider" oninput="document.getElementById('price-val').textContent='৳'+parseInt(this.value).toLocaleString()" style="width:100%;accent-color:var(--amber);cursor:pointer;"/>
          <div class="price-labels"><span>৳500</span><span id="price-val">৳100,000</span><span>৳100k+</span></div>
        </div>
      </div>

      <button class="filter-reset" onclick="resetFilters()">Reset All Filters</button>
    </div>
  </aside>

  {{-- RESULTS --}}
  <div>
    <div class="usd-toggle-bar" id="usd-bar">
      <span>Prices shown in <strong id="currency-label">BDT (৳)</strong></span>
      <button class="btn-usd-toggle" id="btn-usd" onclick="toggleUsd()" disabled>
        ⏳ Loading rate…
      </button>
    </div>

    <div class="results-header">
      <p class="results-count"><strong id="result-count">{{ $listings->count() }}</strong> services found</p>
      <select class="sort-select" onchange="sortCards(this.value)">
        <option value="rating">Sort: Top Rated</option>
        <option value="price-asc">Price: Low to High</option>
        <option value="price-desc">Price: High to Low</option>
      </select>
    </div>

    <div class="cards-grid" id="cards-grid">

      @forelse($listings as $listing)
      @php
        $catMap = [
          'Synthesizers'      => 'synthesizers',
          'Retro Gaming'      => 'retro-gaming',
          'Hi-Fi Audio'       => 'hifi-audio',
          'Vintage Keyboards' => 'vintage-keyboards',
          'Vintage Radio'     => 'vintage-radio',
          'Vintage Computer'  => 'vintage-computer',
          'Cameras'           => 'cameras',
          'Other'             => 'other',
        ];
        $catSlug = $catMap[$listing->category] ?? strtolower(str_replace(' ', '-', $listing->category));
        $techName = $listing->technicianProfile->user->name ?? 'Unknown';
        $techInitial = strtoupper(substr($techName, 0, 1));
        $rating = number_format($listing->technicianProfile->avg_rating ?? 0, 1);
        $priceAvg = ($listing->price_min + $listing->price_max) / 2;
      @endphp
      <div class="card"
           data-category="{{ $catSlug }}"
           data-rating="{{ $listing->technicianProfile->avg_rating ?? 0 }}"
           data-price="{{ $priceAvg }}"
           data-title="{{ $listing->title }}"
           data-price-min="{{ $listing->price_min }}"
           data-price-max="{{ $listing->price_max }}"
           data-tech="{{ $techName }}">
      <div class="ba-container">
          <div class="ba-before" style="background-image:url('{{ asset('storage/' . $listing->before_image) }}');background-size:cover;background-position:center;">
            <span class="ba-label before">Before</span>
          </div>
          <div class="ba-after" style="background-image:url('{{ asset('storage/' . $listing->after_image) }}');background-size:cover;background-position:center;">
            <span class="ba-label after">After</span>
          </div>
          <div class="ba-divider"></div>
          <span class="device-badge">{{ $listing->category }}</span>
        </div>
        <div class="card-body">
          <div class="card-meta">
            <div class="tech-info">
              <div class="tech-avatar">{{ $techInitial }}</div>
              <div>
                <div class="tech-name">{{ $techName }}</div>
                <div class="tech-location">{{ $listing->technicianProfile->location ?? 'Location not set' }}</div>
              </div>
            </div>
            <div class="rating-pill"><span>★</span><span>{{ $rating }}</span></div>
          </div>
          <h3 class="card-title">{{ $listing->title }}</h3>
          <p class="card-desc">{{ Str::limit($listing->description, 120) }}</p>
          <div class="card-footer">
            <div class="price-tag" data-min="{{ $listing->price_min }}" data-max="{{ $listing->price_max }}">
              <strong class="price-display">৳{{ number_format($listing->price_min, 0) }} – ৳{{ number_format($listing->price_max, 0) }}</strong>
              <span class="price-currency">BDT</span>
            </div>
            <div class="card-actions">
              <button class="btn-ghost" onclick="window.location='/technicians/{{ $listing->technicianProfile->user->id ?? 0 }}/portfolio'">Portfolio</button>
              @if(!auth()->check() || !auth()->user()->isTechnician())
              <button class="btn-primary" onclick="bookSlot('{{ $listing->technicianProfile->user->id ?? 0 }}', '{{ addslashes($techName) }}', {{ $listing->id }})">Book Slot</button>
              @endif
            </div>
          </div>
        </div>
      </div>
      @empty
      <div style="grid-column:1/-1;text-align:center;padding:4rem 2rem;color:var(--muted);">
        <div style="font-size:2.5rem;margin-bottom:1rem;">🔍</div>
        <p style="font-size:1rem;font-weight:600;">No approved listings yet</p>
        <p style="font-size:0.85rem;margin-top:0.4rem;">Check back soon — technicians are submitting their work.</p>
      </div>
      @endforelse

    </div>


<script>
  let activeCategory = 'all';
  let minRating = 0;

  // ── EXCHANGE RATE ─────────────────────────────────────────
  let usdRate   = null;   // 1 BDT = X USD (fetched once)
  let showingUsd = false;

  (function loadExchangeRate() {
    fetch('/api/exchange-rate')
      .then(r => r.json())
      .then(data => {
        if (data.success && data.rate) {
          usdRate = data.rate;
          const btn = document.getElementById('btn-usd');
          btn.disabled   = false;
          btn.textContent = 'Show in USD';
        }
      })
      .catch(() => {
        // Silently fail — BDT display remains
        const btn = document.getElementById('btn-usd');
        if (btn) { btn.textContent = 'Rate unavailable'; }
      });
  })();

  function toggleUsd() {
    if (!usdRate) return;
    showingUsd = !showingUsd;
    const btn = document.getElementById('btn-usd');
    const label = document.getElementById('currency-label');

    document.querySelectorAll('.price-tag').forEach(tag => {
      const min = parseFloat(tag.dataset.min);
      const max = parseFloat(tag.dataset.max);
      const display = tag.querySelector('.price-display');
      const currency = tag.querySelector('.price-currency');
      if (!display) return;
      if (showingUsd) {
        const uMin = (min * usdRate).toFixed(2);
        const uMax = (max * usdRate).toFixed(2);
        display.textContent = '$' + uMin + ' – $' + uMax;
        if (currency) currency.textContent = 'USD';
      } else {
        display.textContent = '৳' + Math.round(min).toLocaleString() + ' – ৳' + Math.round(max).toLocaleString();
        if (currency) currency.textContent = 'BDT';
      }
    });

    btn.textContent = showingUsd ? 'Show in BDT' : 'Show in USD';
    btn.classList.toggle('active', showingUsd);
    if (label) label.innerHTML = showingUsd ? '<strong>USD ($)</strong>' : '<strong>BDT (৳)</strong>';
  }

  function setCategory(el, val) {
    document.querySelectorAll('.cat-item').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    activeCategory = val;
    filterCards();
  }

  // ── ROLE CHECK: hide Book Slot for technicians ──
  (function() {
    const role = localStorage.getItem('vr_role');
    if (role === 'technician') document.body.classList.add('tech-view');
  })();

  // ── BUTTON HANDLERS ──
  function bookSlot(techUserId, techName, listingId) {
    const role = localStorage.getItem('vr_role');
    if (!role) {
      if (confirm('You need to join as a Collector to book a slot. Join now?')) {
        window.location.href = '/';
      }
      return;
    }
    window.location.href = '/technicians/' + techUserId + '?listing=' + listingId;
  }

  function viewPortfolio(techSlug) {
    window.location.href = '/technicians/' + techSlug + '/portfolio';
  }

  function setRating(el, val) {
    document.querySelectorAll('.star-row').forEach(r => r.classList.remove('active'));
    el.classList.add('active');
    minRating = val;
    filterCards();
  }

  function filterCards() {
    const q = document.getElementById('search-input').value.toLowerCase();
    const maxPrice = parseInt(document.getElementById('price-slider').value);
    const cards = document.querySelectorAll('.card');
    let visible = 0;
    cards.forEach(card => {
      const catMatch = activeCategory === 'all' || card.dataset.category === activeCategory;
      const ratingMatch = parseFloat(card.dataset.rating) >= minRating;
      const priceMatch = parseFloat(card.dataset.price) <= maxPrice;
      const searchMatch = !q || card.dataset.title.toLowerCase().includes(q) || card.dataset.tech.toLowerCase().includes(q) || card.dataset.category.toLowerCase().includes(q);
      const show = catMatch && ratingMatch && priceMatch && searchMatch;
      card.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    document.getElementById('result-count').textContent = visible;
  }

  function resetFilters() {
    document.getElementById('search-input').value = '';
    document.querySelectorAll('.cat-item').forEach(i => i.classList.remove('active'));
    document.querySelector('.cat-item').classList.add('active');
    document.querySelectorAll('.star-row').forEach(r => r.classList.remove('active'));
    document.querySelectorAll('.star-row')[0].classList.add('active');
    activeCategory = 'all'; minRating = 0;
    const slider = document.getElementById('price-slider');
    slider.value = slider.max;
    document.getElementById('price-val').textContent = '৳' + parseInt(slider.max).toLocaleString();
    filterCards();
  }

  function sortCards(val) {
    const grid = document.getElementById('cards-grid');
    const cards = [...grid.querySelectorAll('.card')];
    cards.sort((a, b) => {
      if (val === 'rating') return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
      if (val === 'price-asc') return parseInt(a.dataset.price) - parseInt(b.dataset.price);
      if (val === 'price-desc') return parseInt(b.dataset.price) - parseInt(a.dataset.price);
      return 0;
    });
    cards.forEach(c => grid.appendChild(c));
  }

  // Init on page load
  filterCards();
</script>
@endsection