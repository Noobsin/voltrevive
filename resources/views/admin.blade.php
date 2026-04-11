@extends('layout')
@section('title', 'Admin Dashboard')

@section('styles')
<style>
  /* ── LAYOUT ── */
  .admin-wrap {
    display: grid; grid-template-columns: 240px 1fr;
    min-height: calc(100vh - 64px);
    position: relative; z-index: 1;
  }

  /* ── SIDEBAR NAV ── */
  .admin-nav {
    background: var(--bg-card); border-right: 1px solid var(--border);
    padding: 1.5rem 0 0; position: sticky; top: 64px;
    height: calc(100vh - 64px); overflow-y: auto;
    display: flex; flex-direction: column;
  }
  .admin-nav-label {
    font-size: 0.65rem; font-weight: 700; letter-spacing: 0.12em;
    text-transform: uppercase; color: var(--muted);
    padding: 0 1.25rem; margin-bottom: 0.5rem; margin-top: 1.25rem;
  }
  .admin-nav-label:first-child { margin-top: 0; }
  .admin-nav-item {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.65rem 1.25rem; font-size: 0.88rem; font-weight: 500;
    color: var(--muted); cursor: pointer; transition: all 0.15s;
    border: none; background: transparent; width: 100%;
    font-family: 'DM Sans', sans-serif; text-align: left;
    border-left: 3px solid transparent;
  }
  .admin-nav-item:hover { color: var(--cream); background: rgba(255,255,255,0.03); }
  .admin-nav-item.active {
    color: var(--amber-lt); background: rgba(212,137,26,0.08);
    border-left-color: var(--amber);
  }
  .nav-badge {
    margin-left: auto; background: var(--amber); color: #161310;
    font-size: 0.65rem; font-weight: 700; padding: 0.1rem 0.45rem;
    border-radius: 20px; min-width: 20px; text-align: center;
  }
  .nav-badge.green { background: #4a9a60; color: #fff; }

  /* ── MAIN CONTENT ── */
  .admin-main { padding: 2rem; overflow: hidden; }

  /* ── SECTION PANELS ── */
  .admin-section { display: none; animation: fadeUp 0.3s ease; }
  .admin-section.active { display: block; }

  /* ── OVERVIEW STATS ── */
  .stats-grid {
    display: grid; grid-template-columns: repeat(5, 1fr);
    gap: 1rem; margin-bottom: 2rem;
  }
  .stat-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 12px; padding: 1.25rem;
  }
  .stat-card-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 0.75rem; }
  .stat-icon { font-size: 1.4rem; }
  .stat-change { font-size: 0.72rem; font-weight: 700; }
  .stat-change.up { color: #5de0b0; }
  .stat-change.warn { color: var(--amber-lt); }
  .stat-num { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 900; color: var(--amber-lt); line-height: 1; margin-bottom: 0.2rem; }
  .stat-lbl { font-size: 0.78rem; color: var(--muted); }

  /* ── SECTION HEADER ── */
  .section-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;
  }
  .section-header h2 { font-size: 1.3rem; }
  .section-header h2 em { font-style: italic; color: var(--amber-lt); }
  .section-header p { font-size: 0.85rem; color: var(--muted); margin-top: 0.2rem; }

  /* ── TABLE ── */
  .admin-table-wrap {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
  }
  .admin-table { width: 100%; border-collapse: collapse; }
  .admin-table thead tr {
    background: var(--bg-card2); border-bottom: 1px solid var(--border);
  }
  .admin-table thead th {
    padding: 0.85rem 1.1rem; text-align: left;
    font-size: 0.7rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--muted); white-space: nowrap;
  }
  .admin-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
  }
  .admin-table tbody tr:last-child { border-bottom: none; }
  .admin-table tbody tr:hover { background: rgba(255,255,255,0.02); }
  .admin-table td { padding: 1rem 1.1rem; font-size: 0.85rem; vertical-align: middle; }
  .td-device { font-weight: 600; }
  .td-sub { font-size: 0.75rem; color: var(--muted); margin-top: 0.15rem; }

  /* BA thumbnail */
  .ba-thumb {
    display: grid; grid-template-columns: 1fr 1fr;
    width: 80px; height: 44px; gap: 2px; border-radius: 6px; overflow: hidden;
  }
  .ba-thumb-img { background-size: cover; background-position: center; }
  .im-sb { background: linear-gradient(135deg,#2a1f0e,#3d2b0f); }
  .im-sa { background: linear-gradient(135deg,#1a2a1a,#233520); }
  .im-rb { background: linear-gradient(135deg,#1e1a2a,#2a2038); }
  .im-ra { background: linear-gradient(135deg,#0e1e2a,#122438); }
  .im-hb { background: linear-gradient(135deg,#201a0a,#30250e); }
  .im-ha { background: linear-gradient(135deg,#0a1a20,#0e2530); }

  /* status pills */
  .pill {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.22rem 0.7rem; border-radius: 20px;
    font-size: 0.72rem; font-weight: 700; white-space: nowrap;
  }
  .pill-pending { background: rgba(212,137,26,0.12); border: 1px solid rgba(212,137,26,0.3); color: var(--amber-lt); }
  .pill-approved { background: rgba(30,160,100,0.1); border: 1px solid rgba(74,200,120,0.3); color: #5de0b0; }
  .pill-rejected { background: rgba(200,60,60,0.1); border: 1px solid rgba(200,60,60,0.3); color: #f09090; }
  .pill-under-review { background: rgba(90,90,212,0.1); border: 1px solid rgba(120,120,230,0.3); color: #a0a0f0; }

  /* action buttons */
  .btn-approve {
    background: rgba(30,160,100,0.15); border: 1px solid rgba(74,200,120,0.35);
    color: #5de0b0; padding: 0.38rem 0.85rem; border-radius: 6px;
    font-size: 0.78rem; font-weight: 700; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: all 0.2s;
  }
  .btn-approve:hover { background: rgba(30,160,100,0.25); }
  .btn-reject {
    background: rgba(200,60,60,0.1); border: 1px solid rgba(200,60,60,0.3);
    color: #f09090; padding: 0.38rem 0.85rem; border-radius: 6px;
    font-size: 0.78rem; font-weight: 700; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: all 0.2s; margin-left: 0.4rem;
  }
  .btn-reject:hover { background: rgba(200,60,60,0.2); }
  .btn-view {
    background: transparent; border: 1px solid var(--border);
    color: var(--muted); padding: 0.38rem 0.85rem; border-radius: 6px;
    font-size: 0.78rem; cursor: pointer; font-family: 'DM Sans', sans-serif;
    transition: all 0.2s; margin-left: 0.4rem;
  }
  .btn-view:hover { border-color: var(--amber); color: var(--amber-lt); }

  /* ── CREATE EVENT FORM ── */
  .event-form-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden; max-width: 680px;
  }
  .event-form-header {
    padding: 1.25rem 1.5rem; background: var(--bg-card2);
    border-bottom: 1px solid var(--border);
    font-family: 'Playfair Display', serif; font-size: 1.1rem;
  }
  .event-form-body { padding: 1.5rem; }
  .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  .form-group { margin-bottom: 1.1rem; }
  .form-label { display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 0.4rem; }
  .form-label .req { color: var(--amber); }
  .form-input, .form-select, .form-textarea {
    width: 100%; background: var(--bg); border: 1px solid var(--border);
    border-radius: 8px; color: var(--cream); font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem; padding: 0.75rem 1rem; outline: none;
    appearance: none; transition: border-color 0.2s;
  }
  .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--amber); }
  .form-input::placeholder, .form-textarea::placeholder { color: var(--muted); }
  .event-form-footer {
    padding: 1.1rem 1.5rem; border-top: 1px solid var(--border);
    background: var(--bg-card2); display: flex; justify-content: flex-end; gap: 0.75rem;
  }
  .btn-cancel-sm {
    background: transparent; border: 1px solid var(--border); color: var(--muted);
    padding: 0.65rem 1.25rem; border-radius: 8px; cursor: pointer;
    font-family: 'DM Sans', sans-serif; font-size: 0.88rem;
  }
  .btn-publish {
    background: var(--amber); border: none; color: #161310;
    padding: 0.65rem 1.5rem; border-radius: 8px; font-weight: 700;
    font-size: 0.88rem; cursor: pointer; font-family: 'DM Sans', sans-serif;
    transition: background 0.2s;
  }
  .btn-publish:hover { background: var(--amber-lt); }

  /* ── REJECTION MODAL ── */
  .modal-backdrop {
    position: fixed; inset: 0; background: rgba(0,0,0,0.65);
    backdrop-filter: blur(4px); z-index: 900;
    display: flex; align-items: center; justify-content: center;
    opacity: 0; pointer-events: none; transition: opacity 0.2s;
  }
  .modal-backdrop.show { opacity: 1; pointer-events: all; }
  .modal-box {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 16px; width: 100%; max-width: 480px;
    box-shadow: 0 24px 64px rgba(0,0,0,0.5);
    transform: translateY(16px); transition: transform 0.25s;
  }
  .modal-backdrop.show .modal-box { transform: translateY(0); }
  .modal-header {
    padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
  }
  .modal-header h3 { font-size: 1rem; font-weight: 700; color: #f09090; }
  .modal-close {
    background: transparent; border: none; color: var(--muted);
    font-size: 1.2rem; cursor: pointer; line-height: 1; padding: 0.2rem;
  }
  .modal-close:hover { color: var(--cream); }
  .modal-body { padding: 1.5rem; }
  .modal-body p { font-size: 0.85rem; color: var(--muted); margin-bottom: 1rem; }
  .modal-textarea {
    width: 100%; background: var(--bg); border: 1px solid var(--border);
    border-radius: 8px; color: var(--cream); font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem; padding: 0.75rem 1rem; outline: none;
    resize: vertical; min-height: 110px; transition: border-color 0.2s;
    box-sizing: border-box;
  }
  .modal-textarea:focus { border-color: #f09090; }
  .modal-textarea::placeholder { color: var(--muted); }
  .modal-footer {
    padding: 1rem 1.5rem; border-top: 1px solid var(--border);
    display: flex; justify-content: flex-end; gap: 0.75rem;
  }
  .btn-modal-cancel {
    background: transparent; border: 1px solid var(--border); color: var(--muted);
    padding: 0.6rem 1.2rem; border-radius: 8px; cursor: pointer;
    font-family: 'DM Sans', sans-serif; font-size: 0.88rem;
  }
  .btn-modal-reject {
    background: rgba(200,60,60,0.15); border: 1px solid rgba(200,60,60,0.4);
    color: #f09090; padding: 0.6rem 1.4rem; border-radius: 8px;
    font-weight: 700; font-size: 0.88rem; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: all 0.2s;
  }
  .btn-modal-reject:hover { background: rgba(200,60,60,0.25); }

  /* ── TOAST ── */
  .toast {
    position: fixed; bottom: 2rem; right: 2rem; z-index: 999;
    background: var(--bg-card); border: 1px solid var(--amber);
    border-radius: 10px; padding: 1rem 1.5rem;
    display: flex; align-items: center; gap: 0.75rem;
    font-size: 0.88rem; box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    transform: translateY(100px); opacity: 0;
    transition: all 0.3s ease; pointer-events: none;
  }
  .toast.show { transform: translateY(0); opacity: 1; }
  .toast-icon { font-size: 1.2rem; }

  @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

  @media(max-width:900px) {
    .admin-wrap { grid-template-columns: 1fr; }
    .admin-nav { position: static; height: auto; display: flex; flex-wrap: wrap; padding: 0.75rem; gap: 0.25rem; border-right: none; border-bottom: 1px solid var(--border); }
    .admin-nav-label { display: none; }
    .admin-nav-item { width: auto; padding: 0.5rem 0.9rem; border-radius: 6px; border-left: none; font-size: 0.8rem; }
    .admin-nav-item.active { background: rgba(212,137,26,0.12); }
    .stats-grid { grid-template-columns: repeat(3,1fr); }
  }
  @media(max-width:600px) {
    .admin-main { padding: 1rem; }
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .form-row-2 { grid-template-columns: 1fr; }
  }
</style>
@endsection

@section('content')
<div class="admin-wrap">

  {{-- ── SIDEBAR NAV ── --}}
  <nav class="admin-nav">
    <div class="admin-nav-label">Overview</div>
    <button class="admin-nav-item active" onclick="showSection('overview', this)">
      📊 &nbsp;Dashboard
    </button>

    <div class="admin-nav-label">Moderation</div>
    <button class="admin-nav-item" onclick="showSection('listings', this)">
      🔍 &nbsp;Service Listings
      <span class="nav-badge" id="badge-listings">{{ $stats['pending_listings'] }}</span>
    </button>
    <button class="admin-nav-item" onclick="showSection('technicians', this)">
      🔧 &nbsp;Active Technicians
    </button>

    <div class="admin-nav-label">Community</div>
    <button class="admin-nav-item" onclick="showSection('events', this)">
      📅 &nbsp;Create Event
    </button>
    <button class="admin-nav-item" onclick="showSection('payments', this)">
      💳 &nbsp;Payments
      <span class="nav-badge green" id="badge-payments">{{ $stats['total_payments'] }}</span>
    </button>

    <div style="padding:1.25rem;border-top:1px solid var(--border);margin-top:auto;">
      <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.85rem;">
        <div style="width:34px;height:34px;border-radius:50%;background:rgba(212,137,26,0.15);border:1px solid rgba(212,137,26,0.3);display:flex;align-items:center;justify-content:center;font-size:0.82rem;font-weight:700;color:var(--amber);flex-shrink:0;">A</div>
        <div style="min-width:0;">
          <div style="font-size:0.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Admin</div>
          <div style="font-size:0.72rem;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">admin@voltrevive.com</div>
        </div>
      </div>
      <button onclick="confirmLogout()"
        style="width:100%;background:rgba(200,60,60,0.08);border:1px solid rgba(200,60,60,0.25);color:#f09090;padding:0.6rem;border-radius:8px;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:0.5rem;"
        onmouseover="this.style.background='rgba(200,60,60,0.15)'" onmouseout="this.style.background='rgba(200,60,60,0.08)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sign Out
      </button>
    </div>
  </nav>

  {{-- ── MAIN CONTENT ── --}}
  <main class="admin-main">

    {{-- ══════════════════════════════ --}}
    {{-- SECTION 1: OVERVIEW           --}}
    {{-- ══════════════════════════════ --}}
    <div class="admin-section active" id="section-overview">
      <div class="section-header">
        <div>
          <h2>Platform <em>Overview</em></h2>
          <p>Welcome back. Here's what needs your attention today.</p>
        </div>
        <div style="font-size:0.8rem;color:var(--muted);">
          📅 {{ now()->format('D, M j, Y') }}
        </div>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-icon">🔍</span>
            <span class="stat-change warn">{{ $stats['pending_listings'] }} pending</span>
          </div>
          <div class="stat-num">{{ $stats['pending_listings'] }}</div>
          <div class="stat-lbl">Listings Awaiting Approval</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-icon">🔧</span>
            <span class="stat-change up">Active</span>
          </div>
          <div class="stat-num">{{ $stats['total_technicians'] }}</div>
          <div class="stat-lbl">Active Technicians</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-icon">⚡</span>
            <span class="stat-change up">This Month</span>
          </div>
          <div class="stat-num">{{ $stats['jobs_completed_month'] }}</div>
          <div class="stat-lbl">Jobs Completed This Month</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-icon">👥</span>
            <span class="stat-change up">Total</span>
          </div>
          <div class="stat-num">{{ $stats['total_users'] }}</div>
          <div class="stat-lbl">Registered Users</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-top">
            <span class="stat-icon">💳</span>
            <span class="stat-change up">Received</span>
          </div>
          <div class="stat-num">{{ $stats['total_payments'] }}</div>
          <div class="stat-lbl">Payments Processed</div>
        </div>
      </div>

      {{-- QUICK ACTIONS --}}
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:14px;padding:1.25rem;margin-top:0.5rem;">
        <div style="font-size:0.72rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:1rem;">Quick Actions</div>
        <div style="display:flex;flex-wrap:wrap;gap:0.75rem;">
          <button onclick="showSection('listings',document.querySelector('[onclick*=listings]'))" style="background:rgba(212,137,26,0.1);border:1px solid rgba(212,137,26,0.25);color:var(--amber-lt);padding:0.6rem 1.2rem;border-radius:8px;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;" onmouseover="this.style.background='rgba(212,137,26,0.18)'" onmouseout="this.style.background='rgba(212,137,26,0.1)'">
            🔍 Review {{ $stats['pending_listings'] }} Pending Listings
          </button>
          <button onclick="showSection('technicians',document.querySelector('[onclick*=technicians]'))" style="background:rgba(212,137,26,0.1);border:1px solid rgba(212,137,26,0.25);color:var(--amber-lt);padding:0.6rem 1.2rem;border-radius:8px;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;" onmouseover="this.style.background='rgba(212,137,26,0.18)'" onmouseout="this.style.background='rgba(212,137,26,0.1)'">
            🔧 View Active Technicians
          </button>
          <button onclick="showSection('events',document.querySelector('[onclick*=events]'))" style="background:rgba(212,137,26,0.1);border:1px solid rgba(212,137,26,0.25);color:var(--amber-lt);padding:0.6rem 1.2rem;border-radius:8px;font-size:0.85rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all 0.2s;" onmouseover="this.style.background='rgba(212,137,26,0.18)'" onmouseout="this.style.background='rgba(212,137,26,0.1)'">
            📅 Create New Event
          </button>
        </div>
      </div>
    </div>

    {{-- ══════════════════════════════ --}}
    {{-- SECTION 2: SERVICE LISTINGS   --}}
    {{-- ══════════════════════════════ --}}
    <div class="admin-section" id="section-listings">
      <div class="section-header">
        <div>
          <h2>Pending Service <em>Listings</em></h2>
          <p>Review Before & After images before approving. Reject with a reason if proof is insufficient.</p>
        </div>
        <span class="pill pill-pending">{{ $stats['pending_listings'] }} awaiting review</span>
      </div>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Portfolio Proof</th>
              <th>Service Title</th>
              <th>Technician</th>
              <th>Category</th>
              <th>Price Range</th>
              <th>Submitted</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="listings-tbody">
            @forelse($pendingListings as $listing)
            <tr id="listing-row-{{ $listing->id }}">
              <td>
                <div class="ba-thumb">
                  <div class="ba-thumb-img" style="background-image:url('{{ asset('storage/' . $listing->before_image) }}');background-size:cover;background-position:center;"></div>
                  <div class="ba-thumb-img" style="background-image:url('{{ asset('storage/' . $listing->after_image) }}');background-size:cover;background-position:center;"></div>
                </div>
              </td>
              <td>
                <div class="td-device">{{ $listing->title }}</div>
                <div class="td-sub">Models: {{ implode(', ', $listing->supported_models ?? []) }}</div>
              </td>
              <td>
                <div style="font-weight:600;">{{ $listing->technicianProfile->user->name ?? '—' }}</div>
                <div class="td-sub">{{ $listing->technicianProfile->location ?? 'Location not set' }}</div>
              </td>
              <td>{{ $listing->category }}</td>
              <td>${{ number_format($listing->price_min, 0) }} – ${{ number_format($listing->price_max, 0) }}</td>
              <td><div class="td-sub">{{ $listing->created_at->diffForHumans() }}</div></td>
              <td><span class="pill pill-pending" id="pill-{{ $listing->id }}">Pending</span></td>
              <td>
                <button class="btn-approve" onclick="approveListing({{ $listing->id }})">✓ Approve</button>
                <button class="btn-reject"  onclick="rejectListing({{ $listing->id }})">✕ Reject</button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" style="text-align:center;padding:2rem;color:var(--muted);">
                No pending listings — you're all caught up!
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- ══════════════════════════════════ --}}
    {{-- SECTION 3: TECHNICIAN APPLICATIONS --}}
    {{-- ══════════════════════════════ --}}
    {{-- SECTION: ACTIVE TECHNICIANS   --}}
    {{-- ══════════════════════════════ --}}
    <div class="admin-section" id="section-technicians">
      <div class="section-header">
        <div>
          <h2>Active <em>Technicians</em></h2>
          <p>All verified technicians currently active on the platform.</p>
        </div>
        <span class="pill pill-approved">{{ $stats['total_technicians'] }} active</span>
      </div>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Technician</th>
              <th>Specialisation</th>
              <th>Location</th>
              <th>Rating</th>
              <th>Jobs Done</th>
              <th>Joined</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($technicians as $profile)
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:0.6rem;">
                  <div style="width:32px;height:32px;border-radius:50%;background:rgba(93,224,176,0.12);border:1px solid rgba(93,224,176,0.25);display:flex;align-items:center;justify-content:center;font-weight:700;color:#5de0b0;font-size:0.85rem;flex-shrink:0;">
                    {{ strtoupper(substr($profile->user->name ?? '?', 0, 1)) }}
                  </div>
                  <div>
                    <div style="font-weight:600;">{{ $profile->user->name ?? '—' }}</div>
                    <div class="td-sub">{{ $profile->user->email ?? '' }}</div>
                  </div>
                </div>
              </td>
              <td><div class="td-device">{{ $profile->specialisation }}</div></td>
              <td>{{ $profile->location ?? '—' }}</td>
              <td style="color:var(--amber-lt);font-weight:700;">★ {{ number_format($profile->avg_rating, 1) }}</td>
              <td>{{ $profile->completed_jobs_count }}</td>
              <td><div class="td-sub">{{ $profile->created_at->format('M Y') }}</div></td>
              <td><span class="pill pill-approved">Active</span></td>
            </tr>
            @empty
            <tr>
              <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted);">No technicians registered yet.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- ══════════════════════════════ --}}
    {{-- SECTION: CREATE EVENT         --}}
    {{-- ══════════════════════════════ --}}
    <div class="admin-section" id="section-events">
      <div class="section-header">
        <div>
          <h2>Create <em>Community Event</em></h2>
          <p>Events are published immediately to the public Community Event Board.</p>
        </div>
      </div>

      <div class="event-form-card">
        <div class="event-form-header">📅 New Event Details</div>
        <div class="event-form-body">
          <form id="event-form">
            <div class="form-group">
              <label class="form-label">Event Name <span class="req">*</span></label>
              <input type="text" class="form-input" name="title" placeholder="e.g. Berlin Vintage Synthesizer Swap Meet 2026" required/>
            </div>
            <div class="form-row-2">
              <div class="form-group">
                <label class="form-label">Event Type <span class="req">*</span></label>
                <select class="form-select" name="event_type" required>
                  <option value="" disabled selected>Select type…</option>
                  <option>Swap Meet</option>
                  <option>Repair Café</option>
                  <option>Exhibition</option>
                  <option>Workshop</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Date <span class="req">*</span></label>
                <input type="date" class="form-input" name="event_date" required/>
              </div>
            </div>
            <div class="form-row-2">
              <div class="form-group">
                <label class="form-label">Start Time</label>
                <input type="time" class="form-input" name="start_time"/>
              </div>
              <div class="form-group">
                <label class="form-label">End Time</label>
                <input type="time" class="form-input" name="end_time"/>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Venue / Location <span class="req">*</span></label>
              <input type="text" class="form-input" name="location" placeholder="e.g. Tempodrom, Berlin, Germany" required/>
            </div>
            <div class="form-group">
              <label class="form-label">Maximum Tickets <span class="req">*</span></label>
              <input type="number" class="form-input" name="ticket_count" placeholder="100" min="1" required style="max-width:200px;"/>
            </div>
            <div class="form-group">
              <label class="form-label">Description</label>
              <textarea class="form-textarea" rows="3" name="description" placeholder="What's happening, who should attend, what to bring…"></textarea>
            </div>
            <div class="event-form-footer">
              <button type="button" class="btn-cancel-sm" onclick="document.getElementById('event-form').reset()">Clear Form</button>
              <button type="submit" class="btn-publish">📅 Publish to Event Board</button>
            </div>
          </form>
        </div>
      </div>
    </div>


    {{-- ══════════════════════════════ --}}
    {{-- SECTION: PAYMENTS             --}}
    {{-- ══════════════════════════════ --}}
    <div class="admin-section" id="section-payments">
      <div class="section-header">
        <div>
          <h2>Payment <em>Records</em></h2>
          <p>All completed payments processed through the platform.</p>
        </div>
        <div style="background:var(--bg-card);border:1px solid rgba(93,224,176,0.3);border-radius:10px;padding:0.75rem 1.25rem;text-align:center;">
          <div style="font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:900;color:#5de0b0;line-height:1;">
            ৳{{ number_format($stats['total_revenue'], 0) }}
          </div>
          <div style="font-size:0.72rem;color:var(--muted);margin-top:0.2rem;text-transform:uppercase;letter-spacing:0.08em;">Total Revenue</div>
        </div>
      </div>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Job Reference</th>
              <th>Collector</th>
              <th>Technician</th>
              <th>Amount</th>
              <th>Card</th>
              <th>Date Paid</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $payment)
            <tr>
              <td>
                <div style="font-weight:600;">{{ $payment->job->reference ?? '—' }}</div>
                <div class="td-sub">{{ $payment->job->booking->device_name ?? '' }}</div>
              </td>
              <td>
                <div style="font-weight:600;">{{ $payment->collector->name ?? '—' }}</div>
                <div class="td-sub">{{ $payment->collector->email ?? '' }}</div>
              </td>
              <td>
                {{ $payment->job->booking->technicianProfile->user->name ?? '—' }}
              </td>
              <td style="color:var(--amber-lt);font-weight:700;">
                ৳{{ number_format($payment->amount, 0) }}
              </td>
              <td>
                <div style="font-family:monospace;letter-spacing:0.1em;color:var(--muted);">
                  •••• •••• •••• {{ $payment->card_last_four }}
                </div>
                <div class="td-sub">{{ $payment->cardholder_name }}</div>
              </td>
              <td>
                <div style="font-size:0.85rem;">{{ $payment->paid_at?->format('M j, Y') ?? '—' }}</div>
                <div class="td-sub">{{ $payment->paid_at?->format('g:i A') ?? '' }}</div>
              </td>
              <td><span class="pill pill-approved">✓ Paid</span></td>
            </tr>
            @empty
            <tr>
              <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted);">
                No payments received yet.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>


  </main>
</div>

{{-- LOGOUT CONFIRMATION MODAL --}}
<div id="logout-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:999;align-items:center;justify-content:center;">
  <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:16px;padding:2rem;max-width:380px;width:90%;animation:fadeUp 0.25s ease;text-align:center;">
    <div style="width:52px;height:52px;border-radius:50%;background:rgba(200,60,60,0.1);border:1px solid rgba(200,60,60,0.25);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
      <svg width="22" height="22" fill="none" stroke="#f09090" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </div>
    <h2 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:0.5rem;">Sign Out?</h2>
    <p style="font-size:0.85rem;color:var(--muted);line-height:1.6;margin-bottom:1.5rem;">You will be returned to the admin login page. Any unsaved changes will be lost.</p>
    <div style="display:flex;gap:0.75rem;">
      <button onclick="closeLogout()"
        style="flex:1;background:transparent;border:1px solid var(--border);color:var(--muted);padding:0.7rem;border-radius:8px;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.88rem;transition:all 0.2s;"
        onmouseover="this.style.borderColor='var(--amber)';this.style.color='var(--amber-lt)'"
        onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
        Stay
      </button>
      <button onclick="doLogout()"
        style="flex:1;background:rgba(200,60,60,0.12);border:1px solid rgba(200,60,60,0.3);color:#f09090;padding:0.7rem;border-radius:8px;cursor:pointer;font-family:'DM Sans',sans-serif;font-size:0.88rem;font-weight:700;transition:all 0.2s;"
        onmouseover="this.style.background='rgba(200,60,60,0.22)'" onmouseout="this.style.background='rgba(200,60,60,0.12)'">
        Sign Out
      </button>
    </div>
  </div>
</div>

{{-- TOAST NOTIFICATION --}}
{{-- ── REJECTION REASON MODAL ── --}}
<div class="modal-backdrop" id="reject-modal">
  <div class="modal-box">
    <div class="modal-header">
      <h3>✕ Reject Listing</h3>
      <button class="modal-close" onclick="closeRejectModal()">✕</button>
    </div>
    <div class="modal-body">
      <p>Provide a reason for rejection. This will be displayed to the technician on their dashboard.</p>
      <textarea class="modal-textarea" id="reject-reason" placeholder="e.g. Before/After images are unclear or unrelated to the listed service…" maxlength="500"></textarea>
      <div style="font-size:0.72rem;color:var(--muted);text-align:right;margin-top:0.3rem;">
        <span id="reason-count">0</span>/500
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-modal-cancel" onclick="closeRejectModal()">Cancel</button>
      <button class="btn-modal-reject" id="btn-confirm-reject" onclick="confirmReject()">
        ✕ Confirm Rejection
      </button>
    </div>
  </div>
</div>

<div class="toast" id="toast">
  <span class="toast-icon" id="toast-icon">✓</span>
  <span id="toast-msg">Action completed.</span>
</div>

<script>
  // ── NAVIGATION ──
  function showSection(name, btn) {
    document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.admin-nav-item').forEach(b => b.classList.remove('active'));
    document.getElementById('section-' + name).classList.add('active');
    if (btn) btn.classList.add('active');
  }

  // ── TOAST ──
  function showToast(msg, icon='✓') {
    const t = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    document.getElementById('toast-icon').textContent = icon;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
  }

  // ── LISTINGS (real AJAX) ──
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function approveListing(id) {
    fetch('/admin/listings/' + id + '/approve', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const row = document.getElementById('listing-row-' + id);
        document.getElementById('pill-' + id).className = 'pill pill-approved';
        document.getElementById('pill-' + id).textContent = '\u2713 Approved';
        row.querySelector('.btn-approve').remove();
        row.querySelector('.btn-reject').remove();
        const badge = document.getElementById('badge-listings');
        badge.textContent = Math.max(0, parseInt(badge.textContent) - 1);
        showToast('Listing approved — now live in Browse.', '\u2713');
      }
    })
    .catch(() => showToast('Error — please try again.', '\u2715'));
  }

  let _rejectListingId = null;

  function rejectListing(id) {
    _rejectListingId = id;
    document.getElementById('reject-reason').value = '';
    document.getElementById('reason-count').textContent = '0';
    document.getElementById('reject-modal').classList.add('show');
    setTimeout(() => document.getElementById('reject-reason').focus(), 200);
  }

  function closeRejectModal() {
    document.getElementById('reject-modal').classList.remove('show');
    _rejectListingId = null;
  }

  function confirmReject() {
    const reason = document.getElementById('reject-reason').value.trim();
    if (!reason) {
      document.getElementById('reject-reason').style.borderColor = '#f09090';
      return;
    }
    const btn = document.getElementById('btn-confirm-reject');
    btn.disabled = true; btn.textContent = 'Rejecting…';

    fetch('/admin/listings/' + _rejectListingId + '/reject', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({ reason: reason }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const row = document.getElementById('listing-row-' + _rejectListingId);
        document.getElementById('pill-' + _rejectListingId).className = 'pill pill-rejected';
        document.getElementById('pill-' + _rejectListingId).textContent = '\u2715 Rejected';
        row.querySelector('.btn-approve').remove();
        row.querySelector('.btn-reject').remove();
        const badge = document.getElementById('badge-listings');
        badge.textContent = Math.max(0, parseInt(badge.textContent) - 1);
        closeRejectModal();
        showToast('Listing rejected. Reason saved to technician dashboard.', '\u2715');
      } else {
        showToast('Error — please try again.', '\u2715');
      }
      btn.disabled = false; btn.textContent = '\u2715 Confirm Rejection';
    })
    .catch(() => {
      showToast('Network error — please try again.', '\u2715');
      btn.disabled = false; btn.textContent = '\u2715 Confirm Rejection';
    });
  }

  // Close modal on backdrop click
  document.getElementById('reject-modal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
  });

  // Character counter for textarea
  document.getElementById('reject-reason').addEventListener('input', function() {
    document.getElementById('reason-count').textContent = this.value.length;
    this.style.borderColor = '';
  });

  // ── APPLICATIONS (removed) ──
  function approveApp(id) {
    const row = document.getElementById('app-row-' + id);
    row.querySelector('.pill.pill-under-review').className = 'pill pill-approved';
    row.querySelector('.pill.pill-approved').textContent = '✓ Approved';
    row.querySelector('.btn-approve').remove();
    row.querySelector('.btn-reject').remove();
    appCount = Math.max(0, appCount - 1);
    document.getElementById('badge-apps').textContent = appCount;
    showToast('Technician role activated. User has been notified.', '⚡');
  }
  function rejectApp(id) {
    const reason = prompt('Rejection reason:');
    if (reason === null) return;
    const row = document.getElementById('app-row-' + id);
    row.querySelector('.pill.pill-under-review').className = 'pill pill-rejected';
    row.querySelector('.pill.pill-rejected').textContent = '✕ Rejected';
    row.querySelector('.btn-approve').remove();
    row.querySelector('.btn-reject').remove();
    appCount = Math.max(0, appCount - 1);
    document.getElementById('badge-apps').textContent = appCount;
    showToast('Application rejected. Applicant has been notified.', '✕');
  }

  // ── LOGOUT ──
  function confirmLogout() {
    const modal = document.getElementById('logout-modal');
    modal.style.display = 'flex';
  }
  function closeLogout() {
    document.getElementById('logout-modal').style.display = 'none';
  }
  function doLogout() {
    document.getElementById('logout-form').submit();
  }
  // Close modal on backdrop click
  document.getElementById('logout-modal').addEventListener('click', function(e) {
    if (e.target === this) closeLogout();
  });
  // Close modal on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLogout();
  });

  // ── CREATE EVENT (real AJAX) ──
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('event-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());

      // Combine date + start_time into a full datetime string (e.g. "2026-06-15 10:00:00")
      const dateVal      = data.event_date  || '';
      const startTimeVal = data.start_time  || '00:00';
      if (dateVal) {
        data.event_date = dateVal + ' ' + startTimeVal + ':00';
      }
      // Remove separate time fields — not DB columns
      delete data.start_time;
      delete data.end_time;

      fetch('/admin/events', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(data),
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          showToast('Event published to Community Event Board!', '\u{1F4C5}');
          document.getElementById('event-form').reset();
        } else {
          showToast(res.message || 'Error publishing event.', '\u2715');
        }
      })
      .catch(() => showToast('Network error — please try again.', '\u2715'));
    });
  });
</script>
@endsection