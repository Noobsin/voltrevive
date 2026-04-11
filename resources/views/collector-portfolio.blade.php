@extends('layout')
@section('title')
{{ auth()->user()->name }} — Collector Portfolio
@endsection

@section('styles')
<style>
  /* ── HERO ── */
  .col-hero {
    background: var(--bg-card); border-bottom: 1px solid var(--border);
    position: relative; z-index: 1;
  }
  .col-hero-inner { max-width: 1400px; margin: 0 auto; padding: 2.5rem 2rem 2rem; }
  .back-link {
    display: inline-flex; align-items: center; gap: 0.4rem;
    color: var(--muted); text-decoration: none; font-size: 0.85rem;
    margin-bottom: 2rem; transition: color 0.2s;
  }
  .back-link:hover { color: var(--cream); }
  .col-header { display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; }
  .col-avatar {
    width: 90px; height: 90px; border-radius: 50%;
    background: linear-gradient(135deg, #1a0e2a, #2a1540);
    border: 3px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif; font-size: 2.2rem;
    font-weight: 900; color: var(--amber-lt); flex-shrink: 0;
  }
  .col-info h1 { font-size: clamp(1.6rem, 3vw, 2rem); margin-bottom: 0.25rem; }
  .col-info p { color: var(--muted); font-size: 0.88rem; }
  .col-stats {
    display: flex; gap: 1px; background: var(--border);
    border: 1px solid var(--border); border-radius: 12px;
    overflow: hidden; margin-left: auto; flex-shrink: 0;
  }
  .col-stat {
    background: var(--bg-card2); padding: 1rem 1.5rem;
    text-align: center; min-width: 100px;
  }
  .col-stat strong {
    display: block; font-family: 'Playfair Display', serif;
    font-size: 1.6rem; color: var(--amber-lt); line-height: 1;
  }
  .col-stat span { font-size: 0.72rem; color: var(--muted); }

  /* ── MAIN ── */
  .col-main {
    max-width: 1400px; margin: 0 auto; padding: 2.5rem 2rem 5rem;
    position: relative; z-index: 1;
  }
  .section-label {
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.12em;
    text-transform: uppercase; color: var(--muted); margin-bottom: 1.5rem;
    display: flex; align-items: center; gap: 0.75rem;
  }
  .section-label::after { content: ''; flex: 1; height: 1px; background: var(--border); }

  /* ── RESTORATION GRID ── */
  .col-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1.5rem;
  }
  .col-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
    transition: border-color 0.25s, transform 0.25s;
    animation: fadeUp 0.4s ease both;
  }
  .col-card:hover { border-color: rgba(212,137,26,0.5); transform: translateY(-3px); }
  .col-card:nth-child(1){animation-delay:0.05s}
  .col-card:nth-child(2){animation-delay:0.10s}
  .col-card:nth-child(3){animation-delay:0.15s}
  .col-card:nth-child(4){animation-delay:0.20s}
  .col-card:nth-child(5){animation-delay:0.25s}

  /* BA image row */
  .col-card-ba {
    display: grid; grid-template-columns: 1fr 1fr;
    height: 160px; gap: 2px; position: relative;
  }
  .col-ba-img { background-size: cover; background-position: center; position: relative; }
  .ba-label {
    position: absolute; bottom: 6px;
    background: rgba(22,19,16,0.82); padding: 0.15rem 0.45rem;
    border-radius: 3px; font-size: 0.6rem; font-weight: 700;
    letter-spacing: 0.07em; text-transform: uppercase;
  }
  .ba-label.before { left: 6px; color: var(--muted); }
  .ba-label.after  { right: 6px; color: var(--amber-lt); }
  .col-status-badge {
    position: absolute; top: 10px; right: 10px;
    padding: 0.2rem 0.65rem; border-radius: 20px;
    font-size: 0.65rem; font-weight: 700; letter-spacing: 0.05em;
    backdrop-filter: blur(6px); z-index: 2;
  }
  .status-completed { background: rgba(30,160,100,0.85); border: 1px solid rgba(74,200,120,0.4); color: #5de0b0; }
  .status-inprogress { background: rgba(212,137,26,0.85); border: 1px solid rgba(232,168,48,0.4); color: #161310; }

  .col-card-body { padding: 1.25rem; }
  .col-card-device {
    font-family: 'Playfair Display', serif; font-size: 1.05rem;
    font-weight: 700; margin-bottom: 0.3rem;
  }
  .col-card-cat {
    font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--amber); margin-bottom: 0.75rem;
  }
  .col-card-row {
    display: flex; align-items: center; justify-content: space-between;
    font-size: 0.8rem; color: var(--muted); margin-bottom: 0.4rem;
  }
  .col-card-row span:last-child { color: var(--cream); font-weight: 500; }
  .col-card-tech {
    display: flex; align-items: center; gap: 0.5rem;
    padding-top: 0.85rem; margin-top: 0.85rem;
    border-top: 1px solid var(--border);
  }
  .tech-mini-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    background: var(--bg-card2); border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.72rem; font-weight: 700; color: var(--amber);
  }
  .tech-mini-info { flex: 1; }
  .tech-mini-name { font-size: 0.82rem; font-weight: 600; }
  .tech-mini-link {
    font-size: 0.72rem; color: var(--muted); text-decoration: none;
    transition: color 0.2s;
  }
  .tech-mini-link:hover { color: var(--amber-lt); }
  .col-card-rating { color: var(--amber); font-size: 0.82rem; font-weight: 600; }

  /* ── EMPTY STATE for in-progress ── */
  .ba-placeholder {
    background: var(--bg-card2);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.7rem; color: var(--muted); text-align: center;
    padding: 0.5rem;
  }

  /* image colours */
  .im-sb { background: linear-gradient(135deg,#2a1f0e,#3d2b0f); }
  .im-sa { background: linear-gradient(135deg,#1a2a1a,#233520); }
  .im-rb { background: linear-gradient(135deg,#1e1a2a,#2a2038); }
  .im-ra { background: linear-gradient(135deg,#0e1e2a,#122438); }
  .im-hb { background: linear-gradient(135deg,#201a0a,#30250e); }
  .im-ha { background: linear-gradient(135deg,#0a1a20,#0e2530); }
  .im-gb { background: linear-gradient(135deg,#2a1a1a,#3a1e1e); }
  .im-ga { background: linear-gradient(135deg,#1a2a28,#1e3530); }
  .im-cb { background: linear-gradient(135deg,#1a1a20,#25252e); }
  .im-ca { background: linear-gradient(135deg,#101a10,#152016); }

  @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:768px) { .col-header{flex-direction:column;align-items:flex-start} .col-stats{margin-left:0;width:100%} .col-hero-inner{padding:1.5rem 1rem 1.5rem} .col-main{padding:1.5rem 1rem 3rem} }
</style>
@endsection

@section('content')

{{-- HERO --}}
<div class="col-hero">
  <div class="col-hero-inner">
    <a href="/browse" class="back-link">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      Back to Browse
    </a>
    <div class="col-header">
      <div class="col-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
      <div class="col-info">
        <h1>{{ auth()->user()->name }}</h1>
        <p>Collector since {{ auth()->user()->created_at->format('F Y') }}</p>
      </div>
      <div class="col-stats">
        <div class="col-stat"><strong>{{ $stats['total_restored'] }}</strong><span>Devices Restored</span></div>
        <div class="col-stat"><strong>{{ $stats['completed'] }}</strong><span>Completed</span></div>
        <div class="col-stat"><strong>{{ $stats['in_progress'] }}</strong><span>In Progress</span></div>
      </div>
    </div>
  </div>
</div>

{{-- MAIN --}}
<div class="col-main">
  <div class="section-label">Restoration History</div>

  <div class="col-grid">

    {{-- COMPLETED JOBS --}}
    @forelse($completedJobs as $job)
    @php
      $svc      = $job->booking->serviceListing;
      $tech     = $job->booking->technicianProfile;
      $techUser = $tech->user ?? null;
      $review   = $job->review;
      $stars    = $review ? str_repeat('★',$review->rating).str_repeat('☆',5-$review->rating) : null;
      $catRaw   = $svc->category ?? 'Other';
      $dateStr  = $job->updated_at->format('F Y');
      $bSrc     = ($svc && $svc->before_image) ? asset('storage/'.$svc->before_image) : '';
      $aSrc     = ($svc && $svc->after_image)  ? asset('storage/'.$svc->after_image)  : '';
    @endphp
    <div class="col-card">
      <div class="col-card-ba">
        @if($bSrc)
          <div class="col-ba-img" style="background:url('{{ $bSrc }}') center/cover;"><span class="ba-label before">Before</span></div>
          <div class="col-ba-img" style="background:url('{{ $aSrc }}') center/cover;"><span class="ba-label after">After</span></div>
        @else
          <div class="col-ba-img im-sb"><span class="ba-label before">Before</span></div>
          <div class="col-ba-img im-sa"><span class="ba-label after">After</span></div>
        @endif
        <span class="col-status-badge status-completed">✓ Completed</span>
      </div>
      <div class="col-card-body">
        <div class="col-card-cat">{{ $catRaw }}</div>
        <div class="col-card-device">{{ $job->booking->device_name }}</div>
        <div class="col-card-row"><span>Restored</span><span>{{ $dateStr }}</span></div>
        <div class="col-card-row"><span>Job</span><span>#{{ $job->reference }}</span></div>
        @if($techUser)
        <div class="col-card-tech">
          <div class="tech-mini-avatar">{{ strtoupper(substr($techUser->name,0,1)) }}</div>
          <div class="tech-mini-info">
            <div class="tech-mini-name">{{ $techUser->name }}</div>
            <a href="/technicians/{{ $tech->user_id }}" class="tech-mini-link">View Technician Profile →</a>
          </div>
          @if($stars)
            <div class="col-card-rating">{{ $stars }}</div>
          @else
            <a href="/jobs/{{ $job->id }}/review" class="col-card-rating" style="font-size:0.78rem;color:var(--amber-lt);text-decoration:none;">⭐ Leave Review</a>
          @endif
        </div>
        @endif
      </div>
    </div>
    @empty
    @endforelse

    {{-- ACTIVE / IN-PROGRESS JOBS --}}
    @foreach($activeJobs as $job)
    @php
      $svc      = $job->booking->serviceListing;
      $tech     = $job->booking->technicianProfile;
      $techUser = $tech->user ?? null;
      $catRaw   = $svc->category ?? 'Other';
    @endphp
    <div class="col-card">
      <div class="col-card-ba">
        <div class="col-ba-img im-sb"><span class="ba-label before">Before</span></div>
        <div class="col-ba-img im-sa" style="opacity:0.35;"><span class="ba-label after">After</span></div>
        <span class="col-status-badge" style="background:rgba(130,170,255,0.12);color:#8ab4ff;border-color:rgba(130,170,255,0.25);">⚙ In Progress</span>
      </div>
      <div class="col-card-body">
        <div class="col-card-cat">{{ $catRaw }}</div>
        <div class="col-card-device">{{ $job->booking->device_name }}</div>
        <div class="col-card-row"><span>Status</span><span>{{ ucfirst(str_replace('_',' ',$job->status)) }}</span></div>
        <div class="col-card-row"><span>Job</span><span>#{{ $job->reference }}</span></div>
        @if($techUser)
        <div class="col-card-tech">
          <div class="tech-mini-avatar">{{ strtoupper(substr($techUser->name,0,1)) }}</div>
          <div class="tech-mini-info">
            <div class="tech-mini-name">{{ $techUser->name }}</div>
            <a href="/jobs/{{ $job->id }}" class="tech-mini-link">Track Job →</a>
          </div>
        </div>
        @endif
      </div>
    </div>
    @endforeach

  </div>{{-- end col-grid --}}
</div>

@endsection