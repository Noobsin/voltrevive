@extends('layout')
@section('title', 'Technician Dashboard')

@section('styles')
<style>
  .dash-wrap { max-width:1200px; margin:0 auto; padding:2.5rem 2rem 5rem; position:relative; z-index:1; }
  .dash-header { margin-bottom:2rem; display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem; }
  .dash-hd-left {}
  .dash-eyebrow { font-size:0.72rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:#5de0b0; margin-bottom:0.5rem; }
  .dash-title { font-family:'Playfair Display',serif; font-size:2rem; font-weight:900; margin-bottom:0.3rem; }
  .dash-title em { font-style:italic; color:var(--amber-lt); }
  .dash-sub { color:var(--muted); font-size:0.9rem; }
  .btn-list-svc-dash { background:var(--amber); color:#161310; padding:0.55rem 1.3rem; border-radius:8px; font-weight:700; font-size:0.88rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem; transition:background 0.15s; }
  .btn-list-svc-dash:hover { background:var(--amber-lt); }

  /* ── REVENUE STATS ── */
  .rev-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:2.5rem; }
  .rev-card { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:1.25rem 1.5rem; }
  .rev-card.highlight { border-color:rgba(93,224,176,0.3); background:rgba(93,224,176,0.04); }
  .rev-icon { font-size:1.3rem; margin-bottom:0.6rem; }
  .rev-num { font-family:'Playfair Display',serif; font-size:1.9rem; font-weight:900; color:var(--amber-lt); line-height:1; margin-bottom:0.3rem; }
  .rev-card.highlight .rev-num { color:#5de0b0; }
  .rev-lbl { font-size:0.78rem; color:var(--muted); }
  .rev-change { font-size:0.72rem; margin-top:0.4rem; }
  .rev-change.up { color:#5de0b0; }

  /* ── SECTION HEADER ── */
  .section-hd { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; }
  .section-hd h2 { font-family:'Playfair Display',serif; font-size:1.25rem; }
  .section-hd h2 em { font-style:italic; color:var(--amber-lt); }
  .section-tag { font-size:0.72rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted); background:var(--bg-card2); border:1px solid var(--border); padding:0.25rem 0.7rem; border-radius:20px; }

  /* ── CURRENT JOBS ── */
  .jobs-list { display:flex; flex-direction:column; gap:0.85rem; margin-bottom:2.5rem; }
  .job-row { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:1rem 1.25rem; display:grid; grid-template-columns:44px 1fr auto auto; align-items:center; gap:1rem; transition:border-color 0.2s; cursor:pointer; }
  .job-row:hover { border-color:var(--amber); }
  .job-icon { width:40px; height:40px; border-radius:8px; background:var(--bg-card2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
  .job-info-name { font-weight:700; font-size:0.9rem; margin-bottom:0.15rem; }
  .job-info-sub { font-size:0.75rem; color:var(--muted); display:flex; gap:0.75rem; flex-wrap:wrap; }
  .job-amount { font-weight:700; color:var(--amber-lt); font-size:0.95rem; }
  .status-pill { display:inline-flex; align-items:center; gap:0.35rem; padding:0.2rem 0.7rem; border-radius:20px; font-size:0.72rem; font-weight:700; text-transform:uppercase; white-space:nowrap; }
  .pill-inprogress { background:rgba(212,137,26,0.1); border:1px solid rgba(212,137,26,0.25); color:var(--amber-lt); }
  .pill-pending { background:rgba(130,170,255,0.08); border:1px solid rgba(130,170,255,0.2); color:#8ab4ff; }
  .pill-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }
  .pill-dot.anim { animation:pulse 2s infinite; }

  /* ── HISTORY TABLE ── */
  .history-panel { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
  .history-table { width:100%; border-collapse:collapse; }
  .history-table thead tr { background:var(--bg-card2); border-bottom:1px solid var(--border); }
  .history-table th { padding:0.85rem 1.25rem; text-align:left; font-size:0.72rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted); }
  .history-table td { padding:0.95rem 1.25rem; font-size:0.88rem; border-bottom:1px solid var(--border); vertical-align:middle; }
  .history-table tr:last-child td { border-bottom:none; }
  .history-table tr:hover td { background:var(--bg-card2); }
  .collector-cell { display:flex; align-items:center; gap:0.5rem; }
  .coll-dot { width:26px; height:26px; border-radius:50%; background:var(--bg-card2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; color:var(--amber); }
  .device-name-td { font-weight:600; }
  .device-cat-td { font-size:0.72rem; color:var(--muted); }
  .earned-td { font-weight:700; color:#5de0b0; }
  .pill-completed { background:rgba(93,224,176,0.1); border:1px solid rgba(93,224,176,0.25); color:#5de0b0; }
  .rating-td { color:var(--amber); font-size:0.88rem; }
  .btn-view-sm { background:transparent; border:1px solid var(--border); color:var(--cream); padding:0.3rem 0.75rem; border-radius:6px; font-size:0.75rem; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all 0.2s; text-decoration:none; display:inline-block; }
  .btn-view-sm:hover { border-color:var(--amber); color:var(--amber-lt); }

  @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
  @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:900px) { .rev-grid { grid-template-columns:repeat(2,1fr); } }
  @media(max-width:600px) { .dash-wrap { padding:1.5rem 1rem 4rem; } .job-row { grid-template-columns:40px 1fr; } .job-amount,.status-pill { display:none; } }
</style>
@endsection

@section('content')
<div class="dash-wrap">

  {{-- HEADER --}}
  <div class="dash-header">
    <div class="dash-hd-left">
      <div class="dash-eyebrow">Technician Dashboard</div>
      <h1 class="dash-title">Welcome back, <em>{{ explode(' ', auth()->user()->name)[0] }}</em></h1>
      <p class="dash-sub">Your earnings, job history, and active repairs at a glance.</p>
    </div>
    <a href="/services/create" class="btn-list-svc-dash">+ List New Service</a>
  </div>


  {{-- REVENUE STATS --}}
  <div class="rev-grid">
    <div class="rev-card highlight">
      <div class="rev-icon">💰</div>
      <div class="rev-num">৳{{ number_format($stats['total_revenue'], 0) }}</div>
      <div class="rev-lbl">Total Revenue (All Time)</div>
      <div class="rev-change up">↑ All completed jobs</div>
    </div>
    <div class="rev-card highlight">
      <div class="rev-icon">📅</div>
      <div class="rev-num">৳{{ number_format($stats['month_revenue'], 0) }}</div>
      <div class="rev-lbl">Revenue This Month</div>
      <div class="rev-change up">↑ This month</div>
    </div>
    <div class="rev-card highlight">
      <div class="rev-icon">📆</div>
      <div class="rev-num">৳{{ number_format($stats['year_revenue'], 0) }}</div>
      <div class="rev-lbl">Revenue This Year ({{ now()->year }})</div>
      <div class="rev-change up">↑ This year</div>
    </div>
    <div class="rev-card">
      <div class="rev-icon">✅</div>
      <div class="rev-num">{{ $stats['completed_jobs'] }}</div>
      <div class="rev-lbl">Total Completed Jobs</div>
      <div class="rev-change up">★ {{ number_format($stats['avg_rating'], 1) }} avg rating</div>
    </div>
  </div>

  {{-- CURRENT ACTIVE JOBS --}}
  <div class="section-hd">
    <h2>Current <em>Active Jobs</em></h2>
    <span class="section-tag">{{ $activeJobs->count() }} active</span>
  </div>
  <div class="jobs-list">
    @forelse($activeJobs as $job)
    <div class="job-row" onclick="window.location='/jobs/{{ $job->id }}'">
      <div class="job-icon">🔧</div>
      <div>
        <div class="job-info-name">{{ $job->booking->device_name }}</div>
        <div class="job-info-sub">
          <span>Collector: {{ $job->booking->collector->name }}</span>
          <span>Booked {{ $job->booking->requested_date->format('M j, Y') }}</span>
          <span>{{ $job->booking->serviceListing->category ?? '' }}</span>
        </div>
      </div>
      <span class="job-amount">৳{{ number_format($job->payment_amount, 0) }}</span>
      <span class="status-pill {{ $job->status === 'in_progress' ? 'pill-inprogress' : 'pill-pending' }}">
        <span class="pill-dot {{ $job->status === 'in_progress' ? 'anim' : '' }}"></span>
        {{ $job->status === 'in_progress' ? 'In Progress' : 'Confirmed' }}
      </span>
    </div>
    @empty
    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:0.88rem;">No active jobs right now.</div>
    @endforelse
  </div>

  {{-- PENDING BOOKING REQUESTS --}}
  @if($pendingBookings->count() > 0)
  <div class="section-hd" style="margin-top:2rem;">
    <h2>Pending <em>Requests</em></h2>
    <span class="section-tag" style="background:rgba(212,137,26,0.12);color:var(--amber-lt);">{{ $pendingBookings->count() }} awaiting</span>
  </div>
  <div class="jobs-list">
    @foreach($pendingBookings as $booking)
    <div class="job-row" id="booking-row-{{ $booking->id }}">
      <div class="job-icon">📋</div>
      <div style="flex:1;">
        <div class="job-info-name">{{ $booking->device_name }}</div>
        <div class="job-info-sub">
          <span>Collector: {{ $booking->collector->name }}</span>
          <span>Requested: {{ $booking->requested_date->format('M j, Y') }}</span>
          <span>{{ $booking->serviceListing->title ?? '' }}</span>
        </div>
        @if($booking->device_description)
        <div style="font-size:0.78rem;color:var(--muted);margin-top:0.3rem;">{{ Str::limit($booking->device_description, 100) }}</div>
        @endif
      </div>
      <div style="display:flex;gap:0.5rem;flex-shrink:0;" id="booking-actions-{{ $booking->id }}">
        <button onclick="acceptBooking({{ $booking->id }})"
          style="background:rgba(74,200,120,0.1);border:1px solid rgba(74,200,120,0.3);color:#5de0b0;padding:0.45rem 1rem;border-radius:8px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">
          ✓ Accept
        </button>
        <button onclick="rejectBooking({{ $booking->id }})"
          style="background:rgba(200,60,60,0.08);border:1px solid rgba(200,60,60,0.25);color:#f09090;padding:0.45rem 1rem;border-radius:8px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">
          ✕ Decline
        </button>
      </div>
    </div>
    @endforeach
  </div>
  @endif


  {{-- MY SERVICE LISTINGS --}}
  <div class="section-hd" style="margin-top:2rem;">
    <h2>My Service <em>Listings</em></h2>
    <a href="/services/create" style="font-size:0.82rem;color:var(--amber-lt);text-decoration:none;font-weight:600;">+ Add New</a>
  </div>
  @if($myListings->isEmpty())
  <div style="padding:1.5rem;background:var(--bg-card2);border:1px solid var(--border);border-radius:12px;color:var(--muted);margin-bottom:1.5rem;">
    You haven't listed any services yet. <a href="/services/create" style="color:var(--amber-lt);">Create your first listing →</a>
  </div>
  @else
  <div style="display:flex;flex-direction:column;gap:0.75rem;margin-bottom:2rem;">
    @foreach($myListings as $listing)
    @php
      $statusStyle = match($listing->status) {
        'approved'  => 'background:rgba(74,200,120,0.1);border:1px solid rgba(74,200,120,0.3);color:#5de0b0;',
        'rejected'  => 'background:rgba(200,60,60,0.08);border:1px solid rgba(200,60,60,0.25);color:#f09090;',
        default     => 'background:rgba(130,170,255,0.08);border:1px solid rgba(130,170,255,0.2);color:#8ab4ff;',
      };
      $statusLabel = ucfirst($listing->status);
    @endphp
    <div style="background:var(--bg-card2);border:1px solid var(--border);border-radius:12px;padding:1rem 1.25rem;display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
      <div style="flex:1;min-width:200px;">
        <div style="font-weight:700;color:var(--cream);margin-bottom:0.2rem;">{{ $listing->title }}</div>
        <div style="font-size:0.8rem;color:var(--muted);">{{ $listing->category }} · ৳{{ number_format($listing->price_min,0) }}–৳{{ number_format($listing->price_max,0) }}</div>
      </div>
      <span style="padding:0.3rem 0.75rem;border-radius:20px;font-size:0.75rem;font-weight:700;flex-shrink:0;{{ $statusStyle }}">
        {{ $statusLabel }}
      </span>
      @if($listing->status === 'rejected' && $listing->rejection_reason)
      <div style="width:100%;font-size:0.78rem;color:#f09090;padding:0.5rem 0.75rem;background:rgba(200,60,60,0.07);border-radius:8px;border:1px solid rgba(200,60,60,0.18);margin-top:0.25rem;">
        <strong>Reason:</strong> {{ $listing->rejection_reason }}
      </div>
      @elseif($listing->status === 'pending')
      <div style="width:100%;font-size:0.78rem;color:var(--muted);margin-top:0.1rem;">
        Under review — the admin will approve or reject within 24 hours.
      </div>
      @endif
    </div>
    @endforeach
  </div>
  @endif

  {{-- JOB HISTORY --}}
  <div class="section-hd">
    <h2>Completed Job <em>History</em></h2>
    <span class="section-tag">{{ $stats['completed_jobs'] }} jobs</span>
  </div>
  <div class="history-table-wrap">
    <table class="history-table">
      <thead>
        <tr>
          <th>Device</th><th>Collector</th><th>Date</th><th>Amount</th><th>Rating</th>
        </tr>
      </thead>
      <tbody>
        @forelse($completedJobs as $job)
        <tr>
          <td><strong>{{ $job->booking->device_name }}</strong><br/><span style="font-size:0.75rem;color:var(--muted);">{{ $job->reference }}</span></td>
          <td>{{ $job->booking->collector->name }}</td>
          <td>{{ $job->updated_at->format('M j, Y') }}</td>
          <td style="color:var(--amber-lt);font-weight:700;">৳{{ number_format($job->payment_amount, 0) }}</td>
          <td>
            @if($job->booking->serviceListing)
              {{ $job->booking->serviceListing->category }}
            @else —
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:2rem;">No completed jobs yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function acceptBooking(id) {
    if (!confirm('Accept this booking request?')) return;
    const actionsEl = document.getElementById('booking-actions-' + id);
    if (actionsEl) actionsEl.innerHTML = '<span style="color:var(--muted);font-size:0.82rem;">Processing...</span>';

    fetch('/bookings/' + id + '/accept', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        if (actionsEl) actionsEl.innerHTML =
          '<span style="color:#5de0b0;font-size:0.82rem;font-weight:600;">✓ Accepted — Job ' + data.reference + ' created</span>';
        setTimeout(() => location.reload(), 1800);
      } else {
        alert(data.message || 'Could not accept booking.');
        location.reload();
      }
    })
    .catch(() => { alert('Network error. Please try again.'); location.reload(); });
  }

  function rejectBooking(id) {
    const reason = prompt('Reason for declining (optional — leave blank to skip):');
    if (reason === null) return; // user hit Cancel on prompt

    const actionsEl = document.getElementById('booking-actions-' + id);
    if (actionsEl) actionsEl.innerHTML = '<span style="color:var(--muted);font-size:0.82rem;">Processing...</span>';

    fetch('/bookings/' + id + '/reject', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({ reason: reason || '' }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const row = document.getElementById('booking-row-' + id);
        if (row) {
          row.style.opacity = '0.4';
          if (actionsEl) actionsEl.innerHTML = '<span style="color:#f09090;font-size:0.82rem;font-weight:600;">✕ Declined</span>';
          setTimeout(() => row.remove(), 1000);
        }
      } else {
        alert(data.message || 'Could not decline booking.');
        location.reload();
      }
    })
    .catch(() => { alert('Network error. Please try again.'); location.reload(); });
  }
</script>

@if(!auth()->check() || !auth()->user()->isTechnician())
  <script>window.location.href = '/';</script>
@endif
@endsection