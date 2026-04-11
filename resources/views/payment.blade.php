@extends('layout')
@section('title', 'Complete Payment — Job #{{ $job->reference }}')

@section('styles')
<style>
  .pay-wrap {
    max-width: 960px; margin: 0 auto;
    padding: 2.5rem 2rem 5rem;
    display: grid; grid-template-columns: 1fr 380px;
    gap: 2rem; position: relative; z-index: 1;
  }
  .pay-breadcrumb {
    max-width: 960px; margin: 0 auto;
    padding: 1.5rem 2rem 0; position: relative; z-index: 1;
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.82rem; color: var(--muted);
  }
  .pay-breadcrumb a { color: var(--muted); text-decoration: none; transition: color 0.2s; }
  .pay-breadcrumb a:hover { color: var(--cream); }
  .pay-breadcrumb span { color: var(--border); }

  /* ── FORM CARD ── */
  .form-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
  }
  .form-card-header {
    padding: 1.25rem 1.5rem; background: var(--bg-card2);
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 0.75rem;
  }
  .form-card-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 1.15rem; margin: 0;
  }
  .secure-badge {
    margin-left: auto; display: flex; align-items: center; gap: 0.4rem;
    font-size: 0.72rem; color: #5de0b0;
    background: rgba(93,224,176,0.08); border: 1px solid rgba(93,224,176,0.2);
    padding: 0.25rem 0.65rem; border-radius: 20px;
  }
  .form-card-body { padding: 1.75rem 1.5rem; }
  .form-group { margin-bottom: 1.25rem; }
  .form-label {
    display: block; font-size: 0.8rem; font-weight: 600;
    margin-bottom: 0.45rem; color: var(--cream);
  }
  .form-label .req { color: var(--amber); }
  .form-input {
    width: 100%; background: var(--bg); border: 1px solid var(--border);
    border-radius: 8px; color: var(--cream); font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem; padding: 0.75rem 1rem; outline: none;
    transition: border-color 0.2s; box-sizing: border-box;
    letter-spacing: 0.02em;
  }
  .form-input:focus { border-color: var(--amber); }
  .form-input::placeholder { color: var(--muted); letter-spacing: normal; }
  .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

  /* card number visual */
  .card-number-wrap { position: relative; }
  .card-network-icon {
    position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
    font-size: 1.1rem; pointer-events: none;
  }
  #card-number-input { padding-right: 3rem; font-family: 'DM Mono', monospace, 'DM Sans', sans-serif; letter-spacing: 0.15em; }

  /* ── SUBMIT BUTTON ── */
  .btn-pay {
    width: 100%; background: var(--amber); border: none; color: #161310;
    padding: 0.9rem; border-radius: 10px; font-weight: 700; font-size: 1rem;
    cursor: pointer; font-family: 'DM Sans', sans-serif;
    transition: background 0.2s; display: flex; align-items: center;
    justify-content: center; gap: 0.6rem; margin-top: 0.5rem;
  }
  .btn-pay:hover { background: var(--amber-lt); }
  .btn-pay:disabled { opacity: 0.5; cursor: not-allowed; }
  .pay-note {
    font-size: 0.72rem; color: var(--muted); text-align: center;
    margin-top: 0.85rem; line-height: 1.6;
  }
  .pay-note a { color: var(--amber-lt); text-decoration: none; }

  /* ── ORDER SUMMARY ── */
  .summary-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden; align-self: start;
    position: sticky; top: 80px;
  }
  .summary-header {
    padding: 1.1rem 1.25rem; background: var(--bg-card2);
    border-bottom: 1px solid var(--border);
    font-size: 0.72rem; font-weight: 700; letter-spacing: 0.1em;
    text-transform: uppercase; color: var(--muted);
  }
  .summary-body { padding: 1.25rem; }
  .summary-row {
    display: flex; justify-content: space-between; align-items: baseline;
    padding: 0.6rem 0; border-bottom: 1px solid var(--border);
    font-size: 0.85rem; gap: 0.75rem;
  }
  .summary-row:last-child { border-bottom: none; }
  .summary-row .lbl { color: var(--muted); }
  .summary-row .val { color: var(--cream); font-weight: 500; text-align: right; }
  .summary-total {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 1rem; padding: 1rem; background: rgba(212,137,26,0.06);
    border: 1px solid rgba(212,137,26,0.2); border-radius: 10px;
  }
  .summary-total .lbl { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); }
  .summary-total .amount {
    font-family: 'Playfair Display', serif; font-size: 1.8rem;
    font-weight: 900; color: var(--amber-lt); line-height: 1;
  }
  .summary-total .currency { font-size: 0.78rem; color: var(--muted); margin-top: 0.2rem; text-align: right; }

  .tech-info-box {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 1rem; background: var(--bg-card2); border-radius: 10px;
    margin-top: 1.25rem; border: 1px solid var(--border);
  }
  .tech-avatar-lg {
    width: 42px; height: 42px; border-radius: 50%;
    background: rgba(212,137,26,0.15); border: 2px solid rgba(212,137,26,0.3);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1rem; color: var(--amber); flex-shrink: 0;
  }
  .tech-info-box .name { font-size: 0.88rem; font-weight: 600; }
  .tech-info-box .role { font-size: 0.72rem; color: var(--muted); }

  @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:800px) { .pay-wrap { grid-template-columns: 1fr; } .summary-card { position: static; } }
  @media(max-width:600px) { .pay-breadcrumb, .pay-wrap { padding-left: 1rem; padding-right: 1rem; } }
</style>
@endsection

@section('content')

<div class="pay-breadcrumb">
  <a href="/dashboard">Dashboard</a>
  <span>›</span>
  <a href="/jobs/{{ $job->id }}">Job #{{ $job->reference }}</a>
  <span>›</span>
  Complete Payment
</div>

<div class="pay-wrap">

  {{-- ── LEFT: PAYMENT FORM ── --}}
  <div style="animation: fadeUp 0.4s ease;">
    <div class="form-card">
      <div class="form-card-header">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
        </svg>
        <h2>Card Payment</h2>
        <div class="secure-badge">
          <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
          Secure
        </div>
      </div>

      <div class="form-card-body">
        @if ($errors->any())
          <div style="background:rgba(200,60,60,0.08);border:1px solid rgba(200,60,60,0.25);border-radius:8px;padding:0.85rem 1rem;margin-bottom:1.25rem;font-size:0.85rem;color:#f09090;">
            @foreach ($errors->all() as $error)
              <div>⚠ {{ $error }}</div>
            @endforeach
          </div>
        @endif

        <form method="POST" action="/jobs/{{ $job->id }}/pay" id="payment-form">
          @csrf

          <div class="form-group">
            <label class="form-label">Cardholder Name <span class="req">*</span></label>
            <input type="text" class="form-input" name="cardholder_name"
              placeholder="As shown on your card"
              value="{{ old('cardholder_name') }}" required autocomplete="cc-name"/>
          </div>

          <div class="form-group">
            <label class="form-label">Card Number <span class="req">*</span></label>
            <div class="card-number-wrap">
              <input type="text" class="form-input" id="card-number-input"
                name="card_number" placeholder="0000 0000 0000 0000"
                maxlength="19" required autocomplete="cc-number"
                oninput="formatCardNumber(this)" value="{{ old('card_number') }}"/>
              <span class="card-network-icon" id="card-icon">💳</span>
            </div>
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label class="form-label">Expiry Date <span class="req">*</span></label>
              <input type="text" class="form-input" name="expiry"
                placeholder="MM/YY" maxlength="5" required autocomplete="cc-exp"
                oninput="formatExpiry(this)" value="{{ old('expiry') }}"/>
            </div>
            <div class="form-group">
              <label class="form-label">CVV <span class="req">*</span></label>
              <input type="password" class="form-input" name="cvv"
                placeholder="•••" maxlength="4" required autocomplete="cc-csc"/>
            </div>
          </div>

          <button type="submit" class="btn-pay" id="pay-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
            </svg>
            Pay ৳{{ number_format($job->payment_amount, 0) }} Now
          </button>
        </form>

        <p class="pay-note">
          🔒 Your card details are processed securely and never stored in full.<br>
          By paying you agree to VoltRevive's <a href="#">Terms of Service</a>.
        </p>
      </div>
    </div>

    {{-- Back link --}}
    <div style="margin-top: 1rem;">
      <a href="/jobs/{{ $job->id }}" style="font-size:0.82rem;color:var(--muted);text-decoration:none;display:inline-flex;align-items:center;gap:0.4rem;transition:color 0.2s;"
        onmouseover="this.style.color='var(--cream)'" onmouseout="this.style.color='var(--muted)'">
        ← Back to Job #{{ $job->reference }}
      </a>
    </div>
  </div>

  {{-- ── RIGHT: ORDER SUMMARY ── --}}
  <aside>
    <div class="summary-card" style="animation: fadeUp 0.4s ease 0.1s both;">
      <div class="summary-header">📋 Order Summary</div>
      <div class="summary-body">
        <div class="summary-row">
          <span class="lbl">Job Reference</span>
          <span class="val">{{ $job->reference }}</span>
        </div>
        <div class="summary-row">
          <span class="lbl">Device</span>
          <span class="val">{{ $job->booking->device_name }}</span>
        </div>
        <div class="summary-row">
          <span class="lbl">Service</span>
          <span class="val">{{ $job->booking->serviceListing->title ?? '—' }}</span>
        </div>
        <div class="summary-row">
          <span class="lbl">Agreed Price</span>
          <span class="val">৳{{ number_format($job->payment_amount, 0) }}</span>
        </div>

        <div class="summary-total">
          <div>
            <div class="lbl">Total Due</div>
            <div class="currency">BDT</div>
          </div>
          <div style="text-align:right;">
            <div class="amount">৳{{ number_format($job->payment_amount, 0) }}</div>
          </div>
        </div>

        <div class="tech-info-box">
          <div class="tech-avatar-lg">
            {{ strtoupper(substr($job->booking->technicianProfile->user->name ?? 'T', 0, 1)) }}
          </div>
          <div>
            <div class="name">{{ $job->booking->technicianProfile->user->name ?? '—' }}</div>
            <div class="role">Your Technician · {{ $job->booking->technicianProfile->location ?? '' }}</div>
          </div>
        </div>
      </div>
    </div>
  </aside>
</div>

<script>
  function formatCardNumber(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = v.replace(/(.{4})/g, '$1 ').trim();
    // Card network icon
    const icon = document.getElementById('card-icon');
    if (v.startsWith('4'))      icon.textContent = '💙'; // Visa
    else if (/^5[1-5]/.test(v)) icon.textContent = '🔴'; // Mastercard
    else if (v.startsWith('3')) icon.textContent = '🟡'; // Amex
    else                         icon.textContent = '💳';
  }

  function formatExpiry(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 3) v = v.substring(0, 2) + '/' + v.substring(2);
    input.value = v;
  }

  document.getElementById('payment-form').addEventListener('submit', function() {
    const btn = document.getElementById('pay-btn');
    btn.disabled = true;
    btn.innerHTML = '⏳ Processing…';
  });
</script>
@endsection
