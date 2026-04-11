@extends('layout')
@section('title', 'Restore What Others Discard')

@section('styles')
<style>
  /* ── HERO ── */
  .hero-section {
    position: relative; z-index: 1;
    min-height: 92vh;
    display: flex; align-items: center;
    padding: 0 2rem;
    overflow: hidden;
  }
  .hero-bg {
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 80% 60% at 70% 50%, rgba(212,137,26,0.07) 0%, transparent 70%);
    pointer-events: none;
  }
  .hero-grid-lines {
    position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(51,43,31,0.35) 1px, transparent 1px),
      linear-gradient(90deg, rgba(51,43,31,0.35) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
    mask-image: radial-gradient(ellipse 90% 90% at 50% 50%, black 30%, transparent 100%);
  }
  .hero-inner {
    max-width: 1400px; margin: 0 auto; width: 100%;
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 4rem; align-items: center;
    padding: 6rem 0 4rem;
  }
  .hero-left { position: relative; z-index: 2; }
  .hero-eyebrow {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(212,137,26,0.1); border: 1px solid rgba(212,137,26,0.25);
    color: var(--amber-lt); padding: 0.35rem 0.9rem; border-radius: 20px;
    font-size: 0.75rem; font-weight: 700; letter-spacing: 0.08em;
    text-transform: uppercase; margin-bottom: 1.5rem;
  }
  .hero-eyebrow::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--amber); animation: pulse 2s infinite; }
  .hero-h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.8rem, 5vw, 4.2rem);
    font-weight: 900; line-height: 1.05;
    letter-spacing: -0.02em;
    margin-bottom: 1.5rem;
  }
  .hero-h1 em { font-style: italic; color: var(--amber-lt); display: block; }
  .hero-p {
    font-size: 1.1rem; color: var(--muted); line-height: 1.7;
    max-width: 480px; margin-bottom: 2.5rem;
  }
  .hero-ctas { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 3rem; }
  .btn-cta-primary {
    background: var(--amber); color: #161310;
    padding: 0.9rem 2rem; border-radius: 10px;
    font-weight: 700; font-size: 1rem;
    text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;
    transition: all 0.2s; border: none; cursor: pointer;
    font-family: 'DM Sans', sans-serif;
  }
  .btn-cta-primary:hover { background: var(--amber-lt); transform: translateY(-2px); }
  .btn-cta-secondary {
    background: transparent; color: var(--cream);
    padding: 0.9rem 2rem; border-radius: 10px;
    font-weight: 600; font-size: 1rem;
    text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;
    border: 1px solid var(--border);
    transition: all 0.2s;
  }
  .btn-cta-secondary:hover { border-color: var(--amber); color: var(--amber-lt); }

  /* technician quick-access cards */
  .tech-quick-card {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:0.4rem; padding:0.9rem 0.5rem;
    background:var(--bg-card); border:1px solid var(--border); border-radius:10px;
    text-decoration:none; transition:all 0.2s; text-align:center;
  }
  .tech-quick-card:hover { border-color:var(--amber); background:rgba(212,137,26,0.06); transform:translateY(-2px); }
  .tqc-icon { font-size:1.3rem; }
  .tqc-label { font-size:0.75rem; font-weight:700; color:var(--cream); }
  .hero-trust {
    display: flex; align-items: center; gap: 1.5rem;
    font-size: 0.82rem; color: var(--muted);
  }
  .trust-avatars {
    display: flex;
  }
  .trust-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    border: 2px solid var(--bg);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.75rem;
    margin-left: -8px; background: var(--bg-card2); color: var(--amber);
  }
  .trust-avatar:first-child { margin-left: 0; }

  /* ── HERO BA SHOWCASE ── */
  .hero-right { position: relative; z-index: 2; }
  .ba-showcase {
    position: relative;
    border-radius: 20px; overflow: hidden;
    border: 1px solid var(--border);
    aspect-ratio: 4/3;
    cursor: ew-resize;
    box-shadow: 0 40px 80px rgba(0,0,0,0.5);
  }
  .ba-showcase-before, .ba-showcase-after {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
  }
  .ba-showcase-before { background: linear-gradient(135deg, #1a120a 0%, #2e1e08 50%, #1a0e04 100%); }
  .ba-showcase-after  { background: linear-gradient(135deg, #0a1a12 0%, #0e2820 50%, #081a10 100%); clip-path: inset(0 40% 0 0); transition: clip-path 0.05s; }
  .ba-showcase-divider {
    position: absolute; top: 0; bottom: 0;
    width: 3px; background: var(--amber);
    left: 60%; transform: translateX(-50%);
    pointer-events: none; z-index: 5;
    transition: left 0.05s;
  }
  .ba-handle {
    position: absolute; top: 50%; left: 60%;
    transform: translate(-50%, -50%);
    width: 44px; height: 44px; border-radius: 50%;
    background: var(--amber); border: 3px solid #fff;
    display: flex; align-items: center; justify-content: center;
    z-index: 6; pointer-events: none;
    font-size: 1rem; font-weight: 900; color: #161310;
    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
    transition: left 0.05s;
  }
  .ba-showcase-label {
    position: absolute; bottom: 16px; z-index: 7;
    background: rgba(22,19,16,0.85); backdrop-filter: blur(6px);
    padding: 0.3rem 0.8rem; border-radius: 6px;
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
  }
  .ba-showcase-label.before { left: 16px; color: var(--muted); }
  .ba-showcase-label.after  { right: 16px; color: var(--amber-lt); }
  /* fake device inside the card */
  .ba-showcase-before::after {
    content: ''; position: absolute; inset: 20%;
    border: 2px solid rgba(212,137,26,0.2); border-radius: 8px;
    background: rgba(212,137,26,0.04);
  }
  .ba-showcase-after::after {
    content: ''; position: absolute; inset: 20%;
    border: 2px solid rgba(74,200,120,0.2); border-radius: 8px;
    background: rgba(74,200,120,0.04);
  }
  .showcase-caption {
    margin-top: 1rem; padding: 0 0.5rem;
    display: flex; align-items: center; justify-content: space-between;
  }
  .showcase-device { font-size: 0.88rem; font-weight: 600; }
  .showcase-tech { font-size: 0.78rem; color: var(--muted); }
  .showcase-rating { color: var(--amber); font-size: 0.82rem; }
  /* floating badges */
  .float-badge {
    position: absolute; background: var(--bg-card);
    border: 1px solid var(--border); border-radius: 12px;
    padding: 0.75rem 1rem; z-index: 8;
    animation: float 4s ease-in-out infinite;
    box-shadow: 0 8px 24px rgba(0,0,0,0.3);
  }
  .float-badge.top-left { top: -16px; left: -20px; animation-delay: 0s; }
  .float-badge.bottom-right { bottom: 60px; right: -20px; animation-delay: 2s; }
  .float-badge-label { font-size: 0.65rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.08em; }
  .float-badge-value { font-size: 1.1rem; font-weight: 700; color: var(--cream); }
  .float-badge-value span { color: var(--amber-lt); }
  @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
  @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

  /* ── STATS BAR ── */
  .stats-bar {
    position: relative; z-index: 1;
    background: var(--bg-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);
  }
  .stats-inner {
    max-width: 1400px; margin: 0 auto;
    padding: 2rem; display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
  }
  .stat-item {
    text-align: center; padding: 1rem 2rem;
    border-right: 1px solid var(--border);
  }
  .stat-item:last-child { border-right: none; }
  .stat-number {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem; font-weight: 900;
    color: var(--amber-lt); line-height: 1;
    margin-bottom: 0.3rem;
  }
  .stat-label { font-size: 0.82rem; color: var(--muted); }

  /* ── SECTION SHARED ── */
  .section-wrap { max-width: 1400px; margin: 0 auto; padding: 5rem 2rem; position: relative; z-index: 1; }
  .section-header { text-align: center; margin-bottom: 3rem; }
  .section-eyebrow {
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.14em;
    text-transform: uppercase; color: var(--amber);
    margin-bottom: 0.75rem; display: block;
  }
  .section-title { font-size: clamp(1.8rem, 3vw, 2.5rem); line-height: 1.15; margin-bottom: 0.75rem; }
  .section-title em { font-style: italic; color: var(--amber-lt); }
  .section-sub { color: var(--muted); font-size: 1rem; max-width: 520px; margin: 0 auto; }

  /* ── FEATURED RESTORATIONS ── */
  .featured-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
  }
  .feat-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
    transition: border-color 0.25s, transform 0.25s;
    animation: fadeUp 0.5s ease both;
  }
  .feat-card:hover { border-color: var(--amber); transform: translateY(-4px); }
  .feat-card:nth-child(1){animation-delay:0.1s} .feat-card:nth-child(2){animation-delay:0.2s} .feat-card:nth-child(3){animation-delay:0.3s}
  .feat-img-row {
    display: grid; grid-template-columns: 1fr 1fr;
    height: 160px; gap: 2px;
  }
  .feat-img {
    background-size: cover; background-position: center;
    position: relative;
  }
  .feat-img-label {
    position: absolute; bottom: 6px; left: 6px;
    font-size: 0.6rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;
    background: rgba(22,19,16,0.8); padding: 0.15rem 0.4rem; border-radius: 3px;
  }
  .feat-img-label.after { color: var(--amber-lt); left: auto; right: 6px; }
  .feat-body { padding: 1.25rem; }
  .feat-category {
    font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase;
    color: var(--amber); margin-bottom: 0.4rem;
  }
  .feat-title { font-family: 'Playfair Display', serif; font-size: 1rem; margin-bottom: 0.5rem; line-height: 1.3; }
  .feat-footer {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--border);
  }
  .feat-tech { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; }
  .feat-avatar { width: 26px; height: 26px; border-radius: 50%; background: var(--bg-card2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: var(--amber); }
  .feat-rating { color: var(--amber); font-size: 0.8rem; font-weight: 600; }
  .feat-img-b1 { background: linear-gradient(135deg,#2a1f0e,#3d2b0f); }
  .feat-img-a1 { background: linear-gradient(135deg,#1a2a1a,#233520); }
  .feat-img-b2 { background: linear-gradient(135deg,#1e1a2a,#2a2038); }
  .feat-img-a2 { background: linear-gradient(135deg,#0e1e2a,#122438); }
  .feat-img-b3 { background: linear-gradient(135deg,#201a0a,#30250e); }
  .feat-img-a3 { background: linear-gradient(135deg,#0a1a20,#0e2530); }
  .view-all-wrap { text-align: center; margin-top: 2.5rem; }
  .btn-view-all {
    display: inline-flex; align-items: center; gap: 0.6rem;
    border: 1px solid var(--border); color: var(--cream);
    padding: 0.8rem 2rem; border-radius: 10px;
    font-size: 0.9rem; font-weight: 600; text-decoration: none;
    transition: all 0.2s;
  }
  .btn-view-all:hover { border-color: var(--amber); color: var(--amber-lt); }

  /* ── HOW IT WORKS ── */
  .how-section { background: var(--bg-card); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
  .how-steps {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 0; position: relative;
  }
  .how-steps::before {
    content: '';
    position: absolute; top: 48px; left: calc(16.66% + 24px); right: calc(16.66% + 24px);
    height: 2px;
    background: linear-gradient(90deg, var(--amber), rgba(212,137,26,0.3), var(--amber));
    pointer-events: none;
  }
  .how-step { padding: 3rem 2.5rem; text-align: center; position: relative; }
  .how-step:not(:last-child) { border-right: 1px solid var(--border); }
  .how-num {
    width: 56px; height: 56px; border-radius: 50%;
    background: rgba(212,137,26,0.1); border: 2px solid var(--amber);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 900; color: var(--amber);
    margin: 0 auto 1.5rem; position: relative; z-index: 1;
  }
  .how-icon { font-size: 1.8rem; margin-bottom: 1rem; }
  .how-title { font-family: 'Playfair Display', serif; font-size: 1.2rem; margin-bottom: 0.75rem; }
  .how-desc { font-size: 0.88rem; color: var(--muted); line-height: 1.65; max-width: 260px; margin: 0 auto; }

  /* ── CATEGORIES STRIP ── */
  .cat-strip {
    display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;
    margin-top: 2rem;
  }
  .cat-pill {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.6rem 1.2rem; border-radius: 30px;
    border: 1px solid var(--border); background: var(--bg-card);
    font-size: 0.85rem; text-decoration: none; color: var(--cream);
    transition: all 0.2s;
  }
  .cat-pill:hover { border-color: var(--amber); color: var(--amber-lt); background: rgba(212,137,26,0.08); }
  .cat-pill-icon { font-size: 1.1rem; }
  .cat-pill-count { font-size: 0.72rem; color: var(--muted); }

  /* ── REPAIR WALL CTA ── */
  .wall-cta {
    background: linear-gradient(135deg, var(--bg-card2) 0%, rgba(212,137,26,0.06) 100%);
    border: 1px solid var(--border); border-radius: 20px;
    padding: 4rem; text-align: center; position: relative; overflow: hidden;
  }
  .wall-cta::before {
    content: ''; position: absolute; inset: 0;
    background: radial-gradient(ellipse 60% 60% at 50% 50%, rgba(212,137,26,0.06), transparent);
    pointer-events: none;
  }
  .wall-cta h2 { font-size: clamp(1.6rem, 3vw, 2.2rem); margin-bottom: 0.75rem; }
  .wall-cta h2 em { font-style: italic; color: var(--amber-lt); }
  .wall-cta p { color: var(--muted); max-width: 480px; margin: 0 auto 2rem; font-size: 0.95rem; line-height: 1.65; }
  .wall-cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }

  /* ── TESTIMONIAL ── */
  .testimonials {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;
  }
  .testimonial-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; padding: 1.5rem;
    animation: fadeUp 0.5s ease both;
  }
  .testimonial-card:nth-child(1){animation-delay:0.1s} .testimonial-card:nth-child(2){animation-delay:0.2s} .testimonial-card:nth-child(3){animation-delay:0.3s}
  .testimonial-stars { color: var(--amber); font-size: 0.9rem; margin-bottom: 0.75rem; }
  .testimonial-text { font-size: 0.88rem; color: var(--muted); line-height: 1.65; margin-bottom: 1.25rem; font-style: italic; }
  .testimonial-author { display: flex; align-items: center; gap: 0.75rem; }
  .testimonial-avatar { width: 38px; height: 38px; border-radius: 50%; background: var(--bg-card2); border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--amber); }
  .testimonial-name { font-size: 0.85rem; font-weight: 600; }
  .testimonial-device { font-size: 0.75rem; color: var(--muted); }

  /* ── FOOTER ── */
  footer {
    background: var(--bg-card); border-top: 1px solid var(--border);
    padding: 3rem 2rem; position: relative; z-index: 1;
  }
  .footer-inner {
    max-width: 1400px; margin: 0 auto;
    display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 3rem;
  }
  .footer-brand p { color: var(--muted); font-size: 0.85rem; line-height: 1.65; margin-top: 0.75rem; max-width: 280px; }
  .footer-col h4 { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); margin-bottom: 1rem; }
  .footer-col a { display: block; color: var(--muted); text-decoration: none; font-size: 0.85rem; margin-bottom: 0.5rem; transition: color 0.2s; }
  .footer-col a:hover { color: var(--cream); }
  .footer-bottom { max-width: 1400px; margin: 2rem auto 0; padding-top: 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; font-size: 0.78rem; color: var(--muted); }

  @keyframes fadeUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }

  @media(max-width:1024px) {
    .hero-inner { grid-template-columns: 1fr; gap: 3rem; }
    .hero-right { order: -1; }
    .featured-grid { grid-template-columns: repeat(2,1fr); }
    .how-steps { grid-template-columns: 1fr; }
    .how-step:not(:last-child) { border-right:none; border-bottom: 1px solid var(--border); }
    .how-steps::before { display: none; }
    .testimonials { grid-template-columns: 1fr; }
    .footer-inner { grid-template-columns: 1fr 1fr; }
    .stats-inner { grid-template-columns: repeat(2,1fr); }
  }
  @media(max-width:600px) {
    .hero-section { padding: 0 1rem; }
    .hero-inner { padding: 3rem 0 2rem; }
    .hero-ctas { flex-direction: column; }
    .featured-grid { grid-template-columns: 1fr; }
    .section-wrap { padding: 3rem 1rem; }
    .wall-cta { padding: 2rem 1.5rem; }
    .footer-inner { grid-template-columns: 1fr; gap: 2rem; }
    .stats-inner { grid-template-columns: repeat(2,1fr); }
    .stat-item { padding: 1rem; }
  }

  /* ── HERO JOIN DROPDOWN ── */
  .hero-join-wrap { position:relative; display:inline-flex; }
  .btn-join-hero {
    background:transparent; color:var(--cream);
    padding:0.9rem 2rem; border-radius:10px;
    font-weight:600; font-size:1rem;
    border:1px solid var(--border);
    cursor:pointer; font-family:'DM Sans',sans-serif;
    display:inline-flex; align-items:center; gap:0.5rem;
    transition:all 0.2s;
  }
  .btn-join-hero:hover { border-color:var(--amber); color:var(--amber-lt); }
  .hero-join-dd {
    display:none; position:absolute; left:0; top:calc(100% + 8px);
    background:var(--bg-card); border:1px solid var(--border);
    border-radius:10px; overflow:hidden; min-width:230px;
    box-shadow:0 12px 40px rgba(0,0,0,0.6); z-index:200;
  }
  .hero-join-dd.open { display:block; animation:ddFadeH 0.15s ease; }
  @keyframes ddFadeH { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
  .hero-join-opt {
    display:flex; align-items:center; gap:0.75rem;
    padding:0.9rem 1.15rem; text-decoration:none;
    color:var(--cream); transition:background 0.15s; font-size:0.88rem;
  }
  .hero-join-opt:hover { background:var(--bg-card2); }
  .hero-join-opt-icon { font-size:1.15rem; flex-shrink:0; }
  .hero-join-opt-label { font-weight:600; display:block; }
  .hero-join-opt-sub { font-size:0.72rem; color:var(--muted); display:block; margin-top:0.1rem; }
</style>
@endsection

@section('content')

{{-- ── HERO ── --}}
<section class="hero-section">
  <div class="hero-bg"></div>
  <div class="hero-grid-lines"></div>
  <div class="hero-inner">

    <div class="hero-left">
      <div class="hero-eyebrow">The Vintage Electronics Restoration Marketplace</div>
      <h1 class="hero-h1">
        Restore What
        <em>Others Discard.</em>
      </h1>
      <p class="hero-p">
        Connect with verified restoration technicians who breathe life back into vintage synthesizers, retro consoles, Hi-Fi amplifiers, and rare electronics. Every listing is a proven portfolio piece.
      </p>
      {{-- ── GUEST CTAs ── --}}
      <div class="hero-ctas" id="hero-ctas-guest">
        <a href="/browse" class="btn-cta-primary">
          Browse Restorations
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </a>

        {{-- Join VoltRevive dropdown -- mirrors navbar --}}
        <div class="hero-join-wrap">
          <button class="btn-join-hero" onclick="toggleHeroJoinDd(event)">
            Join VoltRevive
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="hero-join-dd" id="hero-join-dd">
            <a href="/register" class="hero-join-opt">
              <span class="hero-join-opt-icon">&#x1F3AE;</span>
              <span>
                <span class="hero-join-opt-label">Join as Collector</span>
                <span class="hero-join-opt-sub">Book restorers, track your devices</span>
              </span>
            </a>
            <a href="/register?role=technician" class="hero-join-opt">
              <span class="hero-join-opt-icon">&#x1F527;</span>
              <span>
                <span class="hero-join-opt-label">Join as Technician</span>
                <span class="hero-join-opt-sub">List your skills, get hired</span>
              </span>
            </a>
          </div>
        </div>

        <a href="/events" class="btn-cta-secondary">View Events</a>
      </div>

      {{-- ── COLLECTOR CTAs ── --}}
      <div class="hero-ctas" id="hero-ctas-collector" style="display:none;">
        <a href="/browse" class="btn-cta-primary">
          Browse Restorations
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
        <a href="/my-devices" class="btn-cta-secondary">📦 My Devices</a>
        <a href="/events" class="btn-cta-secondary">View Events</a>
      </div>

      {{-- ── TECHNICIAN CTAs (6 quick-action cards) ── --}}
      <div id="hero-ctas-technician" style="display:none;width:100%;margin-top:0.5rem;">
        <p style="font-size:0.78rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:1rem;">Quick Access</p>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem;max-width:560px;">
          <a href="/technician-portfolio" class="tech-quick-card">
            <span class="tqc-icon">🖼️</span>
            <span class="tqc-label">My Portfolio</span>
          </a>
          <a href="{{ $currentJobId ? '/jobs/'.$currentJobId : '/technician-dashboard' }}" class="tech-quick-card">
            <span class="tqc-icon">🔧</span>
            <span class="tqc-label">Current Job</span>
          </a>
          <a href="/technician-dashboard" class="tech-quick-card">
            <span class="tqc-icon">📊</span>
            <span class="tqc-label">My Dashboard</span>
          </a>
          <a href="/repair-wall" class="tech-quick-card">
            <span class="tqc-icon">📋</span>
            <span class="tqc-label">Repair Wall</span>
          </a>
          <a href="/browse" class="tech-quick-card">
            <span class="tqc-icon">🔍</span>
            <span class="tqc-label">Browse</span>
          </a>
          <a href="/services/create" class="tech-quick-card" style="border-color:rgba(212,137,26,0.4);background:rgba(212,137,26,0.06);">
            <span class="tqc-icon">➕</span>
            <span class="tqc-label" style="color:var(--amber-lt);">List Service</span>
          </a>
        </div>
      </div>
      <div class="hero-trust">
        <div class="trust-avatars">
          <div class="trust-avatar">M</div>
          <div class="trust-avatar">E</div>
          <div class="trust-avatar">J</div>
          <div class="trust-avatar">R</div>
          <div class="trust-avatar">+</div>
        </div>
        <span>Trusted by <strong style="color:var(--cream)">2,400+</strong> collectors worldwide</span>
      </div>
    </div>

    <div class="hero-right">
      <div class="float-badge top-left">
        <div class="float-badge-label">Completed Jobs</div>
        <div class="float-badge-value"><span>1,847</span> restorations</div>
      </div>

      <div class="ba-showcase" id="hero-showcase">
        <div class="ba-showcase-before" id="sc-before"></div>
        <div class="ba-showcase-after" id="sc-after"></div>
        <div class="ba-showcase-divider" id="sc-divider"></div>
        <div class="ba-handle" id="sc-handle">⟺</div>
        <span class="ba-showcase-label before">Before</span>
        <span class="ba-showcase-label after">After</span>
      </div>

      <div class="showcase-caption">
        <div>
          <div class="showcase-device">Roland Juno-106 — Voice Chip Restoration</div>
          <div class="showcase-tech">by Marcus H. · Berlin, Germany</div>
        </div>
        <div class="showcase-rating">★★★★★ 4.9</div>
      </div>

      <div class="float-badge bottom-right">
        <div class="float-badge-label">Average Rating</div>
        <div class="float-badge-value">★ <span>4.8</span> / 5.0</div>
      </div>
    </div>

  </div>
</section>

{{-- ── STATS BAR ── --}}
<div class="stats-bar">
  <div class="stats-inner">
    <div class="stat-item">
      <div class="stat-number" data-target="1847">0</div>
      <div class="stat-label">Restorations Completed</div>
    </div>
    <div class="stat-item">
      <div class="stat-number" data-target="312">0</div>
      <div class="stat-label">Verified Technicians</div>
    </div>
    <div class="stat-item">
      <div class="stat-number" data-target="2400">0</div>
      <div class="stat-label">Registered Collectors</div>
    </div>
    <div class="stat-item">
      <div class="stat-number" data-target="24">0</div>
      <div class="stat-label">Device Categories</div>
    </div>
  </div>
</div>

{{-- ── FEATURED RESTORATIONS ── --}}
<div class="section-wrap">
  <div class="section-header">
    <span class="section-eyebrow">Featured Work</span>
    <h2 class="section-title">Recently <em>Restored</em></h2>
    <p class="section-sub">Hand-picked restorations showcasing the depth of craft on VoltRevive.</p>
  </div>

  <div class="featured-grid">

    <div class="feat-card">
      <div class="feat-img-row">
        <div class="feat-img feat-img-b1">
          <span class="feat-img-label">Before</span>
        </div>
        <div class="feat-img feat-img-a1">
          <span class="feat-img-label after">After</span>
        </div>
      </div>
      <div class="feat-body">
        <div class="feat-category">Synthesizer</div>
        <div class="feat-title">Roland Juno-106 Full Voice Chip Restoration</div>
        <p style="font-size:0.8rem;color:var(--muted);line-height:1.5;">Complete 80017A chip replacement, key contact cleaning, and factory recalibration.</p>
        <div class="feat-footer">
          <div class="feat-tech">
            <div class="feat-avatar">M</div>
            Marcus H.
          </div>
          <div class="feat-rating">★ 4.9</div>
        </div>
      </div>
    </div>

    <div class="feat-card">
      <div class="feat-img-row">
        <div class="feat-img feat-img-b2">
          <span class="feat-img-label">Before</span>
        </div>
        <div class="feat-img feat-img-a2">
          <span class="feat-img-label after">After</span>
        </div>
      </div>
      <div class="feat-body">
        <div class="feat-category">Vintage Radio</div>
        <div class="feat-title">Zenith Trans-Oceanic Radio — Full Rebuild</div>
        <p style="font-size:0.8rem;color:var(--muted);line-height:1.5;">Capacitor reformation, dial lamp replacement, alignment, and cabinet restoration.</p>
        <div class="feat-footer">
          <div class="feat-tech">
            <div class="feat-avatar">E</div>
            Elena V.
          </div>
          <div class="feat-rating">★ 4.8</div>
        </div>
      </div>
    </div>

    <div class="feat-card">
      <div class="feat-img-row">
        <div class="feat-img feat-img-b3">
          <span class="feat-img-label">Before</span>
        </div>
        <div class="feat-img feat-img-a3">
          <span class="feat-img-label after">After</span>
        </div>
      </div>
      <div class="feat-body">
        <div class="feat-category">Hi-Fi Audio</div>
        <div class="feat-title">McIntosh MA5100 Amplifier Rebuild</div>
        <p style="font-size:0.8rem;color:var(--muted);line-height:1.5;">Full recap with premium Nichicon caps, bias adjustment, and VU meter restoration.</p>
        <div class="feat-footer">
          <div class="feat-tech">
            <div class="feat-avatar">R</div>
            Rosa M.
          </div>
          <div class="feat-rating">★ 5.0</div>
        </div>
      </div>
    </div>

  </div>

  <div class="view-all-wrap">
    <a href="/browse" class="btn-view-all">
      View All Restorations
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
  </div>
</div>

{{-- ── HOW IT WORKS ── --}}
<section class="how-section">
  <div class="section-wrap">
    <div class="section-header">
      <span class="section-eyebrow">The Process</span>
      <h2 class="section-title">How <em>VoltRevive</em> Works</h2>
      <p class="section-sub">Three steps from broken relic to fully restored collector's piece.</p>
    </div>

    <div class="how-steps">
      <div class="how-step">
        <div class="how-num">1</div>
        <div class="how-icon">🔍</div>
        <h3 class="how-title">Find Your Technician</h3>
        <p class="how-desc">Browse verified technicians by device category or keyword. Compare ratings, portfolios, and price ranges side by side. Every listing is backed by real Before & After proof.</p>
      </div>
      <div class="how-step">
        <div class="how-num">2</div>
        <div class="how-icon">📦</div>
        <h3 class="how-title">Book, Diagnose & Ship</h3>
        <p class="how-desc">Book a repair slot, run a live video diagnostic session via Jitsi to verify the fault before shipping, and pay securely via SSLCommerz — funds held in escrow until completion.</p>
      </div>
      <div class="how-step">
        <div class="how-num">3</div>
        <div class="how-icon">⚡</div>
        <h3 class="how-title">Receive & Review</h3>
        <p class="how-desc">Your device ships back fully restored. Review the technician's work, and the Before & After pair is added to both your Collector Portfolio and their public Technician Portfolio.</p>
      </div>
    </div>
  </div>
</section>

{{-- ── BROWSE BY CATEGORY ── --}}
<div class="section-wrap" style="padding-top:3rem;padding-bottom:3rem;">
  <div class="section-header">
    <span class="section-eyebrow">Browse by Category</span>
    <h2 class="section-title">What Can We <em>Restore?</em></h2>
  </div>
  <div class="cat-strip">
    <a href="/browse?category=synthesizers" class="cat-pill">
      <span class="cat-pill-icon">🎹</span> Synthesizers <span class="cat-pill-count">8 listings</span>
    </a>
    <a href="/browse?category=retro-gaming" class="cat-pill">
      <span class="cat-pill-icon">🎮</span> Retro Gaming <span class="cat-pill-count">6 listings</span>
    </a>
    <a href="/browse?category=hifi-audio" class="cat-pill">
      <span class="cat-pill-icon">🔊</span> Hi-Fi Audio <span class="cat-pill-count">5 listings</span>
    </a>
    <a href="/browse?category=vintage-radio" class="cat-pill">
      <span class="cat-pill-icon">📻</span> Vintage Radio <span class="cat-pill-count">3 listings</span>
    </a>
    <a href="/browse?category=cameras" class="cat-pill">
      <span class="cat-pill-icon">📷</span> Cameras <span class="cat-pill-count">2 listings</span>
    </a>
    <a href="/browse?category=vintage-computers" class="cat-pill">
      <span class="cat-pill-icon">🖥️</span> Vintage Computers <span class="cat-pill-count">coming soon</span>
    </a>
  </div>
</div>

{{-- ── TESTIMONIALS ── --}}
<div class="section-wrap" style="padding-top:2rem;">
  <div class="section-header">
    <span class="section-eyebrow">Collector Stories</span>
    <h2 class="section-title">What Collectors <em>Say</em></h2>
  </div>
  <div class="testimonials">
    <div class="testimonial-card">
      <div class="testimonial-stars">★★★★★</div>
      <p class="testimonial-text">"Marcus brought my Juno-106 back from the dead. Every single voice chip was gone — now it sounds better than the day it left the factory. The live diagnostic session before shipping was a brilliant touch."</p>
      <div class="testimonial-author">
        <div class="testimonial-avatar">S</div>
        <div>
          <div class="testimonial-name">Sarah K.</div>
          <div class="testimonial-device">Roland Juno-106 owner</div>
        </div>
      </div>
    </div>
    <div class="testimonial-card">
      <div class="testimonial-stars">★★★★★</div>
      <p class="testimonial-text">"I'd been searching for someone who understood the Trans-Oceanic for two years. Elena knew it inside out. The escrow payment system made me feel completely safe sending a £400 radio across the Atlantic."</p>
      <div class="testimonial-author">
        <div class="testimonial-avatar">D</div>
        <div>
          <div class="testimonial-name">David M.</div>
          <div class="testimonial-device">Zenith Trans-Oceanic owner</div>
        </div>
      </div>
    </div>
    <div class="testimonial-card">
      <div class="testimonial-stars">★★★★☆</div>
      <p class="testimonial-text">"The comparison page saved me hours. I could see Rosa, Marcus, and Lars side by side — ratings, price ranges, completed jobs. Chose Rosa for my McIntosh and couldn't be happier with the result."</p>
      <div class="testimonial-author">
        <div class="testimonial-avatar">A</div>
        <div>
          <div class="testimonial-name">Akira T.</div>
          <div class="testimonial-device">McIntosh MA5100 owner</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ── REPAIR WALL CTA ── --}}
<div class="section-wrap" style="padding-top:2rem;">
  <div class="wall-cta">
    <h2>Can't Find a Technician for Your <em>Rare Device?</em></h2>
    <p>Post a rescue appeal on the Repair Request Wall. Technicians and collectors can flag interest, and admins use the data to recruit specialists for underserved devices.</p>
    <div class="wall-cta-btns">
      <a href="/repair-wall" class="btn-cta-primary">Post a Rescue Appeal</a>
      <a href="/services/create" class="btn-cta-secondary">List Your Service</a>
    </div>
  </div>
</div>

{{-- ── FOOTER ── --}}
<footer>
  <div class="footer-inner">
    <div class="footer-brand">
      <div style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:900;color:var(--amber-lt);">Volt<span style="color:var(--cream)">Revive</span></div>
      <p>The peer-to-peer marketplace for vintage electronics restoration. Connecting collectors with verified technicians worldwide.</p>
    </div>
    <div class="footer-col">
      <h4>Marketplace</h4>
      <a href="/browse">Browse Restorations</a>
      <a href="/services/create">List a Service</a>
      <a href="/repair-wall">Repair Request Wall</a>
      <a href="/events">Community Events</a>
    </div>
    <div class="footer-col">
      <h4>Account</h4>
      <a href="/register">Join VoltRevive</a>
      <a href="/login">Sign In</a>
      <a href="/dashboard">My Dashboard</a>
    </div>
    <div class="footer-col">
      <h4>Support</h4>
      <a href="/contact">Contact Us</a>
      <a href="#">How It Works</a>
      <a href="#">Shipping Guide</a>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© 2026 VoltRevive. Built for BRAC University CSE471.</span>
    <span>Laravel · TailwindCSS · MySQL</span>
  </div>
</footer>

<script>

  // ── HERO JOIN DROPDOWN ──
  function toggleHeroJoinDd() {
    document.getElementById('hero-join-dd').classList.toggle('open');
  }
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.hero-join-wrap')) {
      var dd = document.getElementById('hero-join-dd');
      if (dd) dd.classList.remove('open');
    }
  });

  // ── HOME ROLE DISPLAY ──
  function applyHomeRole() {
    const role = localStorage.getItem('vr_role');
    document.getElementById('hero-ctas-guest').style.display      = (role === null || role === undefined || role === '') ? 'flex' : 'none';
    document.getElementById('hero-ctas-collector').style.display  = (role === 'collector')  ? 'flex'  : 'none';
    document.getElementById('hero-ctas-technician').style.display = (role === 'technician') ? 'block' : 'none';
  }
  applyHomeRole();
  window.addEventListener('vr-role-changed', applyHomeRole);

  // ── DRAGGABLE BEFORE/AFTER HERO SHOWCASE ──
  const showcase = document.getElementById('hero-showcase');
  const after = document.getElementById('sc-after');
  const divider = document.getElementById('sc-divider');
  const handle = document.getElementById('sc-handle');
  let dragging = false;

  function setPos(pct) {
    pct = Math.max(5, Math.min(95, pct));
    after.style.clipPath = `inset(0 ${100 - pct}% 0 0)`;
    divider.style.left = pct + '%';
    handle.style.left = pct + '%';
  }
  setPos(60);

  showcase.addEventListener('mousedown', () => dragging = true);
  window.addEventListener('mouseup', () => dragging = false);
  showcase.addEventListener('mousemove', e => {
    if (!dragging) return;
    const rect = showcase.getBoundingClientRect();
    setPos(((e.clientX - rect.left) / rect.width) * 100);
  });
  showcase.addEventListener('touchmove', e => {
    e.preventDefault();
    const rect = showcase.getBoundingClientRect();
    setPos(((e.touches[0].clientX - rect.left) / rect.width) * 100);
  }, { passive: false });
  // Auto-animate on load
  let autoPos = 60, autoDir = -1;
  const autoAnim = setInterval(() => {
    autoPos += autoDir * 0.4;
    if (autoPos < 30 || autoPos > 75) autoDir *= -1;
    setPos(autoPos);
  }, 30);
  showcase.addEventListener('mousedown', () => clearInterval(autoAnim));
  showcase.addEventListener('touchstart', () => clearInterval(autoAnim));

  // ── COUNTER ANIMATION ──
  const counters = document.querySelectorAll('.stat-number[data-target]');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target = parseInt(el.dataset.target);
      const duration = 1800;
      const step = target / (duration / 16);
      let current = 0;
      const timer = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = Math.floor(current).toLocaleString();
        if (current >= target) clearInterval(timer);
      }, 16);
      observer.unobserve(el);
    });
  }, { threshold: 0.3 });
  counters.forEach(c => observer.observe(c));
</script>
@endsection