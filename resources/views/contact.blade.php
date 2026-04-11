@extends('layout')
@section('title', 'Contact Us — VoltRevive')

@section('styles')
<style>
  .contact-wrap {
    max-width: 620px; margin: 0 auto;
    padding: 4rem 2rem 6rem; position: relative; z-index: 1;
  }
  .contact-wrap::before {
    content: ''; position: fixed; inset: 0;
    background: radial-gradient(ellipse 60% 50% at 50% 30%, rgba(212,137,26,0.05), transparent);
    pointer-events: none;
  }

  .contact-header { text-align: center; margin-bottom: 2.5rem; }
  .contact-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.8rem, 4vw, 2.4rem);
    margin-bottom: 0.5rem;
  }
  .contact-header p { color: var(--muted); font-size: 0.92rem; line-height: 1.7; }

  .contact-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 18px; padding: 2rem 2rem 2.25rem;
  }

  .form-group { margin-bottom: 1.25rem; }
  .form-label {
    display: block; font-size: 0.82rem; font-weight: 600;
    margin-bottom: 0.45rem; color: var(--cream);
  }
  .form-label .req { color: var(--amber); }
  .form-input, .form-textarea {
    width: 100%; background: var(--bg); border: 1px solid var(--border);
    border-radius: 9px; color: var(--cream);
    font-family: 'DM Sans', sans-serif; font-size: 0.92rem;
    padding: 0.8rem 1rem; outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
  }
  .form-input:focus, .form-textarea:focus {
    border-color: var(--amber);
    box-shadow: 0 0 0 3px rgba(212,137,26,0.12);
  }
  .form-input::placeholder, .form-textarea::placeholder { color: var(--muted); }
  .form-textarea { resize: vertical; min-height: 140px; line-height: 1.6; }

  .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

  .btn-submit {
    width: 100%; background: var(--amber); border: none; color: #161310;
    padding: 0.9rem; border-radius: 10px; font-weight: 700;
    font-size: 0.95rem; cursor: pointer; font-family: 'DM Sans', sans-serif;
    transition: background 0.2s; display: flex; align-items: center;
    justify-content: center; gap: 0.5rem;
  }
  .btn-submit:hover { background: var(--amber-lt); }
  .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

  /* Error states */
  .form-input.error, .form-textarea.error { border-color: rgba(200,60,60,0.6); }
  .field-error {
    font-size: 0.76rem; color: #f09090;
    margin-top: 0.3rem; display: none;
  }
  .field-error.show { display: block; }

  /* Success panel */
  .success-panel {
    display: none; text-align: center; padding: 2rem 1rem;
  }
  .success-panel.show { display: block; }
  .success-icon { font-size: 3rem; margin-bottom: 1rem; }
  .success-title {
    font-family: 'Playfair Display', serif; font-size: 1.6rem;
    color: #5de0b0; margin-bottom: 0.5rem;
  }
  .success-sub { color: var(--muted); font-size: 0.88rem; line-height: 1.7; }

  /* Info cards */
  .contact-info { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem; }
  .info-card {
    background: var(--bg-card2); border: 1px solid var(--border);
    border-radius: 12px; padding: 1.1rem 1.25rem;
    display: flex; align-items: flex-start; gap: 0.75rem;
  }
  .info-icon { font-size: 1.3rem; flex-shrink: 0; }
  .info-title { font-size: 0.82rem; font-weight: 700; margin-bottom: 0.2rem; }
  .info-text { font-size: 0.78rem; color: var(--muted); line-height: 1.5; }

  @keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:600px) {
    .form-row-2 { grid-template-columns: 1fr; }
    .contact-info { grid-template-columns: 1fr; }
  }
</style>
@endsection

@section('content')
<div class="contact-wrap">

  <div class="contact-header">
    <h1>Get in <em>Touch</em></h1>
    <p>Have a question about the platform, a listing, or a restoration? Send us a message and we'll get back to you within 24 hours.</p>
  </div>

  <div class="contact-card">

    {{-- FORM PANEL --}}
    <div id="form-panel">
      <form id="contact-form" onsubmit="submitContact(event)" novalidate>

        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label" for="c-name">Full Name <span class="req">*</span></label>
            <input type="text" class="form-input" id="c-name" placeholder="Your name" autocomplete="name">
            <div class="field-error" id="err-name">Please enter your name.</div>
          </div>
          <div class="form-group">
            <label class="form-label" for="c-email">Email Address <span class="req">*</span></label>
            <input type="email" class="form-input" id="c-email" placeholder="you@example.com" autocomplete="email">
            <div class="field-error" id="err-email">Please enter a valid email.</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="c-subject">Subject</label>
          <input type="text" class="form-input" id="c-subject" placeholder="e.g. Question about a service listing">
        </div>

        <div class="form-group">
          <label class="form-label" for="c-message">Message <span class="req">*</span></label>
          <textarea class="form-textarea" id="c-message" placeholder="Describe your question or issue in detail…"></textarea>
          <div class="field-error" id="err-message">Please enter a message.</div>
        </div>

        <button type="submit" class="btn-submit" id="btn-submit">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
          </svg>
          Send Message
        </button>

      </form>
    </div>

    {{-- SUCCESS PANEL --}}
    <div class="success-panel" id="success-panel">
      <div class="success-icon">✉️</div>
      <div class="success-title">Message Sent!</div>
      <p class="success-sub">
        Thank you for reaching out. Our team will review your message and respond to
        <strong id="success-email" style="color:var(--cream);"></strong> within 24 hours.
      </p>
    </div>

  </div>{{-- end contact-card --}}

  {{-- INFO CARDS --}}
  <div class="contact-info">
    <div class="info-card">
      <div class="info-icon">⚡</div>
      <div>
        <div class="info-title">Response Time</div>
        <div class="info-text">We typically respond within 24 hours on business days.</div>
      </div>
    </div>
    <div class="info-card">
      <div class="info-icon">🛡️</div>
      <div>
        <div class="info-title">Dispute Support</div>
        <div class="info-text">Job disputes are reviewed by our admin team within 48 hours.</div>
      </div>
    </div>
    <div class="info-card">
      <div class="info-icon">🔧</div>
      <div>
        <div class="info-title">Technician Help</div>
        <div class="info-text">Questions about listings or applications? We're here to help.</div>
      </div>
    </div>
    <div class="info-card">
      <div class="info-icon">📋</div>
      <div>
        <div class="info-title">Admin Inquiries</div>
        <div class="info-text">All messages go directly to the VoltRevive admin inbox.</div>
      </div>
    </div>
  </div>

</div>

<script>
  function submitContact(e) {
    e.preventDefault();

    const name    = document.getElementById('c-name');
    const email   = document.getElementById('c-email');
    const message = document.getElementById('c-message');
    let valid = true;

    // Reset errors
    document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('.form-input, .form-textarea').forEach(el => el.classList.remove('error'));

    // Validate name
    if (!name.value.trim()) {
      document.getElementById('err-name').classList.add('show');
      name.classList.add('error');
      valid = false;
    }

    // Validate email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email.value.trim() || !emailRegex.test(email.value)) {
      document.getElementById('err-email').classList.add('show');
      email.classList.add('error');
      valid = false;
    }

    // Validate message
    if (!message.value.trim()) {
      document.getElementById('err-message').classList.add('show');
      message.classList.add('error');
      valid = false;
    }

    if (!valid) return;

    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = `
      <div style="width:16px;height:16px;border:2px solid #161310;border-top-color:transparent;border-radius:50%;animation:spin 0.7s linear infinite;"></div>
      Sending…`;

    // POST to backend
    fetch('/contact', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({
        name:    name.value.trim(),
        email:   email.value.trim(),
        subject: document.getElementById('c-subject').value.trim(),
        message: message.value.trim(),
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        document.getElementById('form-panel').style.display = 'none';
        document.getElementById('success-email').textContent = email.value.trim();
        document.getElementById('success-panel').classList.add('show');
      } else {
        btn.disabled = false;
        btn.innerHTML = 'Send Message';
        alert(data.message || 'Something went wrong. Please try again.');
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.innerHTML = 'Send Message';
      alert('Network error. Please check your connection and try again.');
    });
  }
</script>
@endsection