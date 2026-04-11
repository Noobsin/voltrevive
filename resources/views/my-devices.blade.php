@extends('layout')
@section('title', 'My Devices')

@section('styles')
<style>
  .page-wrap { max-width:1100px; margin:0 auto; padding:2.5rem 2rem 5rem; position:relative; z-index:1; }
  .page-hd { margin-bottom:2rem; }
  .page-eyebrow { font-size:0.72rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--amber); margin-bottom:0.5rem; }
  .page-title { font-family:'Playfair Display',serif; font-size:2rem; font-weight:900; margin-bottom:0.3rem; }
  .page-sub { color:var(--muted); font-size:0.9rem; }

  /* ── TAB BAR ── */
  .tab-bar { display:flex; gap:0; background:var(--bg-card); border:1px solid var(--border); border-radius:10px; overflow:hidden; margin-bottom:2rem; width:fit-content; }
  .tab-btn { padding:0.6rem 1.4rem; font-size:0.85rem; font-weight:600; cursor:pointer; border:none; background:transparent; color:var(--muted); font-family:'DM Sans',sans-serif; transition:all 0.15s; }
  .tab-btn.active { background:var(--amber); color:#161310; }

  /* ── DEVICE CARDS ── */
  .devices-list { display:flex; flex-direction:column; gap:1rem; }
  .dev-row {
    background:var(--bg-card); border:1px solid var(--border); border-radius:14px;
    padding:1.25rem 1.5rem; display:grid;
    grid-template-columns:56px 1fr auto auto auto;
    align-items:center; gap:1.25rem;
    transition:border-color 0.2s; animation:fadeUp 0.3s ease both;
  }
  .dev-row:hover { border-color:var(--amber); }
  .dev-icon { width:52px; height:52px; border-radius:10px; background:var(--bg-card2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:1.6rem; flex-shrink:0; }
  .dev-info-main { min-width:0; }
  .dev-name { font-family:'Playfair Display',serif; font-size:1.05rem; font-weight:700; margin-bottom:0.2rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .dev-sub { font-size:0.78rem; color:var(--muted); display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap; }
  .dev-tech { display:flex; align-items:center; gap:0.35rem; }
  .dev-tech-dot { width:18px; height:18px; border-radius:50%; background:var(--bg-card2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:0.6rem; font-weight:700; color:var(--amber); }

  /* progress column */
  .dev-progress { width:140px; flex-shrink:0; }
  .prog-bar { height:5px; background:var(--border); border-radius:3px; overflow:hidden; margin-bottom:0.3rem; }
  .prog-fill { height:100%; background:var(--amber); border-radius:3px; }
  .prog-fill.green { background:#5de0b0; }
  .prog-label { font-size:0.68rem; color:var(--muted); }

  /* status pill */
  .status-pill { display:inline-flex; align-items:center; gap:0.35rem; padding:0.25rem 0.8rem; border-radius:20px; font-size:0.72rem; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; white-space:nowrap; }
  .pill-completed { background:rgba(93,224,176,0.1); border:1px solid rgba(93,224,176,0.25); color:#5de0b0; }
  .pill-inprogress { background:rgba(212,137,26,0.1); border:1px solid rgba(212,137,26,0.25); color:var(--amber-lt); }
  .pill-pending { background:rgba(130,170,255,0.08); border:1px solid rgba(130,170,255,0.2); color:#8ab4ff; }
  .pill-dot { width:6px; height:6px; border-radius:50%; background:currentColor; flex-shrink:0; }
  .pill-dot.anim { animation:pulse 2s infinite; }

  /* action btn */
  .btn-track { background:transparent; border:1px solid var(--border); color:var(--cream); padding:0.45rem 1.1rem; border-radius:8px; font-size:0.82rem; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
  .btn-track:hover { border-color:var(--amber); color:var(--amber-lt); }
  .btn-track.active-btn { background:var(--amber); border-color:var(--amber); color:#161310; }
  .btn-track.active-btn:hover { background:var(--amber-lt); }

  /* empty state */
  .empty-state { text-align:center; padding:4rem 2rem; color:var(--muted); }
  .empty-state-icon { font-size:3rem; margin-bottom:1rem; opacity:0.4; }

  @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
  @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:700px) {
    .dev-row { grid-template-columns:44px 1fr; grid-template-rows:auto auto; }
    .dev-progress,.status-pill,.btn-track { display:none; }
  }
</style>
@endsection

@section('content')
<div class="page-wrap">

  <div class="page-hd">
    <div class="page-eyebrow">My Devices</div>
    <h1 class="page-title">Devices <em>Given for Repair</em></h1>
    <p class="page-sub">Track every device you've sent for restoration. Click any row to view the full job detail and chat with your technician.</p>
  </div>

  {{-- TABS --}}
  <div class="tab-bar">
    <button class="tab-btn active" onclick="filterTab('all', this)">All ({{ $total }})</button>
    <button class="tab-btn" onclick="filterTab('active', this)">Under Repair ({{ $active }})</button>
    <button class="tab-btn" onclick="filterTab('completed', this)">Completed ({{ $completed }})</button>
  </div>

  {{-- DEVICE LIST --}}
  <div class="devices-list" id="devices-list">

    @forelse($bookings as $booking)
    @php
      $job = $booking->job;
      $jobStatus = $job ? $job->status : $booking->status;
      $tabStatus = in_array($jobStatus, ['confirmed','in_progress']) ? 'active' : ($jobStatus === 'completed' ? 'completed' : 'other');
      $techName  = $booking->technicianProfile->user->name ?? 'Unknown';
      $techInit  = strtoupper(substr($techName, 0, 1));
      $techLoc   = $booking->technicianProfile->location ?? '';
      $category  = $booking->serviceListing->category ?? '—';
      $doneTicks = $job ? collect($job->timeline_state ?? [])->filter()->count() : 0;
      $pct       = $doneTicks > 0 ? round(($doneTicks / 7) * 100) : 0;
      $pillClass = match($jobStatus) {
        'in_progress' => 'pill-inprogress',
        'confirmed'   => 'pill-pending',
        'completed'   => 'pill-completed',
        'cancelled'   => 'pill-cancelled',
        default       => 'pill-pending',
      };
      $pillLabel = match($jobStatus) {
        'in_progress' => 'In Progress',
        'confirmed'   => 'Confirmed',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled',
        'rejected'    => 'Rejected',
        default       => ucfirst($booking->status),
      };
      $animate = $jobStatus === 'in_progress' ? ' anim' : '';
      $jobLink = $job ? '/jobs/'.$job->id : '#';
    @endphp
    <div class="dev-row" data-status="{{ $tabStatus }}" data-link="{{ $jobLink }}" @if($job) onclick="if(this.dataset.link!='#')window.location=this.dataset.link" style="cursor:pointer;" @endif>
      <div class="dev-icon">🔧</div>
      <div class="dev-info-main">
        <div class="dev-name">{{ $booking->device_name }}</div>
        <div class="dev-sub">
          <span>{{ $category }}</span>
          <span class="dev-tech"><div class="dev-tech-dot">{{ $techInit }}</div> {{ $techName }}{{ $techLoc ? ' · '.$techLoc : '' }}</span>
          <span>Booked {{ $booking->requested_date->format('M j, Y') }}</span>
        </div>
        @if($booking->status === 'rejected' && $booking->rejection_reason)
        <div style="font-size:0.78rem;color:#f09090;margin-top:0.3rem;padding:0.4rem 0.75rem;background:rgba(200,60,60,0.07);border-radius:6px;border:1px solid rgba(200,60,60,0.18);">
          ✕ Declined: {{ $booking->rejection_reason }}
        </div>
        @endif
      </div>
      @if($job)
      <div class="dev-progress">
        <div class="prog-bar"><div class="prog-fill" style="width:{{ $pct }}%"></div></div>
        <div class="prog-label">{{ $doneTicks }} of 7 stages complete</div>
      </div>
      @else
      <div class="dev-progress"><div class="prog-label" style="color:var(--muted);">Awaiting technician response</div></div>
      @endif
      <span class="status-pill {{ $pillClass }}"><span class="pill-dot{{ $animate }}"></span>{{ $pillLabel }}</span>
      @if($job)
      <a href="{{ $jobLink }}" class="btn-track active-btn" onclick="event.stopPropagation()">Track Job &rarr;</a>
      @else
      <span class="btn-track" style="opacity:0.4;cursor:default;">Pending</span>
      @endif
    </div>
    @empty
    <div style="padding:3rem;text-align:center;color:var(--muted);">No devices yet. <a href="/browse" style="color:var(--amber-lt);">Browse technicians</a> to get started.</div>
    @endforelse

  </div>{{-- end devices-list --}}

</div>
<script>
  (function() {
    const role = localStorage.getItem('vr_role');
    if (role !== 'collector') window.location.href = '/';
  })();

  function filterTab(filter, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.dev-row').forEach(row => {
      const show = filter === 'all' || row.dataset.status === filter;
      row.style.display = show ? '' : 'none';
    });
  }
</script>
@endsection