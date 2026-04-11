@extends('layout')
@section('title', 'My Dashboard — Collector')

@section('styles')
<style>
  .dash-wrap { max-width:1200px; margin:0 auto; padding:2.5rem 2rem 5rem; position:relative; z-index:1; }
  .dash-header { margin-bottom:2rem; }
  .dash-eyebrow { font-size:0.72rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--amber); margin-bottom:0.5rem; }
  .dash-title { font-family:'Playfair Display',serif; font-size:2rem; font-weight:900; margin-bottom:0.3rem; }
  .dash-sub { color:var(--muted); font-size:0.9rem; }

  /* ── STAT CARDS ── */
  .stat-row { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:2.5rem; }
  .stat-card { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; padding:1.25rem 1.5rem; }
  .stat-icon { font-size:1.3rem; margin-bottom:0.6rem; }
  .stat-num { font-family:'Playfair Display',serif; font-size:1.9rem; font-weight:900; color:var(--amber-lt); line-height:1; margin-bottom:0.3rem; }
  .stat-lbl { font-size:0.78rem; color:var(--muted); }

  /* ── SECTION HEADER ── */
  .section-hd { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; }
  .section-hd h2 { font-family:'Playfair Display',serif; font-size:1.25rem; }
  .section-hd h2 em { font-style:italic; color:var(--amber-lt); }
  .section-tag { font-size:0.72rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted); background:var(--bg-card2); border:1px solid var(--border); padding:0.25rem 0.7rem; border-radius:20px; }

  /* ── PURCHASE HISTORY TABLE ── */
  .history-panel { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:2.5rem; }
  .history-table { width:100%; border-collapse:collapse; }
  .history-table thead tr { background:var(--bg-card2); border-bottom:1px solid var(--border); }
  .history-table th { padding:0.85rem 1.25rem; text-align:left; font-size:0.72rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--muted); }
  .history-table td { padding:1rem 1.25rem; font-size:0.88rem; border-bottom:1px solid var(--border); vertical-align:middle; }
  .history-table tr:last-child td { border-bottom:none; }
  .history-table tr:hover td { background:var(--bg-card2); }
  .device-cell { display:flex; align-items:center; gap:0.75rem; }
  .device-thumb { width:44px; height:44px; border-radius:8px; background:var(--bg-card2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
  .device-name { font-weight:600; font-size:0.9rem; }
  .device-cat { font-size:0.72rem; color:var(--muted); }
  .tech-cell { display:flex; align-items:center; gap:0.5rem; }
  .tech-dot { width:28px; height:28px; border-radius:50%; background:var(--bg-card2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; color:var(--amber); }
  .status-pill { display:inline-flex; align-items:center; gap:0.35rem; padding:0.2rem 0.7rem; border-radius:20px; font-size:0.72rem; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; }
  .pill-completed { background:rgba(93,224,176,0.1); border:1px solid rgba(93,224,176,0.25); color:#5de0b0; }
  .pill-inprogress { background:rgba(212,137,26,0.1); border:1px solid rgba(212,137,26,0.25); color:var(--amber-lt); }
  .pill-pending { background:rgba(130,170,255,0.08); border:1px solid rgba(130,170,255,0.2); color:#8ab4ff; }
  .pill-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }
  .pill-dot.pulse { animation:pulse 2s infinite; }
  .amount-cell { font-weight:700; color:var(--amber-lt); }
  .btn-view-job { background:transparent; border:1px solid var(--border); color:var(--cream); padding:0.35rem 0.85rem; border-radius:6px; font-size:0.78rem; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all 0.2s; text-decoration:none; display:inline-block; }
  .btn-view-job:hover { border-color:var(--amber); color:var(--amber-lt); }

  /* ── ACTIVE DEVICE CARDS ── */
  .devices-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.25rem; }
  .device-card { background:var(--bg-card); border:1px solid var(--border); border-radius:12px; overflow:hidden; transition:border-color 0.2s,transform 0.2s; }
  .device-card:hover { border-color:var(--amber); transform:translateY(-2px); }
  .device-card-img { height:120px; background:linear-gradient(135deg,#2a1f0e,#1a2a28); display:flex; align-items:center; justify-content:center; font-size:2.5rem; }
  .device-card-body { padding:1rem 1.25rem; }
  .device-card-name { font-family:'Playfair Display',serif; font-size:1rem; font-weight:700; margin-bottom:0.3rem; }
  .device-card-tech { font-size:0.78rem; color:var(--muted); margin-bottom:0.75rem; }
  .device-card-footer { display:flex; align-items:center; justify-content:space-between; }
  .progress-mini { flex:1; margin-right:0.75rem; }
  .progress-bar-sm { height:4px; background:var(--border); border-radius:2px; overflow:hidden; margin-bottom:0.2rem; }
  .progress-fill-sm { height:100%; background:var(--amber); border-radius:2px; }
  .progress-label-sm { font-size:0.68rem; color:var(--muted); }

  @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
  @keyframes fadeUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:900px) { .stat-row { grid-template-columns:repeat(2,1fr); } }
  @media(max-width:600px) { .dash-wrap { padding:1.5rem 1rem 4rem; } .stat-row { grid-template-columns:1fr 1fr; } }
</style>
@endsection

@section('content')
<div class="dash-wrap">

  {{-- HEADER --}}
  <div class="dash-header">
    <div class="dash-eyebrow">Collector Dashboard</div>
    <h1 class="dash-title">Welcome back, <em>{{ explode(' ', auth()->user()->name)[0] }}</em></h1>
    <p class="dash-sub">Track your devices, payment history, and ongoing restorations.</p>
  </div>

  {{-- STATS --}}
  <div class="stat-row">
    <div class="stat-card">
      <div class="stat-icon">📦</div>
      <div class="stat-num">{{ $stats['total_sent'] }}</div>
      <div class="stat-lbl">Total Bookings Made</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">✅</div>
      <div class="stat-num">{{ $stats['completed'] }}</div>
      <div class="stat-lbl">Restorations Completed</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🔧</div>
      <div class="stat-num">{{ $stats['under_repair'] }}</div>
      <div class="stat-lbl">Under Repair</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">💳</div>
      <div class="stat-num">৳{{ number_format($stats['total_spent'], 0) }}</div>
      <div class="stat-lbl">Total Spent</div>
    </div>
  </div>

  {{-- DEVICES UNDER REPAIR --}}
  <div class="section-hd">
    <h2>Devices <em>Under Repair</em></h2>
    <span class="section-tag">{{ $activeJobs->count() }} active</span>
  </div>
  <div class="devices-grid" style="margin-bottom:2.5rem;">
    @forelse($activeJobs as $job)
    <div class="device-card">
      <div class="device-card-img">🔧</div>
      <div class="device-card-body">
        <div class="device-card-name">{{ $job->booking->device_name }}</div>
        <div class="device-card-tech">🔧 {{ $job->booking->technicianProfile->user->name }} · {{ $job->booking->technicianProfile->location }}</div>
        <div class="device-card-footer">
          <div class="progress-mini">
            @php
              $steps = count($job->timeline_state ?? []);
              $done  = collect($job->timeline_state ?? [])->filter()->count();
              $pct   = $steps > 0 ? round(($done / 7) * 100) : 0;
            @endphp
            <div class="progress-bar-sm"><div class="progress-fill-sm" style="width:{{ $pct }}%"></div></div>
            <div class="progress-label-sm">{{ $done }} of 7 stages</div>
          </div>
          <a href="/jobs/{{ $job->id }}" class="btn-view-job">Track &rarr;</a>
        </div>
      </div>
    </div>
    @empty
    <div style="color:var(--muted);font-size:0.88rem;padding:1rem 0;">No active repairs right now.</div>
    @endforelse
  </div>

  {{-- PURCHASE HISTORY --}}
  <div class="section-hd">
    <h2>Purchase <em>History</em></h2>
    <span class="section-tag">{{ $allBookings->count() }} bookings</span>
  </div>
  <div class="history-panel">
    <table class="history-table">
      <thead>
        <tr>
          <th>Device</th><th>Technician</th><th>Date</th><th>Amount</th><th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($allBookings as $booking)
        <tr>
          <td><strong>{{ $booking->device_name }}</strong></td>
          <td>{{ $booking->technicianProfile->user->name ?? '—' }}</td>
          <td>{{ $booking->requested_date->format('M j, Y') }}</td>
          <td>
            @if($booking->job)
              <span style="color:var(--amber-lt);font-weight:700;">৳{{ number_format($booking->job->payment_amount, 0) }}</span>
            @else —
            @endif
          </td>
          <td>
            @php
              $statusStyles = [
                'pending'   => 'background:rgba(212,137,26,0.1);color:var(--amber-lt);',
                'confirmed' => 'background:rgba(74,200,120,0.1);color:#5de0b0;',
                'rejected'  => 'background:rgba(200,60,60,0.08);color:#f09090;',
                'cancelled' => 'background:rgba(120,120,120,0.08);color:var(--muted);',
              ];
              $s = $booking->status;
              $jobStatus = $booking->job ? $booking->job->status : null;
              $displayStatus = $jobStatus ?? $s;
              $style = $statusStyles[$s] ?? '';
            @endphp
            <span style="padding:0.2rem 0.6rem;border-radius:20px;font-size:0.75rem;font-weight:700;{{ $style }}">
              {{ ucfirst(str_replace('_', ' ', $displayStatus)) }}
            </span>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:2rem;">No bookings yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if(!auth()->check() || auth()->user()->isAdmin() || auth()->user()->isTechnician())
  <script>window.location.href = '/';</script>
@endif
@endsection