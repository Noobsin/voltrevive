@extends('layout')
@section('title')
Job #{{ $job->reference }} — {{ $job->booking->device_name }}
@endsection

@section('styles')
<style>
  /* ── PAGE WRAP ── */
  .job-wrap {
    max-width: 1400px; margin: 0 auto;
    padding: 2.5rem 2rem 5rem;
    display: grid; grid-template-columns: 1fr 380px;
    gap: 2rem; position: relative; z-index: 1;
  }

  /* ── BREADCRUMB ── */
  .job-breadcrumb {
    max-width: 1400px; margin: 0 auto;
    padding: 1.5rem 2rem 0; position: relative; z-index: 1;
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.82rem; color: var(--muted);
  }
  .job-breadcrumb a { color: var(--muted); text-decoration: none; transition: color 0.2s; }
  .job-breadcrumb a:hover { color: var(--cream); }
  .job-breadcrumb span { color: var(--border); }

  /* ── PERSPECTIVE TOGGLE ── */

  /* ── JOB HEADER ── */
  .job-header {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; padding: 1.5rem; margin-bottom: 1.5rem;
  }
  .job-header-top {
    display: flex; align-items: flex-start;
    justify-content: space-between; gap: 1rem; flex-wrap: wrap;
    margin-bottom: 1.25rem;
  }
  .job-status-badge {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.35rem 0.9rem; border-radius: 20px;
    font-size: 0.75rem; font-weight: 700; letter-spacing: 0.05em;
    text-transform: uppercase;
  }
  .status-inprogress {
    background: rgba(212,137,26,0.12); border: 1px solid rgba(212,137,26,0.3);
    color: var(--amber-lt);
  }
  .status-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--amber); animation: pulse 2s infinite; }
  @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
  .job-id { font-size: 0.78rem; color: var(--muted); }
  .job-title { font-family: 'Playfair Display', serif; font-size: 1.5rem; margin-bottom: 0.25rem; }
  .job-meta-row {
    display: flex; flex-wrap: wrap; gap: 1.5rem;
    font-size: 0.82rem; color: var(--muted);
  }
  .job-meta-item { display: flex; align-items: center; gap: 0.4rem; }
  .job-meta-item strong { color: var(--cream); }


  /* ── TIMELINE ── */
  .timeline-panel {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden; margin-bottom: 1.5rem;
  }
  .timeline-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.25rem; background: var(--bg-card2);
    border-bottom: 1px solid var(--border);
  }
  .timeline-title {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.82rem; font-weight: 700; letter-spacing: 0.06em;
    text-transform: uppercase; color: var(--amber);
  }
  .timeline-job-id { font-size: 0.75rem; color: var(--muted); }
  .timeline-track { padding: 1.25rem 1.25rem 0.5rem; }

  .tl-step {
    display: grid; grid-template-columns: 32px 1fr;
    gap: 0 1rem; opacity: 1; transition: opacity 0.2s;
  }
  .tl-step.pending { opacity: 0.42; }
  .tl-node-col { display: flex; flex-direction: column; align-items: center; }
  .tl-node {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; position: relative; z-index: 1;
  }
  .tl-node.done { background: var(--amber); border: 2px solid var(--amber); color: #161310; }
  .tl-node.active { background: rgba(212,137,26,0.15); border: 2px solid var(--amber); }
  .tl-node.active-green { background: rgba(93,224,176,0.12); border: 2px solid #5de0b0; }
  .tl-node.active-blue  { background: rgba(130,170,255,0.1); border: 2px solid #8ab4ff; }
  .tl-node.pending { background: var(--bg-card2); border: 2px solid var(--border); color: var(--muted); }

  .tl-pulse {
    width: 10px; height: 10px; border-radius: 50%; background: var(--amber);
  }
  .tl-pulse::after {
    content: ''; position: absolute; inset: -5px;
    border-radius: 50%; border: 2px solid var(--amber);
    animation: ripple 1.5s ease-out infinite;
  }
  /* green pulse for Completed step */
  .tl-pulse.green { background: #5de0b0; }
  .tl-pulse.green::after { border-color: #5de0b0; }
  /* muted pulse inside a pending node — shows shape but no animation */
  .tl-node.pending .tl-pulse { background: var(--border); }
  .tl-node.pending .tl-pulse::after { display: none; }
  /* pre-ticked steps badge */
  .tl-confirm-badge {
    background: rgba(93,224,176,0.1); border: 1px solid rgba(93,224,176,0.25);
    color: #5de0b0; font-size: 0.65rem; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    padding: 0.1rem 0.45rem; border-radius: 20px;
  }
  /* completed badge — green variant */
  .tl-completed-badge {
    background: rgba(93,224,176,0.12); border: 1px solid rgba(93,224,176,0.3);
    color: #5de0b0; font-size: 0.65rem; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    padding: 0.1rem 0.45rem; border-radius: 20px;
    animation: pulse-badge 2s ease infinite;
  }
  .tl-ship-badge {
    background: rgba(130,170,255,0.1); border: 1px solid rgba(130,170,255,0.3);
    color: #8ab4ff; font-size: 0.65rem; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    padding: 0.1rem 0.45rem; border-radius: 20px;
    animation: pulse-badge 2s ease infinite;
  }
  @keyframes ripple { 0%{opacity:1;transform:scale(1)} 100%{opacity:0;transform:scale(2.2)} }

  .tl-line { flex: 1; width: 2px; background: var(--border); margin: 4px 0; min-height: 24px; }
  .tl-step.done .tl-line { background: var(--amber); }
  .tl-step.active .tl-line { background: linear-gradient(to bottom, var(--amber), var(--border)); }
  .tl-step.active-green .tl-line { background: linear-gradient(to bottom, #5de0b0, var(--border)); }
  .tl-step.active-blue  .tl-line { background: linear-gradient(to bottom, #8ab4ff, var(--border)); }

  .tl-content { padding-bottom: 1.25rem; }
  .tl-step.last .tl-content { padding-bottom: 0.5rem; }

  .tl-label {
    font-size: 0.9rem; font-weight: 700; margin-bottom: 0.25rem;
    display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
  }
  .tl-active-badge {
    background: rgba(212,137,26,0.15); border: 1px solid rgba(212,137,26,0.3);
    color: var(--amber-lt); font-size: 0.65rem; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    padding: 0.1rem 0.45rem; border-radius: 20px;
    animation: pulse-badge 2s ease infinite;
  }
  .tl-pay-badge {
    background: rgba(212,137,26,0.12); border: 1px solid rgba(212,137,26,0.35);
    color: var(--amber); font-size: 0.65rem; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    padding: 0.1rem 0.45rem; border-radius: 20px;
  }
  @keyframes pulse-badge { 0%,100%{opacity:1} 50%{opacity:0.6} }
  .tl-desc { font-size: 0.8rem; color: var(--muted); line-height: 1.55; margin-bottom: 0.3rem; }
  .tl-timestamp { font-size: 0.72rem; color: var(--muted); }
  .tl-step.done .tl-timestamp { color: var(--amber-lt); }
  .tl-pending-text { font-style: italic; }

  .timeline-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.9rem 1.25rem; border-top: 1px solid var(--border);
    background: var(--bg-card2); gap: 1rem; flex-wrap: wrap;
  }
  .tl-progress-wrap { flex: 1; min-width: 160px; }
  .tl-progress-bar { height: 5px; background: var(--border); border-radius: 3px; overflow: hidden; margin-bottom: 0.35rem; }
  .tl-progress-fill { height: 100%; background: var(--amber); border-radius: 3px; transition: width 0.6s ease; }
  .tl-progress-label { font-size: 0.72rem; color: var(--muted); }
  .tl-est { display: flex; align-items: center; gap: 0.35rem; font-size: 0.78rem; color: var(--muted); flex-shrink: 0; }
  .tl-est strong { color: var(--cream); }

  /* ── MESSAGING ── */
  .msg-panel {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
  }
  .msg-header {
    padding: 1rem 1.25rem; background: var(--bg-card2);
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
  }
  .msg-header-title {
    font-size: 0.82rem; font-weight: 700; letter-spacing: 0.06em;
    text-transform: uppercase; color: var(--amber);
    display: flex; align-items: center; gap: 0.5rem;
  }
  .msg-count {
    background: var(--amber); color: #161310;
    font-size: 0.65rem; font-weight: 700; padding: 0.1rem 0.45rem;
    border-radius: 20px;
  }
  .msg-thread {
    padding: 1.25rem; display: flex; flex-direction: column;
    gap: 1rem; max-height: 420px; overflow-y: auto;
  }
  .msg-thread::-webkit-scrollbar { width: 4px; }
  .msg-thread::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
  .msg-bubble { display: flex; gap: 0.75rem; animation: fadeUp 0.3s ease; }
  .msg-bubble.own { flex-direction: row-reverse; }
  .msg-avatar {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    background: var(--bg-card2); border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; font-weight: 700; color: var(--amber);
  }
  .msg-content { max-width: 75%; }
  .msg-name-time { font-size: 0.72rem; color: var(--muted); margin-bottom: 0.3rem; display: flex; gap: 0.5rem; }
  .msg-bubble.own .msg-name-time { flex-direction: row-reverse; }
  .msg-text {
    background: var(--bg-card2); border: 1px solid var(--border);
    border-radius: 10px; border-top-left-radius: 2px;
    padding: 0.7rem 0.9rem; font-size: 0.85rem; line-height: 1.6;
  }
  .msg-bubble.own .msg-text {
    background: rgba(212,137,26,0.1); border-color: rgba(212,137,26,0.2);
    border-top-right-radius: 2px; border-top-left-radius: 10px;
  }
  .msg-input-row {
    padding: 1rem 1.25rem; border-top: 1px solid var(--border);
    display: flex; gap: 0.75rem; align-items: flex-end;
    background: var(--bg-card2);
  }
  .msg-textarea {
    flex: 1; background: var(--bg); border: 1px solid var(--border);
    border-radius: 8px; color: var(--cream); font-family: 'DM Sans', sans-serif;
    font-size: 0.88rem; padding: 0.65rem 0.9rem; outline: none;
    resize: none; min-height: 42px; max-height: 120px;
    transition: border-color 0.2s;
  }
  .msg-textarea:focus { border-color: var(--amber); }
  .msg-textarea::placeholder { color: var(--muted); }
  .btn-send {
    background: var(--amber); border: none; color: #161310;
    padding: 0.65rem 1.1rem; border-radius: 8px; cursor: pointer;
    font-family: 'DM Sans', sans-serif; font-weight: 700;
    font-size: 0.85rem; transition: background 0.2s; flex-shrink: 0;
    display: flex; align-items: center; gap: 0.4rem;
  }
  .btn-send:hover { background: var(--amber-lt); }

  /* ── SIDEBAR CARDS ── */
  .job-sidebar { display: flex; flex-direction: column; gap: 1.25rem; }
  .side-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
  }
  .side-card-header {
    padding: 0.9rem 1.25rem; background: var(--bg-card2);
    border-bottom: 1px solid var(--border);
    font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--muted);
    display: flex; align-items: center; gap: 0.4rem;
  }
  .side-card-body { padding: 1.25rem; }

  /* payment */
  .payment-amount { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 900; color: var(--amber-lt); margin-bottom: 0.85rem; }
  .payment-status-row {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem;
  }
  .pay-pending { background: rgba(212,137,26,0.07); border: 1px solid rgba(212,137,26,0.2); }
  

  /* job details */
  .detail-row {
    display: flex; justify-content: space-between; align-items: baseline;
    padding: 0.65rem 0; border-bottom: 1px solid var(--border); gap: 0.75rem;
    font-size: 0.85rem;
  }
  .detail-row:last-child { border-bottom: none; }
  .detail-row .label { color: var(--muted); flex-shrink: 0; }
  .detail-row .value { color: var(--cream); text-align: right; font-weight: 500; }

  /* actions */
  .action-btns { display: flex; flex-direction: column; gap: 0.6rem; }
  .btn-action-danger {
    width: 100%; background: rgba(200,60,60,0.08); border: 1px solid rgba(200,60,60,0.25);
    color: #f09090; padding: 0.7rem; border-radius: 8px; cursor: pointer;
    font-family: 'DM Sans', sans-serif; font-size: 0.88rem; font-weight: 600;
    transition: all 0.2s;
  }
  .btn-action-danger:hover { background: rgba(200,60,60,0.16); }
  .sms-note {
    display: flex; align-items: flex-start; gap: 0.5rem;
    margin-top: 0.85rem; padding: 0.75rem;
    background: rgba(212,137,26,0.05); border: 1px solid rgba(212,137,26,0.15);
    border-radius: 8px; font-family: 'DM Sans', sans-serif;
    font-size: 0.78rem; color: var(--muted); line-height: 1.55;
  }
  .sms-note span:first-child { flex-shrink: 0; margin-top: 0.05rem; }
  /* Review button */
  .btn-pay-now {
    width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    background: #5de0b0; color: #161310; padding: 0.75rem 1rem; border-radius: 8px;
    font-family: 'DM Sans', sans-serif; font-size: 0.88rem; font-weight: 700;
    text-decoration: none; transition: background 0.2s; box-sizing: border-box;
  }
  .btn-pay-now:hover { background: #4ecfa0; }

  /* ── TECHNICIAN TIMELINE CLICK SYSTEM ── */
  .tl-node.tick-btn {
    cursor: pointer; transition: all 0.2s;
    border: 2px dashed var(--border); background: var(--bg-card2);
  }
  .tl-node.tick-btn:hover {
    border-color: var(--amber); border-style: solid;
    background: rgba(212,137,26,0.08);
  }
  .tl-node.tick-btn:hover .tick-empty-icon { opacity: 0.6; color: var(--amber); }
  .tick-empty-icon { color: var(--border); font-size: 0.85rem; transition: all 0.2s; }
  /* only show tick buttons in tech view */
  .tl-node.tick-btn { display: none; }
  body.view-tech .tl-node.tick-btn { display: flex; }
  /* in tech view, hide the static nodes for the 3 pre-ticked steps (tick-btn handles display) */
  body.view-tech #tl-node-confirm-static,
  body.view-tech #tl-node-diag-static,
  body.view-tech #tl-node-shipped-static { display: none; }
  .tech-only { display: none; }
  .coll-only { display: none; }
  body.view-tech .tech-only { display: block; }
  body.view-coll .coll-only { display: block; }
  /* timeline steps need grid not block */
  body.view-tech .tl-step.tech-only { display: grid; }
  body.view-coll .tl-step.coll-only { display: grid; }
  /* timeline footer needs flex not block */
  body.view-tech .timeline-footer.tech-only { display: flex; }
  body.view-coll .timeline-footer.coll-only { display: flex; }
  /* for flex containers */
  body.view-tech .tech-only-flex { display: flex !important; }
  body.view-coll .coll-only-flex { display: flex !important; }
  .tech-only-flex { display: none !important; }
  .coll-only-flex { display: none !important; }

  /* modal overlay */
  .modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.85); z-index: 500;
    align-items: center; justify-content: center; padding: 1rem;
  }
  .modal-overlay.open { display: flex; }
  @keyframes spin { to { transform: rotate(360deg); } }

  @keyframes fadeUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

  @media(max-width:1000px) { .job-wrap { grid-template-columns: 1fr; } }
  @media(max-width:600px) { .job-breadcrumb,.job-wrap { padding-left:1rem; padding-right:1rem; } }
</style>
@endsection

@section('content')

{{-- BREADCRUMB --}}
<div class="job-breadcrumb">
  <a href="/dashboard">Dashboard</a>
  <span>›</span>
  <a href="/dashboard">My Jobs</a>
  <span>›</span>
  Job #{{ $job->reference }}
</div>

<div class="job-wrap">

  {{-- ── LEFT COLUMN ── --}}
  <div>

    {{-- JOB HEADER --}}
        {{-- FLASH MESSAGES FROM PAYMENT CALLBACKS --}}
    @if(session('success'))
      <div style="background:rgba(93,224,176,0.08);border:1px solid rgba(93,224,176,0.25);border-radius:8px;padding:0.85rem 1rem;margin-bottom:1rem;font-size:0.85rem;color:#5de0b0;">
        ✓ {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div style="background:rgba(220,53,69,0.08);border:1px solid rgba(220,53,69,0.25);border-radius:8px;padding:0.85rem 1rem;margin-bottom:1rem;font-size:0.85rem;color:#f87171;">
        ⚠ {{ session('error') }}
      </div>
    @endif
    @if(session('info'))
      <div style="background:rgba(212,137,26,0.08);border:1px solid rgba(212,137,26,0.25);border-radius:8px;padding:0.85rem 1rem;margin-bottom:1rem;font-size:0.85rem;color:var(--amber-lt);">
        ℹ {{ session('info') }}
      </div>
    @endif

    <div class="job-header">
      <div class="job-header-top">
        <div>
          <div class="job-id">Job #{{ $job->reference }}</div>
          <h1 class="job-title">{{ $job->booking->device_name }}</h1>
        </div>
        @php
          $statusClass = ['confirmed'=>'status-pending','in_progress'=>'status-inprogress','completed'=>'status-done','cancelled'=>'status-cancelled'][$job->status] ?? 'status-pending';
          $statusLabel = ['confirmed'=>'Confirmed','in_progress'=>'In Progress','completed'=>'Completed','cancelled'=>'Cancelled'][$job->status] ?? ucfirst($job->status);
        @endphp
        <div class="job-status-badge {{ $statusClass }}">
          <div class="status-dot"></div>
          {{ $statusLabel }}
        </div>
      </div>
      <div class="job-meta-row">
        <div class="job-meta-item">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Collector: <strong>{{ $job->booking->collector->name }}</strong>
        </div>
        <div class="job-meta-item">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          Technician: <strong>{{ $job->booking->technicianProfile->user->name }}</strong>
        </div>
        <div class="job-meta-item">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          Booked: <strong>{{ $job->booking->requested_date->format('M j, Y') }}</strong>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{-- RESTORATION TIMELINE  (perspective-aware) --}}
    {{-- ══════════════════════════════════════════ --}}
    <div class="timeline-panel">
      <div class="timeline-header">
        <div class="timeline-title">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          Restoration Timeline
        </div>
        <div class="timeline-job-id">Job #{{ $job->reference }}</div>
      </div>

      <div class="timeline-track">

        {{-- STEP 1: BOOKING CONFIRMED — pre-ticked (both see checkmark; tech can untick) --}}
        <div class="tl-step done" id="tl-step-confirm">
          <div class="tl-node-col">
            <div class="tl-node done" id="tl-node-confirm-static">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="tl-node tick-btn done" id="tl-node-confirm" title="Click to undo" onclick="tickStep('confirm')">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="tl-line"></div>
          </div>
          <div class="tl-content">
            <div class="tl-label">Booking Confirmed <span id="tl-badge-confirm"><span class="tl-confirm-badge">✓ Confirmed</span></span></div>
            <div class="tl-desc">Technician accepted the request. Job is now active.</div>
            <div class="tl-timestamp" id="tl-ts-confirm">Mar 10, 2026 · 14:32</div>
          </div>
        </div>

        {{-- STEP 2: DIAGNOSTIC SESSION — pre-ticked (both; tech can untick) --}}
        <div class="tl-step done" id="tl-step-diag">
          <div class="tl-node-col">
            <div class="tl-node done" id="tl-node-diag-static">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="tl-node tick-btn done" id="tl-node-diag" title="Click to undo" onclick="tickStep('diag')">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="tl-line"></div>
          </div>
          <div class="tl-content">
            <div class="tl-label">Diagnostic Session Completed <span id="tl-badge-diag"><span class="tl-confirm-badge">✓ Done</span></span></div>
            <div class="tl-desc">Live Jitsi session held. Fault condition verified before shipping.</div>
            <div class="tl-timestamp" id="tl-ts-diag">Mar 12, 2026 · 10:15</div>
          </div>
        </div>

        {{-- STEP 3: DEVICE SHIPPED (collector → technician) — pre-ticked (both; tech can untick) --}}
        <div class="tl-step done" id="tl-step-shipped">
          <div class="tl-node-col">
            <div class="tl-node done" id="tl-node-shipped-static">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="tl-node tick-btn done" id="tl-node-shipped" title="Click to undo" onclick="tickStep('shipped')">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="tl-line"></div>
          </div>
          <div class="tl-content">
            <div class="tl-label">Device Shipped <span id="tl-badge-shipped"><span class="tl-confirm-badge">✓ Shipped</span></span></div>
            <div class="tl-desc">Collector shipped the device. Tracking: DHL Express #1234567890</div>
            <div class="tl-timestamp" id="tl-ts-shipped">Mar 14, 2026 · 09:00</div>
          </div>
        </div>

        {{-- STEP 4: REPAIR STARTED — active (technician only) --}}
        <div class="tl-step active tech-only">
          <div class="tl-node-col">
            <div class="tl-node active">
              <div class="tl-pulse"></div>
            </div>
            <div class="tl-line"></div>
          </div>
          <div class="tl-content">
            <div class="tl-label">
              Repair Started
              <span class="tl-active-badge">In Progress</span>
            </div>
            <div class="tl-desc">Technician has the device on the workbench and repair is underway.</div>
            <div class="tl-timestamp">Mar 18, 2026 · 11:45</div>
          </div>
        </div>

        {{-- STEP 5: COMPLETED — tickable by technician --}}
        <div class="tl-step pending" id="tl-step-complete">
          <div class="tl-node-col">
            <div class="tl-node pending" id="tl-node-complete-static">
              <div class="tl-pulse green"></div>
            </div>
            <div class="tl-node tick-btn" id="tl-node-complete" title="Click to mark Completed" onclick="tickStep('complete')">
              <span class="tick-empty-icon">✓</span>
            </div>
            <div class="tl-line"></div>
          </div>
          <div class="tl-content">
            <div class="tl-label">Completed <span id="tl-badge-complete"></span></div>
            <div class="tl-desc">Repair fully finished. Technician confirms the device is ready to ship back.</div>
            <div class="tl-timestamp tl-pending-text" id="tl-ts-complete">Awaiting…</div>
          </div>
        </div>

        {{-- STEP 6: SHIPPING — tickable by technician --}}
        <div class="tl-step pending" id="tl-step-ship">
          <div class="tl-node-col">
            <div class="tl-node pending" id="tl-node-ship-static">
              <div class="tl-pulse" style="background:var(--border);"></div>
            </div>
            <div class="tl-node tick-btn" id="tl-node-ship" title="Click to mark Shipped Back" onclick="tickStep('ship')">
              <span class="tick-empty-icon">✓</span>
            </div>
            <div class="tl-line"></div>
          </div>
          <div class="tl-content">
            <div class="tl-label">Shipping <span id="tl-badge-ship"></span></div>
            <div class="tl-desc">Technician ships the restored device back to the collector.</div>
            <div class="tl-timestamp tl-pending-text" id="tl-ts-ship">Awaiting…</div>
          </div>
        </div>

        {{-- STEP 7A: PAYMENT RECEIVED — technician view only (tickable) --}}
        <div class="tl-step pending tech-only last" id="tl-step-pay">
          <div class="tl-node-col">
            <div class="tl-node pending" id="tl-node-pay-static">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div class="tl-node tick-btn" id="tl-node-pay" title="Click to mark Payment Received" onclick="tickStep('pay')">
              <span class="tick-empty-icon">✓</span>
            </div>
          </div>
          <div class="tl-content">
            <div class="tl-label">Payment Received <span id="tl-badge-pay"></span></div>
            <div class="tl-desc">Collector completes full payment after device is shipped back.</div>
            <div class="tl-timestamp tl-pending-text" id="tl-ts-pay">Awaiting…</div>
          </div>
        </div>

        {{-- STEP 7B: MAKE PAYMENT — collector view only (triggered by 'ship' tick) --}}
        @php
          $shipDone    = $job->isStepDone('ship');
          $paymentDone = $job->payment_status === 'paid';
        @endphp
        <div class="tl-step {{ $paymentDone ? 'done' : ($shipDone ? 'active' : 'pending') }} coll-only last">
          <div class="tl-node-col">
            <div class="tl-node {{ $paymentDone ? 'done' : ($shipDone ? 'active' : 'pending') }}">
              @if($paymentDone)
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
              @else
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
              @endif
            </div>
          </div>
          <div class="tl-content">
            <div class="tl-label">
              Payment Received
              @if($paymentDone)
                <span class="tl-confirm-badge">✓ Paid</span>
              @elseif($shipDone)
                <span class="tl-pay-badge">Action Required</span>
              @endif
            </div>
            @if($paymentDone)
              <div class="tl-desc">Payment complete. Thank you!</div>
              <div class="tl-timestamp">Paid ✓</div>
            @elseif($shipDone)
              <div class="tl-desc">Your device has been shipped back. Complete payment to release funds to the technician.</div>
              <div style="margin-top:0.6rem;">
                <a href="/jobs/{{ $job->id }}/pay"
                  style="display:inline-flex;align-items:center;gap:0.5rem;background:var(--amber);color:#161310;padding:0.55rem 1.25rem;border-radius:8px;font-weight:700;font-size:0.85rem;font-family:'DM Sans',sans-serif;text-decoration:none;transition:background 0.2s;"
                  onmouseover="this.style.background='var(--amber-lt)'" onmouseout="this.style.background='var(--amber)'">
                  💳 Pay Now — ৳{{ number_format($job->payment_amount, 0) }}
                </a>
              </div>
            @else
              <div class="tl-desc">Pay in full once the technician ships your restored device back to you.</div>
              <div class="tl-timestamp tl-pending-text">Awaiting shipment from technician…</div>
            @endif
          </div>
        </div>

      </div>{{-- end timeline-track --}}

      {{-- ── TECH FOOTER removed (progress bar and est. completion removed) ── --}}

      {{-- ── COLLECTOR FOOTER removed (progress bar and est. completion removed) ── --}}
    </div>

    {{-- ══════════════════════════════════ --}}
    {{-- MESSAGING  (perspective-aware)    --}}
    {{-- ══════════════════════════════════ --}}
    <div class="msg-panel">
      <div class="msg-header">
        <div class="msg-header-title">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          Job Messages
          <span class="msg-count" id="msg-count">{{ $job->messages->count() }}</span>
        </div>
        <span style="font-size:0.75rem;color:var(--muted);">Visible to both parties only</span>
      </div>

      {{-- ── TECHNICIAN THREAD (Jin = own) ── --}}
            {{-- REAL MESSAGE THREAD — shared by both parties --}}
      <div class="msg-thread" id="msg-thread">

        @forelse($job->messages as $msg)
          @php $isOwn = $msg->sender_id === auth()->id(); @endphp
          <div class="msg-bubble {{ $isOwn ? 'own' : '' }}" data-msg-id="{{ $msg->id }}">
            <div class="msg-avatar">{{ $msg->sender->initial() }}</div>
            <div class="msg-content">
              <div class="msg-name-time">
                @if($isOwn)
                  <span>{{ $msg->timeLabel() }}</span>
                  <span style="font-weight:600;">You</span>
                @else
                  <span style="font-weight:600;">{{ $msg->sender->name }}</span>
                  <span>{{ $msg->timeLabel() }}</span>
                @endif
              </div>
              <div class="msg-text">{{ $msg->body }}</div>
            </div>
          </div>
        @empty
          <div id="msg-empty" style="text-align:center;padding:2.5rem 1rem;color:var(--muted);font-size:0.88rem;line-height:1.6;">
            No messages yet.<br>Start the conversation below.
          </div>
        @endforelse

      </div>

      <div class="msg-input-row">
        <textarea class="msg-textarea" id="msg-input" placeholder="Write a message…" rows="1"
          onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendMessage()}"
          oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"></textarea>
        <button class="btn-send" onclick="sendMessage()">
          Send
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
      </div>
    </div>

  </div>{{-- end left column --}}

  {{-- ── RIGHT SIDEBAR ── --}}
  <aside class="job-sidebar">

    {{-- JOB DETAILS (both) --}}
    <div class="side-card">
      <div class="side-card-header">📋 Job Details</div>
      <div class="side-card-body" style="padding:0 1.25rem;">
        <div class="detail-row"><span class="label">Device</span><span class="value">{{ $job->booking->device_name }}</span></div>
        <div class="detail-row"><span class="label">Category</span><span class="value">{{ $job->booking->serviceListing->category ?? '—' }}</span></div>
        <div class="detail-row"><span class="label">Service</span><span class="value">{{ $job->booking->serviceListing->title ?? '—' }}</span></div>
        <div class="detail-row"><span class="label">Technician</span><span class="value">{{ $job->booking->technicianProfile->user->name }} · {{ $job->booking->technicianProfile->location }}</span></div>
        <div class="detail-row"><span class="label">Booked</span><span class="value">{{ $job->booking->requested_date->format('M j, Y') }}</span></div>
        <div class="detail-row"><span class="label">Device Received</span><span class="value">Mar 20, 2026</span></div>
      </div>
    </div>

    {{-- ═══════════════════════════════════ --}}
    {{-- REVIEW PROMPT — COLLECTOR ONLY      --}}
    {{-- Appears when job is marked Shipped  --}}
    {{-- ═══════════════════════════════════ --}}
    <div class="side-card coll-only" id="review-prompt-card" style="display:none;border-color:rgba(93,224,176,0.3);background:rgba(93,224,176,0.03);">
      <div class="side-card-header" style="color:#5de0b0;">⭐ Leave a Review</div>
      <div class="side-card-body">
        <p style="font-size:0.82rem;color:var(--muted);line-height:1.6;margin-bottom:1rem;">
          Your device has been shipped back! Once you receive it, please leave a review for {{ $job->booking->technicianProfile->user->name }}. — it helps other collectors find great technicians.
        </p>
        <a href="/jobs/{{ $job->id }}/review" class="btn-pay-now" style="background:#5de0b0;display:flex;align-items:center;justify-content:center;gap:0.5rem;text-decoration:none;">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          Write a Review for {{ $job->booking->technicianProfile->user->name }}
        </a>
        <p style="font-size:0.7rem;color:var(--muted);margin-top:0.6rem;text-align:center;">Rating contributes to {{ $job->booking->technicianProfile->user->name }}'s platform score</p>
      </div>
    </div>

    {{-- ══════════════════════════════ --}}
    {{-- ACTIONS — TECHNICIAN          --}}
    {{-- ══════════════════════════════ --}}
    <div class="side-card tech-only">
      <div class="side-card-header">⚡ Actions</div>
      <div class="side-card-body">
        <div class="action-btns">
          @if($job->status === 'confirmed')
          <button class="btn-action-primary" onclick="startRepair()" style="width:100%;background:var(--amber);border:none;color:#161310;padding:0.75rem;border-radius:8px;font-weight:700;font-size:0.92rem;cursor:pointer;font-family:'DM Sans',sans-serif;margin-bottom:0.5rem;">
            🔧 Start Repair
          </button>
          @elseif($job->status === 'in_progress')
          <button class="btn-action-primary" onclick="markComplete()" style="width:100%;background:rgba(74,200,120,0.1);border:1px solid rgba(74,200,120,0.3);color:#5de0b0;padding:0.75rem;border-radius:8px;font-weight:700;font-size:0.92rem;cursor:pointer;font-family:'DM Sans',sans-serif;margin-bottom:0.5rem;">
            ✓ Mark as Complete
          </button>
          @endif
          @if($job->status !== 'completed')
          <button class="btn-action-danger" onclick="cancelJob()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:-2px;margin-right:4px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            Cancel Job
          </button>
          @endif
        </div>
              </div>
    </div>

    {{-- ══════════════════════════════ --}}
    {{-- ACTIONS — COLLECTOR           --}}
    {{-- ══════════════════════════════ --}}
    <div class="side-card coll-only">
      <div class="side-card-header">⚡ Actions</div>
      <div class="side-card-body">
        <div class="action-btns">
          @if($job->isStepDone('ship') && $job->payment_status !== 'paid')
          <a href="/jobs/{{ $job->id }}/pay"
            style="width:100%;background:var(--amber);border:none;color:#161310;padding:0.75rem;border-radius:8px;font-weight:700;font-size:0.92rem;cursor:pointer;font-family:'DM Sans',sans-serif;margin-bottom:0.5rem;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:0.5rem;transition:background 0.2s;"
            onmouseover="this.style.background='var(--amber-lt)'" onmouseout="this.style.background='var(--amber)'">
            💳 Pay Now — ৳{{ number_format($job->payment_amount, 0) }}
          </a>
          @elseif($job->payment_status === 'paid')
          <div style="width:100%;background:rgba(93,224,176,0.08);border:1px solid rgba(93,224,176,0.25);color:#5de0b0;padding:0.75rem;border-radius:8px;font-weight:700;font-size:0.88rem;text-align:center;">
            ✓ Payment Complete
          </div>
          @endif
          <button class="btn-action-danger" onclick="cancelOrder()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:-2px;margin-right:4px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            Cancel Order
          </button>
        </div>
        <div class="sms-note">
          <span>⚠️</span>
          <span>Cancelling after the device has been shipped may affect your refund eligibility. Contact {{ $job->booking->technicianProfile->user->name }} before cancelling.</span>
        </div>
      </div>
    </div>

    {{-- VINTAGE FACT WIDGET --}}
    @include('components.vintage-fact')

  </aside>
</div>

{{-- ── CANCEL JOB MODAL ── --}}
<div class="modal-overlay" id="cancel-job-modal">
  <div style="background:var(--bg-card);border:1px solid rgba(200,60,60,0.35);border-radius:16px;padding:2rem;max-width:420px;width:100%;animation:fadeUp 0.25s ease;">
    <div style="font-size:2rem;margin-bottom:0.75rem;">⚠️</div>
    <h2 style="font-family:'Playfair Display',serif;font-size:1.3rem;margin-bottom:0.5rem;color:#f09090;">Cancel This Job?</h2>
    <p style="font-size:0.85rem;color:var(--muted);line-height:1.6;margin-bottom:1rem;">You are about to cancel Job <strong style="color:var(--cream);">#{{ $job->reference }}</strong> for <strong style="color:var(--cream);">{{ $job->booking->device_name }}</strong>.</p>
    <div style="display:flex;gap:0.75rem;">
      <button onclick="document.getElementById('cancel-job-modal').classList.remove('open')"
        style="flex:1;background:transparent;border:1px solid var(--border);color:var(--muted);padding:0.75rem;border-radius:8px;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.88rem;">
        Keep Job
      </button>
      <button onclick="doCancel()"
        style="flex:2;background:rgba(200,60,60,0.12);border:1px solid rgba(200,60,60,0.35);color:#f09090;padding:0.75rem;border-radius:8px;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.88rem;font-weight:700;">
        Yes, Cancel Job
      </button>
    </div>
  </div>
</div>

<script>
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const jobId  = {{ $job->id }};
  const isTech = {{ $isTech ? 'true' : 'false' }};
  let currentView = isTech ? 'tech' : 'coll';

  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('open'); });
  });

  function applyView(v) {
    document.body.className = 'view-' + v;
    const thread = document.getElementById('msg-thread');
    if (thread) {
      thread.style.display = 'flex';
      thread.scrollTop = thread.scrollHeight;
    }
    const inp = document.getElementById('msg-input');
    if (inp) {
      inp.placeholder = v === 'tech'
        ? 'Reply to {{ $job->booking->collector->name }}...'
        : 'Reply to {{ $job->booking->technicianProfile->user->name }}...';
    }
  }

  let msgCount = {{ $job->messages->count() }};
  let lastMsgId = {{ $job->messages->last()?->id ?? 0 }};
  const JOB_ID  = {{ $job->id }};
  const MY_ID   = {{ auth()->id() }};
  const CSRF    = document.querySelector('meta[name="csrf-token"]')?.content || '';

  function buildBubble(msg) {
    const isOwn = msg.sender_id === MY_ID;
    const div = document.createElement('div');
    div.className = 'msg-bubble' + (isOwn ? ' own' : '');
    div.setAttribute('data-msg-id', msg.id);
    const timeHtml = isOwn
      ? `<span>${msg.time}</span><span style="font-weight:600;">You</span>`
      : `<span style="font-weight:600;">${msg.sender_name}</span><span>${msg.time}</span>`;
    div.innerHTML = `
      <div class="msg-avatar">${msg.sender_initial}</div>
      <div class="msg-content">
        <div class="msg-name-time">${timeHtml}</div>
        <div class="msg-text">${msg.body.replace(/</g,'&lt;').replace(/>/g,'&gt;')}</div>
      </div>`;
    return div;
  }

  function appendBubble(msg) {
    const thread = document.getElementById('msg-thread');
    const empty  = document.getElementById('msg-empty');
    if (empty) empty.remove();
    thread.appendChild(buildBubble(msg));
    thread.scrollTop = thread.scrollHeight;
    msgCount++;
    const badge = document.getElementById('msg-count');
    if (badge) badge.textContent = msgCount;
    if (msg.id > lastMsgId) lastMsgId = msg.id;
  }

  function sendMessage() {
    const input = document.getElementById('msg-input');
    const text  = input.value.trim();
    if (!text) return;

    const btn = document.querySelector('.btn-send');
    btn.disabled = true;

    fetch(`/jobs/${JOB_ID}/messages`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
      },
      body: JSON.stringify({ body: text }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        appendBubble(data.message);
        input.value = '';
        input.style.height = 'auto';
      }
    })
    .catch(() => alert('Could not send message. Please try again.'))
    .finally(() => { btn.disabled = false; });
  }

  // Poll for new messages every 5 seconds
  function pollMessages() {
    fetch(`/jobs/${JOB_ID}/messages?after=${lastMsgId}`, {
      headers: { 'X-CSRF-TOKEN': CSRF },
    })
    .then(r => r.json())
    .then(data => {
      (data.messages || []).forEach(appendBubble);
    })
    .catch(() => {}); // silent fail — polling is best-effort
  }

  setInterval(pollMessages, 5000);

  // Load timeline state from DB
  @php $tl = $job->timeline_state ?? []; @endphp
  const tickState = {
    confirm:  {{ !empty($tl['confirm'])  ? 'true' : 'false' }},
    diag:     {{ !empty($tl['diag'])     ? 'true' : 'false' }},
    shipped:  {{ !empty($tl['shipped'])  ? 'true' : 'false' }},
    pay:      {{ !empty($tl['pay'])      ? 'true' : 'false' }},
    complete: {{ !empty($tl['complete']) ? 'true' : 'false' }},
    ship:     {{ !empty($tl['ship'])     ? 'true' : 'false' }},
  };

  const tickConfig = {
    confirm:  {step:'tl-step-confirm', badge:'tl-badge-confirm', ts:'tl-ts-confirm', label:'\u2713 Confirmed',  cls:'tl-confirm-badge',   defaultTs:'{{ $job->created_at->format("M j, Y") }}'},
    diag:     {step:'tl-step-diag',    badge:'tl-badge-diag',    ts:'tl-ts-diag',    label:'\u2713 Done',       cls:'tl-confirm-badge',   defaultTs:null},
    shipped:  {step:'tl-step-shipped', badge:'tl-badge-shipped', ts:'tl-ts-shipped', label:'\u2713 Shipped',    cls:'tl-confirm-badge',   defaultTs:null},
    pay:      {step:'tl-step-pay',     badge:'tl-badge-pay',     ts:'tl-ts-pay',     label:'Payment Received',  cls:'tl-pay-badge',       defaultTs:null},
    complete: {step:'tl-step-complete',badge:'tl-badge-complete',ts:'tl-ts-complete',label:'\u2713 Completed',  cls:'tl-completed-badge', defaultTs:null},
    ship:     {step:'tl-step-ship',    badge:'tl-badge-ship',    ts:'tl-ts-ship',    label:'Shipped Back',      cls:'tl-ship-badge',      defaultTs:null},
  };

  function tickStep(key) {
    if (!isTech) return;
    const cfg = tickConfig[key];
    const newVal = !tickState[key];
    tickState[key] = newVal;
    renderTick(key, newVal, cfg);
    updateTechProgress();
    fetch('/jobs/'+jobId+'/tick', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
      body:JSON.stringify({step:key}),
    }).catch(() => { tickState[key]=!newVal; renderTick(key,!newVal,cfg); updateTechProgress(); });
  }

  function renderTick(key, done, cfg) {
    const stepEl  = document.getElementById(cfg.step);
    const tickBtn = document.getElementById('tl-node-'+key);
    const staticEl= document.getElementById('tl-node-'+key+'-static');
    const badgeEl = document.getElementById(cfg.badge);
    const tsEl    = document.getElementById(cfg.ts);
    if (!stepEl) return;
    if (done) {
      stepEl.classList.remove('pending'); stepEl.classList.add('done');
      if (tickBtn){tickBtn.classList.remove('tick-btn');tickBtn.classList.add('done');tickBtn.innerHTML='<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>';tickBtn.title='Click to undo';}
      if (staticEl) staticEl.style.display='none';
      if (badgeEl)  badgeEl.innerHTML='<span class="'+cfg.cls+'">'+cfg.label+'</span>';
      if (tsEl) { const now=new Date(); tsEl.textContent=cfg.defaultTs||(now.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'})+' \u00b7 '+now.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})); tsEl.classList.remove('tl-pending-text'); }
      if (key==='ship'){const rp=document.getElementById('review-prompt-card');if(rp){rp.style.display='';rp.style.animation='fadeUp 0.4s ease';}}
    } else {
      stepEl.classList.add('pending'); stepEl.classList.remove('done');
      if (tickBtn){tickBtn.classList.add('tick-btn');tickBtn.classList.remove('done');tickBtn.innerHTML='<span class="tick-empty-icon">\u2713</span>';tickBtn.title='Click to mark progress';}
      if (staticEl) staticEl.style.display='none';
      if (badgeEl)  badgeEl.innerHTML='';
      if (tsEl)     {tsEl.textContent='Awaiting...';tsEl.classList.add('tl-pending-text');}
      if (key==='ship'){const rp=document.getElementById('review-prompt-card');if(rp) rp.style.display='none';}
    }
  }

  function updateTechProgress() {
    // progress bar removed
  }

  function startRepair() {
    if (!confirm('Start the repair? This marks the job as In Progress.')) return;
    fetch('/jobs/'+jobId+'/start',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken}})
    .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); else alert(d.message||'Error'); });
  }

  function markComplete() {
    if (!confirm('Mark this job as Completed?')) return;
    fetch('/jobs/'+jobId+'/complete',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken}})
    .then(r=>r.json()).then(d=>{ if(d.success) window.location.href='/technician-dashboard'; else alert(d.message||'Error'); });
  }

  function cancelJob()  { document.getElementById('cancel-job-modal').classList.add('open'); }

  function doCancel() {
    document.getElementById('cancel-job-modal').classList.remove('open');
    fetch('/jobs/'+jobId+'/cancel',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken}})
    .then(r=>r.json()).then(d=>{
      if (d.success) { location.reload(); }
    });
  }

  function cancelOrder() {
    if (!confirm('Cancel your order? Contact the technician first if the device has already been shipped.')) return;
    fetch('/jobs/'+jobId+'/cancel',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken}})
    .then(r=>r.json()).then(d=>{ if(d.success) location.reload(); });
  }


  // Init: apply DB tick states visually
  Object.keys(tickState).forEach(key => { if (tickState[key]) renderTick(key, true, tickConfig[key]); });
  updateTechProgress();
  applyView(currentView);



</script>
@endsection