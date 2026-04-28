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

  .form-textarea { resize: vertical; min-height: 140px; }

  .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

  .btn-submit {
    width: 100%; background: var(--amber); border: none; color: #161310;
    padding: 0.9rem; border-radius: 10px; font-weight: 700;
    font-size: 0.95rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 0.5rem;
  }

  .btn-submit:hover { background: var(--amber-lt); }

  .form-input.error, .form-textarea.error { border-color: rgba(200,60,60,0.6); }

  .field-error {
    font-size: 0.76rem; color: #f09090;
    margin-top: 0.3rem; display: none;
  }
  .field-error.show { display: block; }

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

  @media(max-width:600px) {
    .form-row-2 { grid-template-columns: 1fr; }
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

    <div id="form-panel">
      <form id="contact-form" onsubmit="submitContact(event)" novalidate>

        <div class="form-row-2">
          <div class="form-group">
            <label class="form-label">Full Name <span class="req">*</span></label>
            <input type="text" class="form-input" id="c-name" placeholder="Your name">
            <div class="field-error" id="err-name">Please enter your name.</div>
          </div>

          <div class="form-group">
            <label class="form-label">Email Address <span class="req">*</span></label>
            <input type="email" class="form-input" id="c-email" placeholder="you@example.com">
            <div class="field-error" id="err-email">Please enter a valid email.</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Subject</label>
          <input type="text" class="form-input" id="c-subject">
        </div>

        <div class="form-group">
          <label class="form-label">Message <span class="req">*</span></label>
          <textarea class="form-textarea" id="c-message"></textarea>
          <div class="field-error" id="err-message">Please enter a message.</div>
        </div>

        <button type="submit" class="btn-submit" id="btn-submit">
          Send Message
        </button>

      </form>
    </div>

    <div class="success-panel" id="success-panel">
      <div class="success-icon">✉️</div>
      <div class="success-title">Message Sent!</div>
      <p class="success-sub">
        Thank you for reaching out. We’ll respond within 24 hours.
      </p>
    </div>

  </div>

</div>

<script>
function submitContact(e) {
  e.preventDefault();

  const name = document.getElementById('c-name');
  const email = document.getElementById('c-email');
  const message = document.getElementById('c-message');
  let valid = true;

  document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));
  document.querySelectorAll('.form-input, .form-textarea').forEach(el => el.classList.remove('error'));

  if (!name.value.trim()) {
    document.getElementById('err-name').classList.add('show');
    name.classList.add('error');
    valid = false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!email.value.trim() || !emailRegex.test(email.value)) {
    document.getElementById('err-email').classList.add('show');
    email.classList.add('error');
    valid = false;
  }

  if (!message.value.trim()) {
    document.getElementById('err-message').classList.add('show');
    message.classList.add('error');
    valid = false;
  }

  if (!valid) return;

  fetch('/contact', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
    },
    body: JSON.stringify({
      name: name.value.trim(),
      email: email.value.trim(),
      subject: document.getElementById('c-subject').value.trim(),
      message: message.value.trim(),
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      document.getElementById('form-panel').style.display = 'none';
      document.getElementById('success-panel').classList.add('show');
    }
  });
}
</script>
@endsection