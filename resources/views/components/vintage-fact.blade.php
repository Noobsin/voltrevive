{{--
  VINTAGE FACT OF THE DAY WIDGET
  File: resources/views/components/vintage-fact.blade.php

  Frontend: Shows a cycling vintage electronics fact card with refresh button.
  Backend phase: Replace the JS facts array with a real call to:
    GET /api/vintage-fact  (Laravel controller that caches Wikipedia API response daily)
--}}

<div class="vf-widget" id="vintage-fact-widget">
  <div class="vf-header">
    <div class="vf-title">
      <span class="vf-bulb">💡</span>
      Vintage Fact of the Day
    </div>
    <button class="vf-refresh" id="vf-refresh-btn" onclick="loadNextFact()" title="New fact">
      <svg id="vf-refresh-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <polyline points="23 4 23 10 17 10"/>
        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
      </svg>
    </button>
  </div>

  <div class="vf-body">
    <div class="vf-quote-mark">"</div>
    <p class="vf-fact-text" id="vf-text">Loading…</p>
    <div class="vf-source" id="vf-source"></div>
  </div>

  <div class="vf-footer">
    <div class="vf-dots" id="vf-dots"></div>
    <div class="vf-label">Powered by Wikipedia API</div>
  </div>
</div>

<style>
  .vf-widget {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
    animation: fadeUp 0.4s ease;
  }
  .vf-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.9rem 1.25rem; background: var(--bg-card2);
    border-bottom: 1px solid var(--border);
  }
  .vf-title {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--amber);
  }
  .vf-bulb { font-size: 0.95rem; }
  .vf-refresh {
    background: transparent; border: 1px solid var(--border);
    color: var(--muted); width: 28px; height: 28px; border-radius: 6px;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all 0.2s;
  }
  .vf-refresh:hover { border-color: var(--amber); color: var(--amber); }
  .vf-refresh.spinning #vf-refresh-icon { animation: spin 0.5s linear; }
  @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }

  .vf-body { padding: 1.25rem; position: relative; min-height: 100px; }
  .vf-quote-mark {
    font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 900;
    color: rgba(212,137,26,0.15); position: absolute; top: 0.5rem; left: 1rem;
    line-height: 1; pointer-events: none;
  }
  .vf-fact-text {
    font-size: 0.88rem; line-height: 1.7; color: var(--cream);
    padding-left: 0.5rem; position: relative; z-index: 1;
    transition: opacity 0.3s;
  }
  .vf-fact-text.fading { opacity: 0; }
  .vf-source {
    display: flex; align-items: center; gap: 0.4rem;
    font-size: 0.72rem; color: var(--muted); margin-top: 0.75rem;
    padding-left: 0.5rem;
  }

  .vf-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.75rem 1.25rem; border-top: 1px solid var(--border);
    background: var(--bg-card2);
  }
  .vf-dots { display: flex; gap: 0.4rem; }
  .vf-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--border); transition: background 0.2s;
    cursor: pointer;
  }
  .vf-dot.active { background: var(--amber); }
  .vf-label { font-size: 0.68rem; color: var(--muted); letter-spacing: 0.06em; }
  @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
</style>

<script>
  // Fallback facts shown if API is unavailable
  const fallbackFacts = [
    {
      text: "The first transistor radio, the Regency TR-1, launched in 1954 and cost $49.95 — equivalent to roughly $560 today. It sold 150,000 units in under a year.",
      source: "Regency TR-1 · Wikipedia",
      url: "https://en.wikipedia.org/wiki/Regency_TR-1"
    },
    {
      text: "The Sony Walkman TPS-L2, launched in 1979, initially met internal skepticism. It went on to sell over 200 million units across all models.",
      source: "Sony Walkman · Wikipedia",
      url: "https://en.wikipedia.org/wiki/Sony_Walkman"
    },
    {
      text: "The ARP 2600 (1971) was used to create R2-D2's voice in Star Wars. Sound designer Ben Burtt processed his own voice through the synthesizer.",
      source: "ARP 2600 · Wikipedia",
      url: "https://en.wikipedia.org/wiki/ARP_2600"
    },
    {
      text: "The Roland TR-808 (1980) was a commercial failure on release. Decades later it became the backbone of hip-hop, electronic, and pop music worldwide.",
      source: "Roland TR-808 · Wikipedia",
      url: "https://en.wikipedia.org/wiki/Roland_TR-808"
    },
    {
      text: "The Yamaha CS-80 — famously used by Vangelis on Blade Runner — weighs 99 kg and features full polyphonic aftertouch on all 61 keys.",
      source: "Yamaha CS-80 · Wikipedia",
      url: "https://en.wikipedia.org/wiki/Yamaha_CS-80"
    },
  ];

  let facts        = [];   // populated from API
  let currentIndex = 0;

  function renderDots(count, active) {
    const container = document.getElementById('vf-dots');
    if (!container) return;
    container.innerHTML = '';
    for (let i = 0; i < count; i++) {
      const dot = document.createElement('div');
      dot.className = 'vf-dot' + (i === active ? ' active' : '');
      dot.onclick   = () => goToFact(i);
      container.appendChild(dot);
    }
  }

  function displayFact(text, source, url) {
    const textEl   = document.getElementById('vf-text');
    const sourceEl = document.getElementById('vf-source');
    if (!textEl || !sourceEl) return;

    textEl.classList.add('fading');
    setTimeout(() => {
      textEl.textContent = text;
      const icon = `<svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;
      sourceEl.innerHTML = url
        ? `${icon} <a href="${url}" target="_blank" rel="noopener" style="color:var(--muted);text-decoration:none;">${source}</a>`
        : `${icon} ${source}`;
      textEl.classList.remove('fading');
    }, 250);
  }

  function showFact(index) {
    const list = facts.length ? facts : fallbackFacts;
    const f    = list[index % list.length];
    displayFact(f.text, f.source, f.url);
    renderDots(list.length, index % list.length);
  }

  function loadNextFact() {
    const btn = document.getElementById('vf-refresh-btn');
    if (btn) { btn.classList.add('spinning'); setTimeout(() => btn.classList.remove('spinning'), 500); }
    currentIndex = (currentIndex + 1) % (facts.length || fallbackFacts.length);
    showFact(currentIndex);
  }

  function goToFact(index) {
    currentIndex = index;
    showFact(currentIndex);
  }

  // ── Fetch from Laravel backend (Wikipedia API, 5 facts, 24hr cache) ──
  function loadVintageFacts() {
    fetch('/api/vintage-fact')
      .then(r => r.json())
      .then(data => {
        if (data.success && data.facts && data.facts.length) {
          facts        = data.facts;
          currentIndex = 0;
          showFact(0);
          // Auto-rotate every 12 seconds
          setInterval(loadNextFact, 12000);
        } else {
          useFallback();
        }
      })
      .catch(() => useFallback());
  }

  function useFallback() {
    facts        = [];
    currentIndex = 0;
    showFact(0);
    setInterval(loadNextFact, 12000);
  }

  // Init
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadVintageFacts);
  } else {
    loadVintageFacts();
  }
</script>