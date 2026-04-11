{{--
  RESTORATION TIMELINE COMPONENT
  Usage: @include('components.restoration-timeline', ['status' => 'repair_started'])

  Possible status values:
    booking_confirmed | diagnostic_done | device_shipped |
    repair_started | parts_ordered | restoration_completed | returned

  This partial is included inside the Job Detail Page.
--}}

<div class="timeline-panel">
  <div class="timeline-header">
    <div class="timeline-title">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Restoration Timeline
    </div>
    <div class="timeline-job-id">Job #VR-2026-0041</div>
  </div>

  <div class="timeline-track">

    {{-- STEP 1: BOOKING CONFIRMED --}}
    <div class="tl-step done" id="tl-booking">
      <div class="tl-node-col">
        <div class="tl-node done">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="tl-line"></div>
      </div>
      <div class="tl-content">
        <div class="tl-label">Booking Confirmed</div>
        <div class="tl-desc">Technician accepted the request and payment held in escrow.</div>
        <div class="tl-timestamp">Mar 10, 2026 · 14:32</div>
      </div>
    </div>

    {{-- STEP 2: DIAGNOSTIC SESSION --}}
    <div class="tl-step done" id="tl-diagnostic">
      <div class="tl-node-col">
        <div class="tl-node done">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="tl-line"></div>
      </div>
      <div class="tl-content">
        <div class="tl-label">Diagnostic Session Completed</div>
        <div class="tl-desc">Live video session held via Jitsi. Fault condition verified before shipping.</div>
        <div class="tl-timestamp">Mar 12, 2026 · 10:15</div>
      </div>
    </div>

    {{-- STEP 3: DEVICE SHIPPED --}}
    <div class="tl-step done" id="tl-shipped">
      <div class="tl-node-col">
        <div class="tl-node done">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="tl-line"></div>
      </div>
      <div class="tl-content">
        <div class="tl-label">Device Shipped</div>
        <div class="tl-desc">Collector shipped the device. Tracking: DHL Express #1234567890</div>
        <div class="tl-timestamp">Mar 14, 2026 · 09:00</div>
      </div>
    </div>

    {{-- STEP 4: REPAIR STARTED -- ACTIVE --}}
    <div class="tl-step active" id="tl-repair">
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
        <div class="tl-desc">Technician has received the device and begun diagnostics on the workbench.</div>
        <div class="tl-timestamp">Mar 18, 2026 · 11:45</div>
      </div>
    </div>

    {{-- STEP 5: PARTS ORDERED (OPTIONAL) --}}
    <div class="tl-step pending optional" id="tl-parts">
      <div class="tl-node-col">
        <div class="tl-node pending optional">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
        </div>
        <div class="tl-line"></div>
      </div>
      <div class="tl-content">
        <div class="tl-label">
          Parts Ordered
          <span class="tl-optional-badge">Optional</span>
        </div>
        <div class="tl-desc">If specialist components are needed, the technician will log them here.</div>
        <div class="tl-timestamp tl-pending-text">Awaiting…</div>
      </div>
    </div>

    {{-- STEP 6: RESTORATION COMPLETED --}}
    <div class="tl-step pending" id="tl-completed">
      <div class="tl-node-col">
        <div class="tl-node pending"></div>
        <div class="tl-line"></div>
      </div>
      <div class="tl-content">
        <div class="tl-label">Restoration Completed</div>
        <div class="tl-desc">Technician marks job done. Payment released from escrow to technician.</div>
        <div class="tl-timestamp tl-pending-text">Awaiting…</div>
      </div>
    </div>

    {{-- STEP 7: RETURNED TO COLLECTOR --}}
    <div class="tl-step pending last" id="tl-returned">
      <div class="tl-node-col">
        <div class="tl-node pending"></div>
        {{-- no line after last step --}}
      </div>
      <div class="tl-content">
        <div class="tl-label">Returned to Collector</div>
        <div class="tl-desc">Device shipped back. Collector receives and submits their review.</div>
        <div class="tl-timestamp tl-pending-text">Awaiting…</div>
      </div>
    </div>

  </div>{{-- end timeline-track --}}

  <div class="timeline-footer">
    <div class="tl-progress-wrap">
      <div class="tl-progress-bar">
        <div class="tl-progress-fill" style="width: 57%"></div>
      </div>
      <div class="tl-progress-label">4 of 7 stages complete</div>
    </div>
    <div class="tl-est">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Est. completion: <strong>Apr 1, 2026</strong>
    </div>
  </div>
</div>

<style>
  .timeline-panel {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
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

  /* each step row */
  .tl-step {
    display: grid; grid-template-columns: 32px 1fr;
    gap: 0 1rem; margin-bottom: 0;
    opacity: 1; transition: opacity 0.2s;
  }
  .tl-step.pending { opacity: 0.45; }
  .tl-step.optional { opacity: 0.55; }

  /* left column: node + vertical line */
  .tl-node-col {
    display: flex; flex-direction: column; align-items: center;
  }
  .tl-node {
    width: 32px; height: 32px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; position: relative; z-index: 1;
  }
  .tl-node.done {
    background: var(--amber); border: 2px solid var(--amber);
    color: #161310;
  }
  .tl-node.active {
    background: rgba(212,137,26,0.15); border: 2px solid var(--amber);
    color: var(--amber);
  }
  .tl-node.pending {
    background: var(--bg-card2); border: 2px solid var(--border);
    color: var(--muted);
  }
  .tl-node.optional {
    background: var(--bg-card2); border: 2px dashed var(--border);
    color: var(--muted);
  }
  /* pulsing dot for active */
  .tl-pulse {
    width: 10px; height: 10px; border-radius: 50%;
    background: var(--amber); position: relative;
  }
  .tl-pulse::after {
    content: ''; position: absolute; inset: -5px;
    border-radius: 50%; border: 2px solid var(--amber);
    animation: ripple 1.5s ease-out infinite;
  }
  @keyframes ripple {
    0% { opacity: 1; transform: scale(1); }
    100% { opacity: 0; transform: scale(2.2); }
  }

  /* vertical connector line */
  .tl-line {
    flex: 1; width: 2px; background: var(--border);
    margin: 4px 0; min-height: 24px;
  }
  .tl-step.done .tl-line { background: var(--amber); }
  .tl-step.active .tl-line {
    background: linear-gradient(to bottom, var(--amber), var(--border));
  }

  /* right column: content */
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
  @keyframes pulse-badge { 0%,100%{opacity:1} 50%{opacity:0.6} }
  .tl-optional-badge {
    background: var(--bg-card2); border: 1px dashed var(--border);
    color: var(--muted); font-size: 0.62rem; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    padding: 0.1rem 0.45rem; border-radius: 20px;
  }
  .tl-desc { font-size: 0.8rem; color: var(--muted); line-height: 1.55; margin-bottom: 0.3rem; }
  .tl-timestamp { font-size: 0.72rem; color: var(--muted); }
  .tl-step.done .tl-timestamp { color: var(--amber-lt); }
  .tl-pending-text { color: var(--muted) !important; font-style: italic; }

  /* footer */
  .timeline-footer {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.9rem 1.25rem; border-top: 1px solid var(--border);
    background: var(--bg-card2); gap: 1rem; flex-wrap: wrap;
  }
  .tl-progress-wrap { flex: 1; min-width: 160px; }
  .tl-progress-bar {
    height: 5px; background: var(--border); border-radius: 3px;
    overflow: hidden; margin-bottom: 0.35rem;
  }
  .tl-progress-fill { height: 100%; background: var(--amber); border-radius: 3px; }
  .tl-progress-label { font-size: 0.72rem; color: var(--muted); }
  .tl-est {
    display: flex; align-items: center; gap: 0.35rem;
    font-size: 0.78rem; color: var(--muted); flex-shrink: 0;
  }
  .tl-est strong { color: var(--cream); }
</style>