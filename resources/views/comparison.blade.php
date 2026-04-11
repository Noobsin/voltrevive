@extends('layout')
@section('title', 'Compare Technicians')

@section('styles')
<style>
  /* ── PAGE HEADER ── */
  .cmp-hero {
    max-width: 1400px; margin: 0 auto;
    padding: 3rem 2rem 2rem; position: relative; z-index: 1;
  }
  .cmp-hero h1 { font-size: clamp(1.8rem,3vw,2.6rem); line-height: 1.1; margin-bottom: 0.4rem; }
  .cmp-hero h1 em { font-style: italic; color: var(--amber-lt); }
  .cmp-hero p { color: var(--muted); font-size: 0.95rem; }

  /* ── STEP INDICATOR ── */
  .steps-bar {
    max-width: 1400px; margin: 0 auto;
    padding: 0 2rem 2rem; display: flex; align-items: center;
    position: relative; z-index: 1;
  }
  .step { display: flex; align-items: center; gap: 0.6rem; font-size: 0.82rem; color: var(--muted); }
  .step.active { color: var(--amber-lt); }
  .step.done   { color: #5de0b0; }
  .step-num {
    width: 26px; height: 26px; border-radius: 50%;
    border: 2px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.72rem; font-weight: 700; flex-shrink: 0;
  }
  .step.active .step-num { border-color: var(--amber); color: var(--amber); }
  .step.done   .step-num { border-color: #5de0b0; color: #5de0b0; }
  .step-line { flex: 1; height: 1px; background: var(--border); margin: 0 0.75rem; max-width: 80px; }

  /* ── CATEGORY FILTER ── */
  .cat-filter-wrap {
    max-width: 1400px; margin: 0 auto;
    padding: 0 2rem 2rem; position: relative; z-index: 1;
  }
  .cat-filter-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 1.5rem; }
  .cat-filter-label { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); margin-bottom: 1rem; display: block; }
  .cat-chips { display: flex; flex-wrap: wrap; gap: 0.6rem; }
  .cat-chip {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.55rem 1.1rem; border-radius: 30px;
    border: 1px solid var(--border); font-size: 0.85rem;
    cursor: pointer; transition: all 0.15s; color: var(--muted);
    background: transparent; font-family: 'DM Sans', sans-serif;
  }
  .cat-chip:hover { border-color: var(--amber); color: var(--amber-lt); }
  .cat-chip.active { background: rgba(212,137,26,0.12); border-color: var(--amber); color: var(--amber-lt); font-weight: 600; }
  .cat-chip-count { font-size: 0.7rem; color: var(--muted); background: var(--bg-card2); padding: 0.05rem 0.4rem; border-radius: 20px; }
  .cat-chip.active .cat-chip-count { background: rgba(212,137,26,0.2); color: var(--amber); }

  /* ── TWO COLUMN LAYOUT ── */
  .cmp-layout {
    max-width: 1400px; margin: 0 auto;
    padding: 0 2rem 5rem;
    display: grid; grid-template-columns: 300px 1fr;
    gap: 2rem; position: relative; z-index: 1;
  }
  .panel-title {
    font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--muted);
    margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;
  }
  .panel-title::after { content: ''; flex: 1; height: 1px; background: var(--border); }

  /* ── TECHNICIAN LIST ── */
  .tech-list { display: flex; flex-direction: column; gap: 0.6rem; }
  .tech-list-item {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 10px; padding: 0.85rem 1rem;
    display: flex; align-items: center; gap: 0.85rem;
    cursor: grab; transition: all 0.2s;
    animation: fadeUp 0.3s ease both; user-select: none;
  }
  .tech-list-item:active { cursor: grabbing; }
  .tech-list-item:hover { border-color: rgba(212,137,26,0.4); transform: translateX(3px); }
  .tech-list-item.dragging { opacity: 0.35; border-color: var(--amber); }
  .tech-list-item.already-added { opacity: 0.3; cursor: not-allowed; pointer-events: none; }
  .tech-list-item:nth-child(1){animation-delay:.05s}.tech-list-item:nth-child(2){animation-delay:.10s}
  .tech-list-item:nth-child(3){animation-delay:.15s}.tech-list-item:nth-child(4){animation-delay:.20s}
  .tech-list-item:nth-child(5){animation-delay:.25s}.tech-list-item:nth-child(6){animation-delay:.30s}
  .tli-avatar {
    width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg,#2a1f0e,#3d2b14); border: 2px solid var(--amber);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif; font-size: 1rem; font-weight: 900; color: var(--amber-lt);
  }
  .tli-info { flex: 1; min-width: 0; }
  .tli-name { font-size: 0.9rem; font-weight: 700; }
  .tli-loc { font-size: 0.75rem; color: var(--muted); }
  .tli-rating { font-size: 0.8rem; color: var(--amber); font-weight: 600; flex-shrink: 0; }
  .tli-drag-hint { color: var(--border); flex-shrink: 0; font-size: 0.9rem; letter-spacing: 0.1em; }
  .no-results { background: var(--bg-card); border: 1px dashed var(--border); border-radius: 10px; padding: 2rem; text-align: center; color: var(--muted); font-size: 0.88rem; display: none; }

  /* ── DROP ZONES ── */
  .drop-zones { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; margin-bottom: 1.5rem; }
  .drop-zone {
    background: var(--bg-card); border: 2px dashed var(--border);
    border-radius: 14px; min-height: 150px;
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; gap: 0.5rem; text-align: center;
    transition: all 0.2s; position: relative; padding: 1rem;
  }
  .drop-zone.drag-over { border-color: var(--amber); background: rgba(212,137,26,0.06); }
  .drop-zone.filled { border-style: solid; border-color: var(--border); }
  .drop-zone-num {
    width: 32px; height: 32px; border-radius: 50%;
    border: 2px dashed var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; color: var(--muted); font-weight: 700;
  }
  .drop-zone.drag-over .drop-zone-num { border-color: var(--amber); color: var(--amber); }
  .drop-zone-hint { font-size: 0.78rem; color: var(--muted); }
  /* filled slot */
  .slot-avatar {
    width: 52px; height: 52px; border-radius: 50%;
    background: linear-gradient(135deg,#2a1f0e,#3d2b14); border: 2px solid var(--amber);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 900; color: var(--amber-lt);
    margin: 0 auto 0.5rem;
  }
  .slot-name { font-size: 0.9rem; font-weight: 700; margin-bottom: 0.15rem; }
  .slot-loc { font-size: 0.72rem; color: var(--muted); margin-bottom: 0.4rem; }
  .slot-rating { color: var(--amber); font-size: 0.8rem; }
  .slot-remove {
    position: absolute; top: 8px; right: 8px;
    background: var(--bg-card2); border: 1px solid var(--border);
    color: var(--muted); width: 22px; height: 22px; border-radius: 50%;
    font-size: 0.7rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.15s;
  }
  .slot-remove:hover { border-color: #f09090; color: #f09090; }

  /* ── COMPARISON TABLE ── */
  .cmp-table-wrap {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden; display: none;
    animation: fadeUp 0.4s ease;
  }
  .cmp-table-wrap.visible { display: block; }
  .cmp-table { width: 100%; border-collapse: collapse; }
  .cmp-table thead tr { background: var(--bg-card2); border-bottom: 1px solid var(--border); }
  .cmp-table thead th { padding: 1.5rem 1.25rem; text-align: center; vertical-align: top; border-left: 1px solid var(--border); }
  .cmp-table thead th:first-child { text-align: left; width: 140px; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); border-left: none; }
  .cmp-table thead th.hl { background: rgba(212,137,26,0.07); border-top: 2px solid var(--amber); }
  .col-avatar-lg {
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg,#2a1f0e,#3d2b14); border: 2px solid var(--amber);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 900; color: var(--amber-lt);
    margin: 0 auto 0.65rem;
  }
  .col-name { font-family: 'Playfair Display', serif; font-size: 1.05rem; font-weight: 700; margin-bottom: 0.15rem; }
  .col-loc { font-size: 0.75rem; color: var(--muted); margin-bottom: 0.6rem; }
  .col-cats { display: flex; flex-wrap: wrap; gap: 0.3rem; justify-content: center; }
  .col-cat-tag { background: rgba(212,137,26,0.1); border: 1px solid rgba(212,137,26,0.2); color: var(--amber-lt); padding: 0.15rem 0.55rem; border-radius: 20px; font-size: 0.68rem; font-weight: 600; }
  .cmp-table tbody tr { border-bottom: 1px solid var(--border); }
  .cmp-table tbody tr:last-child { border-bottom: none; }
  .cmp-table tbody tr:hover { background: rgba(255,255,255,0.015); }
  .cmp-table td { padding: 1rem 1.25rem; text-align: center; font-size: 0.88rem; vertical-align: middle; border-left: 1px solid var(--border); }
  .cmp-table td:first-child { text-align: left; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: var(--muted); border-left: none; }
  .cmp-table td.hl { background: rgba(212,137,26,0.04); }
  .cell-big { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 900; color: var(--amber-lt); display: block; line-height: 1; }
  .cell-sub { font-size: 0.72rem; color: var(--muted); margin-top: 0.2rem; display: block; }
  .cell-stars { color: var(--amber); font-size: 0.95rem; }
  .winner { color: #5de0b0; font-weight: 700; background: rgba(74,200,120,0.08); border-radius: 6px; padding: 0.2rem 0.5rem; display: inline-block; }
  /* mini portfolio */
  .mini-gallery { display: grid; grid-template-columns: repeat(3,1fr); gap: 3px; border-radius: 8px; overflow: hidden; }
  .mg-pair { display: grid; grid-template-columns: 1fr 1fr; height: 42px; gap: 2px; }
  .mg-img { background-size: cover; background-position: center; }
  .im-sb{background:linear-gradient(135deg,#2a1f0e,#3d2b0f)}.im-sa{background:linear-gradient(135deg,#1a2a1a,#233520)}
  .im-rb{background:linear-gradient(135deg,#1e1a2a,#2a2038)}.im-ra{background:linear-gradient(135deg,#0e1e2a,#122438)}
  .im-hb{background:linear-gradient(135deg,#201a0a,#30250e)}.im-ha{background:linear-gradient(135deg,#0a1a20,#0e2530)}
  /* tfoot */
  .cmp-table tfoot td { padding: 1.25rem; text-align: center; background: var(--bg-card2); border-top: 2px solid var(--border); border-left: 1px solid var(--border); }
  .cmp-table tfoot td:first-child { border-left: none; }
  .cmp-table tfoot td.hl { background: rgba(212,137,26,0.08); border-top-color: var(--amber); }
  .btn-goto { display: inline-flex; align-items: center; gap: 0.4rem; background: var(--amber); color: #161310; border: none; padding: 0.6rem 1.4rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background 0.2s; text-decoration: none; }
  .btn-goto:hover { background: var(--amber-lt); }
  .btn-goto-ghost { display: inline-flex; align-items: center; gap: 0.4rem; background: transparent; border: 1px solid var(--border); color: var(--cream); padding: 0.6rem 1.4rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; font-family: 'DM Sans', sans-serif; transition: all 0.2s; text-decoration: none; }
  .btn-goto-ghost:hover { border-color: var(--amber); color: var(--amber-lt); }

  /* ── EMPTY DROP PROMPT ── */
  .drop-prompt { background: var(--bg-card); border: 2px dashed var(--border); border-radius: 14px; padding: 3rem; text-align: center; color: var(--muted); }
  .drop-prompt-icon { font-size: 2.5rem; margin-bottom: 0.75rem; }

  @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:1000px){ .cmp-layout{grid-template-columns:1fr} .drop-zones{grid-template-columns:1fr 1fr} }
  @media(max-width:600px){ .cmp-hero,.cat-filter-wrap,.cmp-layout{padding-left:1rem;padding-right:1rem} .drop-zones{grid-template-columns:1fr} .steps-bar{padding-left:1rem} }
</style>
@endsection

@section('content')

<div class="cmp-hero">
  <h1>Compare <em>Technicians</em></h1>
  <p>Filter by device category, then drag up to 3 technicians into the comparison slots.</p>
</div>

{{-- STEP INDICATOR --}}
<div class="steps-bar">
  <div class="step active" id="step-1"><div class="step-num">1</div> Choose a Category</div>
  <div class="step-line"></div>
  <div class="step" id="step-2"><div class="step-num">2</div> Drag Technicians</div>
  <div class="step-line"></div>
  <div class="step" id="step-3"><div class="step-num">3</div> Compare &amp; Choose</div>
</div>

{{-- CATEGORY FILTER --}}
<div class="cat-filter-wrap">
  <div class="cat-filter-card">
    <span class="cat-filter-label">Step 1 — Filter by device category</span>
    <div class="cat-chips">
      <button class="cat-chip active" onclick="filterCat(this,'all')">⚡ All <span class="cat-chip-count">{{ $techsData->count() }}</span></button>
      @php $catIcons=['synthesizer'=>'🎹','radio'=>'📻','hifi'=>'🔊','gaming'=>'🎮','cameras'=>'📷','computer'=>'💻','other'=>'🔧']; $catLabels=['synthesizer'=>'Synthesizers','radio'=>'Vintage Radio','hifi'=>'Hi-Fi Audio','gaming'=>'Retro Gaming','cameras'=>'Cameras','computer'=>'Vintage Computers','other'=>'Other']; @endphp
      @foreach($catCounts as $slug => $count)
      <button class="cat-chip" onclick="filterCat(this,'{{ $slug }}')">{{ $catIcons[$slug] ?? '🔧' }} {{ $catLabels[$slug] ?? ucfirst($slug) }} <span class="cat-chip-count">{{ $count }}</span></button>
      @endforeach
    </div>
  </div>
</div>

{{-- MAIN LAYOUT --}}
<div class="cmp-layout">

  {{-- LEFT: TECHNICIAN LIST --}}
  <div>
    <div class="panel-title">Step 2 — Drag to compare</div>
    <div class="tech-list" id="tech-list">

      @foreach($techsData as $tech)
      <div class="tech-list-item" draggable="true" data-id="{{ $tech['id'] }}" data-cat="{{ implode(' ', $tech['catSlugs']) }}" ondragstart="dragStart(event)">
        <div class="tli-avatar">{{ $tech['initial'] }}</div>
        <div class="tli-info"><div class="tli-name">{{ $tech['name'] }}</div><div class="tli-loc">📍 {{ $tech['location'] }}</div></div>
        <div class="tli-rating">★ {{ number_format($tech['rating'], 1) }}</div>
        <div class="tli-drag-hint">⠇⠇</div>
      </div>
      @endforeach
      <div id="no-results" style="display:none;padding:2rem;text-align:center;color:var(--muted);font-size:0.88rem;">
        No technicians found for this category.
      </div>

    </div>{{-- end tech-list --}}
  </div>

  {{-- RIGHT: DROP ZONES --}}
  <div>
    <div class="panel-title">Step 3 — Compare &amp; Choose</div>

    <div class="drop-zones">
      <div class="drop-zone" id="zone-0" ondragover="dragOver(event,0)" ondragleave="dragLeave(event,0)" ondrop="drop(event,0)">
        <div class="drop-zone-num">1</div><div class="drop-zone-hint">Drop here</div>
      </div>
      <div class="drop-zone" id="zone-1" ondragover="dragOver(event,1)" ondragleave="dragLeave(event,1)" ondrop="drop(event,1)">
        <div class="drop-zone-num">2</div><div class="drop-zone-hint">Drop here</div>
      </div>
      <div class="drop-zone" id="zone-2" ondragover="dragOver(event,2)" ondragleave="dragLeave(event,2)" ondrop="drop(event,2)">
        <div class="drop-zone-num">3</div><div class="drop-zone-hint">Drop here</div>
      </div>
    </div>

    {{-- DROP PROMPT --}}
    <div id="drop-prompt" class="drop-prompt">
      <div class="drop-prompt-icon">⠿</div>
      <p style="font-size:0.9rem;font-weight:600;margin-bottom:0.4rem;">Drag at least 2 technicians here</p>
      <p style="font-size:0.8rem;">The comparison table will appear automatically</p>
    </div>

    {{-- COMPARISON TABLE --}}
    <div id="cmp-table-wrap" class="cmp-table-wrap">
      <table class="cmp-table">
        <thead>
          <tr>
            <th>Technician</th>
            <th id="th-0" style="display:none;"></th>
            <th id="th-1" style="display:none;"></th>
            <th id="th-2" style="display:none;"></th>
          </tr>
        </thead>
        <tbody id="cmp-tbody"></tbody>
      </table>
    </div>

  </div>
</div>{{-- end cmp-layout --}}

<script>
const techs = {
  @foreach($techsData as $tech)
  '{{ $tech['id'] }}': {
    name:    '{{ addslashes($tech['name']) }}',
    loc:     '{{ addslashes($tech['location']) }}',
    init:    '{{ $tech['initial'] }}',
    avStyle: '',
    rating:  {{ $tech['rating'] }},
    stars:   '{{ $tech['stars'] }}',
    jobs:    {{ $tech['jobs'] }},
    since:   {{ $tech['since'] }},
    price:   '{{ $tech['price'] }}',
    cats:    {!! json_encode($tech['cats']) !!},
    slug:    '{{ $tech['id'] }}',
  },
  @endforeach
};

const slots = [null, null, null];
let draggingId = null;

/* ── CATEGORY FILTER ── */
function filterCat(btn, cat) {
  document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
  btn.classList.add('active');
  let visible = 0;
  document.querySelectorAll('.tech-list-item').forEach(el => {
    const show = cat === 'all' || el.dataset.cat.includes(cat);
    el.style.display = show ? '' : 'none';
    if (show) visible++;
  });
  document.getElementById('no-results').style.display = visible ? 'none' : 'block';
  setStep(2);
}

/* ── STEPS ── */
function setStep(n) {
  [1,2,3].forEach(i => {
    const el = document.getElementById('step-' + i);
    el.classList.remove('active','done');
    if (i < n) el.classList.add('done');
    else if (i === n) el.classList.add('active');
  });
}

/* ── DRAG ── */
function dragStart(e) {
  draggingId = e.currentTarget.dataset.id;
  setTimeout(() => e.currentTarget.classList.add('dragging'), 0);
  e.dataTransfer.effectAllowed = 'move';
}
function dragEnd(e) { e.currentTarget.classList.remove('dragging'); }
function dragOver(e, i)  { e.preventDefault(); document.getElementById('zone-'+i).classList.add('drag-over'); }
function dragLeave(e, i) { document.getElementById('zone-'+i).classList.remove('drag-over'); }

function drop(e, i) {
  e.preventDefault();
  document.getElementById('zone-'+i).classList.remove('drag-over');
  if (!draggingId || !techs[draggingId]) return;
  if (slots.includes(draggingId)) return; // already in a slot
  slots[i] = draggingId;
  document.querySelector('[data-id="'+draggingId+'"]').classList.add('already-added');
  fillSlot(i);
  rebuildTable();
  const filled = slots.filter(Boolean).length;
  setStep(filled >= 2 ? 3 : 2);
}

/* ── FILL SLOT ── */
function fillSlot(i) {
  const t = techs[slots[i]];
  const z = document.getElementById('zone-'+i);
  z.classList.add('filled');
  z.innerHTML = `
    <button class="slot-remove" onclick="removeSlot(${i})">✕</button>
    <div style="text-align:center;">
      <div class="col-avatar-lg" style="${t.avStyle}">${t.init}</div>
      <div class="slot-name">${t.name}</div>
      <div class="slot-loc">${t.loc}</div>
      <div class="slot-rating">${t.stars}</div>
    </div>`;
}

/* ── REMOVE SLOT ── */
function removeSlot(i) {
  const id = slots[i];
  slots[i] = null;
  const el = document.querySelector('[data-id="'+id+'"]');
  if (el) el.classList.remove('already-added');
  const z = document.getElementById('zone-'+i);
  z.classList.remove('filled');
  z.innerHTML = `<div class="drop-zone-num">${i+1}</div><div class="drop-zone-hint">Drop here</div>`;
  z.ondragover  = e => dragOver(e,i);
  z.ondragleave = e => dragLeave(e,i);
  z.ondrop      = e => drop(e,i);
  rebuildTable();
  const filled = slots.filter(Boolean).length;
  setStep(filled >= 2 ? 3 : filled >= 1 ? 2 : 1);
}

/* ── REBUILD TABLE ── */
function rebuildTable() {
  const filled = slots.filter(Boolean).length;
  const wrap   = document.getElementById('cmp-table-wrap');
  const prompt = document.getElementById('drop-prompt');

  if (filled < 2) {
    wrap.classList.remove('visible');
    prompt.style.display = '';
    return;
  }
  wrap.classList.add('visible');
  prompt.style.display = 'none';

  const maxRating = Math.max(...slots.map(id => id ? techs[id].rating : -1));
  const maxJobs   = Math.max(...slots.map(id => id ? techs[id].jobs   : -1));
  const firstIdx  = slots.findIndex(Boolean);

  // Update headers
  for (let i = 0; i < 3; i++) {
    const th  = document.getElementById('th-'+i);
    const id  = slots[i];
    const show = id !== null;
    const hl   = i === firstIdx;
    th.style.display = show ? '' : 'none';
    th.className = hl ? 'hl' : '';
    if (!show) continue;
    const t = techs[id];
    th.innerHTML = `
      <div class="col-avatar-lg" style="${t.avStyle}">${t.init}</div>
      <div style="font-weight:800;font-size:0.95rem;margin:0.5rem 0 0.1rem;">${t.name}</div>
      <div style="font-size:0.78rem;color:var(--muted);">${t.loc}</div>
      <a href="/technicians/${t.slug}" style="display:inline-block;margin-top:0.6rem;font-size:0.75rem;color:var(--amber-lt);font-weight:700;text-decoration:none;">View Profile →</a>`;
  }

  // Rebuild rows
  const rows = [
    { label: 'Rating',       vals: slots.map(id => id ? techs[id].stars+' ('+techs[id].rating+')' : '—'), winner: slots.map(id => id ? techs[id].rating : -1), max: maxRating },
    { label: 'Jobs Done',    vals: slots.map(id => id ? techs[id].jobs+' completed' : '—'), winner: slots.map(id => id ? techs[id].jobs : -1), max: maxJobs },
    { label: 'Price Range',  vals: slots.map(id => id ? techs[id].price : '—'), winner: null },
    { label: 'On Platform',  vals: slots.map(id => id ? 'Since '+techs[id].since : '—'), winner: null },
    { label: 'Specialties',  vals: slots.map(id => id ? techs[id].cats.join(', ') : '—'), winner: null },
  ];

  const tbody = document.getElementById('cmp-tbody');
  tbody.innerHTML = rows.map(row => {
    const cells = [0,1,2].map(i => {
      const id = slots[i];
      if (!id) return '<td style="display:none;"></td>';
      const isWinner = row.winner && row.winner[i] === row.max && row.max > 0;
      const hl = i === firstIdx ? 'background:rgba(212,137,26,0.04);' : '';
      return `<td style="text-align:center;${hl}${isWinner?'color:var(--amber-lt);font-weight:700;':''}">
        ${isWinner ? '<span style="font-size:0.7rem;margin-right:0.3rem;">★</span>' : ''}${row.vals[i]}
      </td>`;
    }).join('');
    return `<tr><td style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--muted);padding:1rem 1.25rem;">${row.label}</td>${cells}</tr>`;
  }).join('');
}

// Attach dragend to list items
document.querySelectorAll('.tech-list-item').forEach(el => {
  el.addEventListener('dragend', dragEnd);
});
</script>
@endsection