@extends('layout')
@section('title', 'Community Events')

@section('styles')
<style>
  .hero { position:relative; z-index:1; padding:4rem 2rem 3rem; max-width:1400px; margin:0 auto; display:flex; align-items:flex-end; justify-content:space-between; gap:2rem; }
  .hero-text h1 { font-size:clamp(2rem,4vw,3rem); line-height:1.1; }
  .hero-text h1 em { font-style:italic; color:var(--amber-lt); }
  .hero-text p { color:var(--muted); font-size:1rem; margin-top:0.6rem; max-width:480px; }
  .hero-stats { display:flex; gap:1.5rem; flex-shrink:0; }
  .hero-stat { text-align:center; }
  .hero-stat strong { display:block; font-family:'Playfair Display',serif; font-size:2rem; color:var(--amber-lt); }
  .hero-stat span { font-size:0.78rem; color:var(--muted); }
  .filter-tabs { max-width:1400px; margin:0 auto; padding:0 2rem 2rem; position:relative; z-index:1; display:flex; gap:0.5rem; flex-wrap:wrap; }
  .tab { padding:0.5rem 1.2rem; border-radius:20px; border:1px solid var(--border); font-size:0.85rem; cursor:pointer; transition:all 0.15s; color:var(--muted); background:transparent; font-family:'DM Sans',sans-serif; }
  .tab:hover { border-color:var(--amber); color:var(--amber-lt); }
  .tab.active { background:rgba(212,137,26,0.12); border-color:var(--amber); color:var(--amber-lt); font-weight:600; }
  .events-section { max-width:1400px; margin:0 auto; padding:0 2rem 5rem; position:relative; z-index:1; }
  .section-label { font-size:0.7rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--muted); margin-bottom:1rem; display:flex; align-items:center; gap:0.75rem; }
  .section-label::after { content:''; flex:1; height:1px; background:var(--border); }
  .events-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:1.5rem; }
  .event-card { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; overflow:hidden; transition:border-color 0.25s,transform 0.25s; animation:fadeUp 0.4s ease both; display:flex; flex-direction:column; }
  .event-card:hover { border-color:rgba(212,137,26,0.5); transform:translateY(-3px); }
  .event-card.rsvped { border-color:var(--amber); }
  .event-card:nth-child(1){animation-delay:0.05s}.event-card:nth-child(2){animation-delay:0.1s}.event-card:nth-child(3){animation-delay:0.15s}
  .event-card:nth-child(4){animation-delay:0.2s}.event-card:nth-child(5){animation-delay:0.25s}.event-card:nth-child(6){animation-delay:0.3s}
  .event-img { height:160px; position:relative; display:flex; align-items:flex-end; }
  .event-img-bg { position:absolute; inset:0; background-size:cover; background-position:center; }
  .event-type-badge { position:absolute; top:12px; right:12px; padding:0.25rem 0.75rem; border-radius:20px; font-size:0.7rem; font-weight:700; backdrop-filter:blur(8px); }
  .badge-swap { background:rgba(138,90,20,0.85); border:1px solid rgba(212,137,26,0.5); color:var(--amber-lt); }
  .badge-repair { background:rgba(20,80,60,0.85); border:1px solid rgba(30,160,120,0.4); color:#5de0b0; }
  .badge-exhibition { background:rgba(40,20,80,0.85); border:1px solid rgba(130,80,200,0.4); color:#c0a0f0; }
  .badge-workshop { background:rgba(10,40,80,0.85); border:1px solid rgba(40,120,200,0.4); color:#90c0f0; }
  .sold-out-overlay { position:absolute; inset:0; background:rgba(22,19,16,0.75); backdrop-filter:blur(2px); display:flex; align-items:center; justify-content:center; }
  .sold-out-tag { border:2px solid var(--muted); color:var(--muted); padding:0.5rem 1.5rem; border-radius:8px; font-weight:700; font-size:0.9rem; letter-spacing:0.1em; text-transform:uppercase; transform:rotate(-8deg); }
  .date-ribbon { position:absolute; left:12px; top:12px; background:var(--bg-card); border:1px solid var(--border); border-radius:8px; padding:0.4rem 0.7rem; text-align:center; min-width:48px; }
  .date-month { font-size:0.65rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--amber); }
  .date-day { font-family:'Playfair Display',serif; font-size:1.4rem; font-weight:900; line-height:1; }
  .event-body { padding:1.25rem 1.25rem 1rem; flex:1; display:flex; flex-direction:column; }
  .event-title { font-family:'Playfair Display',serif; font-size:1.1rem; margin-bottom:0.5rem; line-height:1.3; }
  .event-desc { font-size:0.83rem; color:var(--muted); line-height:1.55; margin-bottom:1rem; flex:1; }
  .event-meta { display:flex; flex-direction:column; gap:0.3rem; margin-bottom:1rem; }
  .meta-row { display:flex; align-items:center; gap:0.5rem; font-size:0.8rem; color:var(--muted); }
  .event-footer { display:flex; align-items:center; justify-content:space-between; padding-top:1rem; border-top:1px solid var(--border); gap:0.75rem; }
  .ticket-count { font-size:1.1rem; font-weight:700; color:var(--cream); }
  .ticket-count.low { color:#e06060; }
  .ticket-label { font-size:0.72rem; color:var(--muted); }
  .ticket-bar-wrap { height:3px; background:var(--border); border-radius:2px; margin-top:4px; width:80px; }
  .ticket-bar { height:100%; border-radius:2px; background:var(--amber); }
  .ticket-bar.low { background:#e06060; }
  .btn-rsvp { background:var(--amber); border:none; color:#161310; padding:0.55rem 1.25rem; border-radius:8px; font-weight:700; font-size:0.85rem; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all 0.2s; white-space:nowrap; display:flex; align-items:center; gap:0.4rem; }
  .btn-rsvp:hover { background:var(--amber-lt); }
  .btn-rsvp.done { background:rgba(212,137,26,0.12); border:1px solid var(--amber); color:var(--amber-lt); }
  .btn-rsvp:disabled { opacity:0.4; cursor:not-allowed; background:var(--border); color:var(--muted); }
  .attendee-count { font-size:0.75rem; color:var(--muted); margin-top:0.2rem; }
  .ev-bg-1{background:linear-gradient(135deg,#1a120a,#2e1e08)} .ev-bg-2{background:linear-gradient(135deg,#0a1015,#0e1c28)}
  .ev-bg-3{background:linear-gradient(135deg,#10080e,#1e0e1c)} .ev-bg-4{background:linear-gradient(135deg,#080e10,#0a1e1c)}
  .ev-bg-5{background:linear-gradient(135deg,#12100a,#201c0a)} .ev-bg-6{background:linear-gradient(135deg,#0e0810,#1c1020)}
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.85); z-index:999; align-items:center; justify-content:center; padding:1rem; }
  .modal-overlay.open { display:flex; }
  .modal { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; max-width:460px; width:100%; animation:fadeUp 0.3s ease; overflow:hidden; }
  .modal-slip-header { background:var(--amber); color:#161310; padding:1.5rem 2rem 1.25rem; text-align:center; }
  .modal-slip-header h2 { font-family:'Playfair Display',serif; font-size:1.4rem; }
  .modal-slip-header p { font-size:0.82rem; opacity:0.75; margin-top:0.25rem; }
  .slip-body { padding:1.75rem 2rem; }
  .slip-label { font-size:0.68rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--muted); margin-bottom:0.2rem; }
  .slip-value { font-size:0.95rem; font-weight:600; margin-bottom:1rem; }
  .slip-divider { border:none; border-top:1px dashed var(--border); margin:1.25rem 0; }
  .slip-ref { text-align:center; background:var(--bg-card2); border:1px solid var(--border); border-radius:8px; padding:0.75rem; }
  .slip-ref-label { font-size:0.7rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.08em; }
  .slip-ref-num { font-family:'Playfair Display',serif; font-size:1.6rem; font-weight:900; color:var(--amber-lt); letter-spacing:0.1em; }
  .modal-footer { padding:1rem 2rem 1.5rem; display:flex; gap:0.75rem; }
  .btn-modal-close { flex:1; background:transparent; border:1px solid var(--border); color:var(--muted); padding:0.65rem; border-radius:8px; cursor:pointer; font-size:0.88rem; font-family:'DM Sans',sans-serif; }
  .btn-modal-close:hover { border-color:var(--muted); color:var(--cream); }
  .btn-download { flex:2; background:var(--amber); border:none; color:#161310; padding:0.65rem 1rem; border-radius:8px; font-weight:700; font-size:0.88rem; cursor:pointer; font-family:'DM Sans',sans-serif; display:flex; align-items:center; justify-content:center; gap:0.5rem; }
  .btn-download:hover { background:var(--amber-lt); }

  @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
  @media(max-width:600px){ .hero{flex-direction:column;align-items:flex-start;padding:2rem 1rem} .filter-tabs,.events-section,.admin-strip{padding-left:1rem;padding-right:1rem} }
</style>
@endsection

@section('content')

<div class="hero">
  <div class="hero-text">
    <h1>Community <em>Events</em></h1>
    <p>Swap meets, repair cafés, and exhibitions for vintage electronics enthusiasts.</p>
  </div>
  <div class="hero-stats">
    <div class="hero-stat"><strong>{{ $stats['upcoming'] }}</strong><span>Upcoming Events</span></div>
    <div class="hero-stat"><strong>{{ $stats['rsvps'] }}</strong><span>Total RSVPs</span></div>
    <div class="hero-stat"><strong>{{ $stats['cities'] }}</strong><span>Cities</span></div>
  </div>
</div>

<div class="filter-tabs">
  <button class="tab active" onclick="filterEvents(this,'all')">All Events</button>
  <button class="tab" onclick="filterEvents(this,'swap')">Swap Meets</button>
  <button class="tab" onclick="filterEvents(this,'repair')">Repair Cafés</button>
  <button class="tab" onclick="filterEvents(this,'exhibition')">Exhibitions</button>
  <button class="tab" onclick="filterEvents(this,'workshop')">Workshops</button>
</div>

<div class="events-section">
  <div class="section-label">Upcoming Events</div>
  <div class="events-grid" id="events-grid">

    @php
      $typeMap = [
        'Swap Meet'   => ['slug'=>'swap',       'badge'=>'badge-swap',       'label'=>'Swap Meet'],
        'Repair Café' => ['slug'=>'repair',     'badge'=>'badge-repair',     'label'=>'Repair Café'],
        'Exhibition'  => ['slug'=>'exhibition', 'badge'=>'badge-exhibition', 'label'=>'Exhibition'],
        'Workshop'    => ['slug'=>'workshop',   'badge'=>'badge-workshop',   'label'=>'Workshop'],
      ];
      $bgClasses = ['ev-bg-1','ev-bg-2','ev-bg-3','ev-bg-4','ev-bg-5','ev-bg-6'];
    @endphp

    @forelse($events as $i => $event)
    @php
      $type        = $typeMap[$event->event_type] ?? ['slug'=>'swap','badge'=>'badge-swap','label'=>$event->event_type];
      $bg          = $bgClasses[$i % 6];
      $remaining   = $event->ticketsRemaining();
      $soldOut     = $event->isSoldOut();
      $pct         = $event->ticket_count > 0 ? round(($remaining / $event->ticket_count) * 100) : 0;
      $lowTickets  = $remaining > 0 && $remaining <= 10;
      $userRsvped  = in_array($event->id, $rsvpedIds);
    @endphp
    <div class="event-card{{ $userRsvped ? ' rsvped' : '' }}" data-type="{{ $type['slug'] }}" data-id="{{ $event->id }}">
      <div class="event-img">
        <div class="event-img-bg {{ $bg }}"></div>
        <div class="date-ribbon">
          <div class="date-month">{{ $event->event_date->format('M') }}</div>
          <div class="date-day">{{ $event->event_date->format('j') }}</div>
        </div>
        <div class="event-type-badge {{ $type['badge'] }}">{{ $type['label'] }}</div>
        @if($soldOut)
        <div class="sold-out-overlay"><div class="sold-out-tag">Sold Out</div></div>
        @endif
      </div>
      <div class="event-body">
        <h3 class="event-title">{{ $event->title }}</h3>
        <p class="event-desc">{{ Str::limit($event->description, 120) }}</p>
        <div class="event-meta">
          <div class="meta-row">📍 {{ $event->location }}</div>
          <div class="meta-row">🕙 {{ $event->event_date->format('g:i A') }} — {{ $event->event_date->format('D, M j, Y') }}</div>
          <div class="meta-row">👤 Hosted by {{ $event->creator->name ?? 'VoltRevive' }}</div>
        </div>
        <div class="event-footer">
          <div>
            <div class="ticket-count{{ $lowTickets ? ' low' : '' }}">{{ $remaining }}</div>
            <div class="ticket-label">tickets left</div>
            <div class="ticket-bar-wrap">
              <div class="ticket-bar{{ $lowTickets ? ' low' : '' }}" style="width:{{ $pct }}%"></div>
            </div>
          </div>
          <div>
            @if($soldOut)
              <button class="btn-rsvp" disabled>Sold Out</button>
            @elseif($userRsvped)
              <button class="btn-rsvp done"
                onclick="showSlipFromData({{ $event->id }}, '{{ addslashes($event->title) }}', '{{ $event->event_date->format('M j, Y') }}', '{{ addslashes($event->location) }}')">
                ✓ Going
              </button>
            @else
              <button class="btn-rsvp" id="rsvp-btn-{{ $event->id }}"
                onclick="doRsvp({{ $event->id }}, '{{ addslashes($event->title) }}', '{{ $event->event_date->format('M j, Y') }}', '{{ addslashes($event->location) }}')">
                RSVP Now
              </button>
            @endif
            <div class="attendee-count" id="att-{{ $event->id }}">{{ $event->attendee_count }} attending</div>
          </div>
        </div>
      </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:4rem;color:var(--muted);">
      <div style="font-size:2.5rem;margin-bottom:1rem;">📅</div>
      <p style="font-weight:600;">No upcoming events</p>
      <p style="font-size:0.85rem;margin-top:0.4rem;">Check back soon — events are published by the admin.</p>
    </div>
    @endforelse

  </div>
</div>

{{-- BOOKING SLIP MODAL --}}
<div class="modal-overlay" id="slip-modal">
  <div class="modal">
    <div class="modal-slip-header">
      <h2>⚡ Booking Confirmed!</h2>
      <p>VoltRevive Community Events</p>
    </div>
    <div class="slip-body">
      <div class="slip-label">Event</div><div class="slip-value" id="slip-event-name">—</div>
      <div class="slip-label">Date</div><div class="slip-value" id="slip-event-date">—</div>
      <div class="slip-label">Venue</div><div class="slip-value" id="slip-event-venue">—</div>
      <div class="slip-label">Attendee</div><div class="slip-value">{{ auth()->check() ? auth()->user()->name : 'Guest' }}</div>
      <hr class="slip-divider"/>
      <div class="slip-ref">
        <div class="slip-ref-label">Booking Reference</div>
        <div class="slip-ref-num" id="slip-ref">VR-000000</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-modal-close" onclick="document.getElementById('slip-modal').classList.remove('open')">Close</button>
      <button class="btn-download" id="btn-download-slip" onclick="downloadSlip()">
        ⬇ Download RSVP Slip (PDF)
      </button>
    </div>
  </div>
</div>

<script>
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function doRsvp(id, name, date, venue) {
    @guest
    if (confirm('You need to be logged in to RSVP. Go to login?')) {
      window.location.href = '/login';
    }
    return;
    @endguest

    const btn = document.getElementById('rsvp-btn-' + id);
    if (btn) { btn.disabled = true; btn.textContent = '...'; }

    fetch('/events/' + id + '/rsvp', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        if (btn) {
          btn.id = 'rsvp-done-' + id;
          btn.className = 'btn-rsvp done';
          btn.disabled = false;
          btn.textContent = '✓ Going';
          btn.onclick = function() { showSlipFromData(id, name, date, venue, data.reference); };
          btn.closest('.event-card').classList.add('rsvped');
        }
        const attEl = document.getElementById('att-' + id);
        if (attEl) attEl.textContent = data.attendee_count + ' attending';
        showSlipFromData(id, name, date, venue, data.reference);
      } else {
        alert(data.message || 'Could not complete RSVP.');
        if (btn) { btn.disabled = false; btn.textContent = 'RSVP Now'; }
      }
    })
    .catch(() => {
      alert('Network error. Please try again.');
      if (btn) { btn.disabled = false; btn.textContent = 'RSVP Now'; }
    });
  }

  let currentSlipEventId = null;

  function showSlipFromData(id, name, date, venue, reference) {
    currentSlipEventId = id;
    document.getElementById('slip-event-name').textContent = name;
    document.getElementById('slip-event-date').textContent = date;
    document.getElementById('slip-event-venue').textContent = venue;
    document.getElementById('slip-ref').textContent = reference || ('EVT-' + id);
    document.getElementById('slip-modal').classList.add('open');
  }

  function downloadSlip() {
    if (!currentSlipEventId) return;
    const btn = document.getElementById('btn-download-slip');
    btn.disabled = true;
    btn.textContent = '⏳ Generating PDF…';

    fetch('/events/' + currentSlipEventId + '/rsvp-slip', {
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
      if (data.success && data.url) {
        window.open(data.url, '_blank');
        btn.textContent = '✓ PDF Ready — Click to Re-download';
      } else {
        alert(data.message || 'Could not generate PDF. Please try again.');
        btn.textContent = '⬇ Download RSVP Slip (PDF)';
      }
      btn.disabled = false;
    })
    .catch(() => {
      alert('Network error. Please try again.');
      btn.disabled = false;
      btn.textContent = '⬇ Download RSVP Slip (PDF)';
    });
  }

  function filterEvents(tab, type) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    document.querySelectorAll('.event-card').forEach(card => {
      card.style.display = (type === 'all' || card.dataset.type === type) ? '' : 'none';
    });
  }

  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('open'); });
  });
</script>
@endsection