@extends('layout')
@section('title', 'Leave a Review — Job #{{ $job->reference }}')

@section('styles')
<style>
  .review-wrap {
    max-width: 780px; margin: 0 auto;
    padding: 3rem 2rem 5rem; position: relative; z-index: 1;
  }
  .back-link {
    display: inline-flex; align-items: center; gap: 0.4rem;
    color: var(--muted); text-decoration: none; font-size: 0.85rem;
    margin-bottom: 2rem; transition: color 0.2s;
  }
  .back-link:hover { color: var(--cream); }

  /* ── JOB SUMMARY CARD ── */
  .job-summary {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 14px; padding: 1.5rem; margin-bottom: 2rem;
    display: flex; gap: 1.25rem; align-items: center; flex-wrap: wrap;
  }
  .job-summary-ba {
    display: grid; grid-template-columns: 1fr 1fr;
    height: 80px; width: 160px; gap: 2px; border-radius: 8px;
    overflow: hidden; flex-shrink: 0;
  }
  .ba-img { background-size: cover; background-position: center; }
  .im-sb { background: linear-gradient(135deg,#2a1f0e,#3d2b0f); }
  .im-sa { background: linear-gradient(135deg,#1a2a1a,#233520); }
  .job-summary-info { flex: 1; }
  .job-summary-device { font-family: 'Playfair Display', serif; font-size: 1.1rem; font-weight: 700; margin-bottom: 0.3rem; }
  .job-summary-meta { font-size: 0.8rem; color: var(--muted); }
  .job-summary-tech {
    display: flex; align-items: center; gap: 0.6rem;
    background: var(--bg-card2); border: 1px solid var(--border);
    border-radius: 8px; padding: 0.6rem 0.9rem; flex-shrink: 0;
  }
  .tech-sm-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--amber); display: flex; align-items: center;
    justify-content: center; font-weight: 700; font-size: 0.85rem; color: #161310;
  }
  .tech-sm-name { font-size: 0.85rem; font-weight: 600; }
  .tech-sm-loc { font-size: 0.72rem; color: var(--muted); }

  /* ── REVIEW FORM ── */
  .review-form-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: 16px; overflow: hidden;
    animation: fadeUp 0.4s ease;
  }
  .review-form-header {
    padding: 1.5rem 2rem; background: var(--bg-card2);
    border-bottom: 1px solid var(--border);
  }
  .review-form-header h1 { font-size: 1.5rem; margin-bottom: 0.25rem; }
  .review-form-header h1 em { font-style: italic; color: var(--amber-lt); }
  .review-form-header p { font-size: 0.85rem; color: var(--muted); }
  .review-form-body { padding: 2rem; }

  /* STAR RATING INPUT */
  .star-section { margin-bottom: 2rem; }
  .star-label { font-size: 0.82rem; font-weight: 600; margin-bottom: 0.75rem; display: block; }
  .star-input-row {
    display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
  }
  .star-btn {
    background: none; border: none; font-size: 2.2rem;
    cursor: pointer; line-height: 1; padding: 0.1rem;
    transition: transform 0.1s; color: var(--border);
    filter: grayscale(1);
  }
  .star-btn:hover, .star-btn.active { color: var(--amber); filter: none; transform: scale(1.15); }
  .star-btn.lit { color: var(--amber); filter: none; }
  .rating-label-text {
    font-size: 0.9rem; font-weight: 700; color: var(--amber-lt);
    margin-left: 0.5rem; min-width: 120px;
  }

  /* ASPECTS */
  .aspects-section { margin-bottom: 2rem; }
  .aspects-grid {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;
  }
  .aspect-item { }
  .aspect-label { font-size: 0.78rem; color: var(--muted); margin-bottom: 0.4rem; display: block; }
  .aspect-stars { display: flex; gap: 0.2rem; }
  .aspect-star {
    background: none; border: none; font-size: 1.2rem;
    cursor: pointer; padding: 0; color: var(--border); filter: grayscale(1);
    transition: all 0.1s;
  }
  .aspect-star.lit { color: var(--amber); filter: none; }

  /* COMMENT */
  .comment-section { margin-bottom: 1.75rem; }
  .form-label { font-size: 0.82rem; font-weight: 600; margin-bottom: 0.5rem; display: block; }
  .form-textarea {
    width: 100%; background: var(--bg); border: 1px solid var(--border);
    border-radius: 9px; color: var(--cream); font-family: 'DM Sans', sans-serif;
    font-size: 0.9rem; padding: 0.9rem 1rem; outline: none; resize: vertical;
    min-height: 130px; transition: border-color 0.2s;
  }
  .form-textarea:focus { border-color: var(--amber); }
  .form-textarea::placeholder { color: var(--muted); }
  .char-counter { font-size: 0.72rem; color: var(--muted); text-align: right; margin-top: 0.3rem; }

  /* TAGS */
  .tags-section { margin-bottom: 2rem; }
  .tag-options { display: flex; flex-wrap: wrap; gap: 0.5rem; }
  .tag-chip {
    padding: 0.4rem 0.9rem; border-radius: 20px; font-size: 0.8rem;
    border: 1px solid var(--border); color: var(--muted);
    background: transparent; cursor: pointer; transition: all 0.15s;
    font-family: 'DM Sans', sans-serif;
  }
  .tag-chip:hover { border-color: var(--amber); color: var(--amber-lt); }
  .tag-chip.selected {
    background: rgba(212,137,26,0.12); border-color: var(--amber);
    color: var(--amber-lt); font-weight: 600;
  }

  /* SUBMIT */
  .review-form-footer {
    padding: 1.5rem 2rem; border-top: 1px solid var(--border);
    background: var(--bg-card2); display: flex;
    align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
  }
  .submit-note { font-size: 0.78rem; color: var(--muted); max-width: 360px; line-height: 1.55; }
  .btn-submit-review {
    background: var(--amber); border: none; color: #161310;
    padding: 0.8rem 2rem; border-radius: 9px; font-weight: 700;
    font-size: 0.92rem; cursor: pointer; font-family: 'DM Sans', sans-serif;
    transition: background 0.2s; display: flex; align-items: center; gap: 0.5rem;
    flex-shrink: 0;
  }
  .btn-submit-review:hover { background: var(--amber-lt); }
  .btn-submit-review:disabled { opacity: 0.4; cursor: not-allowed; }

  /* SUCCESS STATE */
  .success-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.85); z-index: 999;
    align-items: center; justify-content: center;
  }
  .success-overlay.open { display: flex; }
  .success-box {
    background: var(--bg-card); border: 1px solid var(--amber);
    border-radius: 18px; padding: 2.5rem; text-align: center;
    max-width: 420px; animation: fadeUp 0.3s ease;
  }
  .success-icon { font-size: 3.5rem; margin-bottom: 1rem; }
  .success-box h2 { font-size: 1.5rem; margin-bottom: 0.6rem; }
  .success-box p { color: var(--muted); font-size: 0.88rem; line-height: 1.65; margin-bottom: 1.75rem; }
  .btn-success {
    background: var(--amber); border: none; color: #161310;
    padding: 0.75rem 2rem; border-radius: 9px; font-weight: 700;
    font-size: 0.9rem; cursor: pointer; font-family: 'DM Sans', sans-serif;
    transition: background 0.2s;
  }
  .btn-success:hover { background: var(--amber-lt); }

  @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
  @media(max-width:600px) {
    .review-wrap { padding: 2rem 1rem 3rem; }
    .job-summary { flex-direction: column; align-items: flex-start; }
    .aspects-grid { grid-template-columns: 1fr 1fr; }
    .review-form-footer { flex-direction: column; align-items: stretch; }
    .btn-submit-review { justify-content: center; }
  }
</style>
@endsection

@section('content')
<div class="review-wrap">
  <a href="/jobs/{{ $job->id }}" class="back-link">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
    Back to Job
  </a>

  {{-- JOB SUMMARY --}}
  <div class="job-summary">
    <div class="job-summary-ba">
      <div class="ba-img im-sb"></div>
      <div class="ba-img im-sa"></div>
    </div>
    <div class="job-summary-info">
      <div class="job-summary-device">{{ $job->booking->serviceListing->title ?? $job->booking->device_name }}</div>
      <div class="job-summary-meta">Job #{{ $job->reference }} · Completed {{ $job->updated_at->format('F Y') }}</div>
    </div>
    <div class="job-summary-tech">
      <div class="tech-sm-avatar">{{ $job->booking->technicianProfile->user->initial() }}</div>
      <div>
        <div class="tech-sm-name">{{ $job->booking->technicianProfile->user->name }}</div>
        <div class="tech-sm-loc">{{ $job->booking->technicianProfile->location ?? '' }}</div>
      </div>
    </div>
  </div>

  {{-- REVIEW FORM --}}
  <div class="review-form-card">
    <div class="review-form-header">
      @if($alreadyReviewed)
      <h1>Review <em>Submitted</em></h1>
      <p>You have already submitted a review for this job. Thank you!</p>
      @else
      <h1>Rate Your <em>Restoration</em></h1>
      <p>Your review helps other collectors make informed decisions and rewards great technicians.</p>
      @endif
    </div>

    <div class="review-form-body">
      <form onsubmit="submitReview(event)">

        {{-- OVERALL STAR RATING --}}
        <div class="star-section">
          <span class="star-label">Overall Rating <span style="color:var(--amber)">*</span></span>
          <div class="star-input-row">
            <button type="button" class="star-btn" data-val="1" onclick="setRating(1)">★</button>
            <button type="button" class="star-btn" data-val="2" onclick="setRating(2)">★</button>
            <button type="button" class="star-btn" data-val="3" onclick="setRating(3)">★</button>
            <button type="button" class="star-btn" data-val="4" onclick="setRating(4)">★</button>
            <button type="button" class="star-btn" data-val="5" onclick="setRating(5)">★</button>
            <span class="rating-label-text" id="rating-label">Tap to rate</span>
          </div>
          <input type="hidden" id="rating-val" value="0"/>
        </div>

        {{-- ASPECT RATINGS --}}
        <div class="aspects-section">
          <span class="star-label">Rate Individual Aspects</span>
          <div class="aspects-grid">
            <div class="aspect-item">
              <span class="aspect-label">Communication</span>
              <div class="aspect-stars" id="asp-comm">
                <button type="button" class="aspect-star" onclick="setAspect('comm',1)">★</button>
                <button type="button" class="aspect-star" onclick="setAspect('comm',2)">★</button>
                <button type="button" class="aspect-star" onclick="setAspect('comm',3)">★</button>
                <button type="button" class="aspect-star" onclick="setAspect('comm',4)">★</button>
                <button type="button" class="aspect-star" onclick="setAspect('comm',5)">★</button>
              </div>
            </div>
            <div class="aspect-item">
              <span class="aspect-label">Workmanship</span>
              <div class="aspect-stars" id="asp-work">
                <button type="button" class="aspect-star" onclick="setAspect('work',1)">★</button>
                <button type="button" class="aspect-star" onclick="setAspect('work',2)">★</button>
                <button type="button" class="aspect-star" onclick="setAspect('work',3)">★</button>
                <button type="button" class="aspect-star" onclick="setAspect('work',4)">★</button>
                <button type="button" class="aspect-star" onclick="setAspect('work',5)">★</button>
              </div>
            </div>
          </div>
        </div>

        {{-- WRITTEN REVIEW --}}
        <div class="comment-section">
          <label class="form-label" for="review-text">Written Review <span style="color:var(--amber)">*</span></label>
          <textarea class="form-textarea" id="review-text" placeholder="Describe the restoration — what was the fault, how well was it fixed, how was communication throughout? Other collectors will read this before booking…" maxlength="600" oninput="document.getElementById('char-counter').textContent=(600-this.value.length)+' characters left'" required></textarea>
          <div class="char-counter" id="char-counter">600 characters left</div>
        </div>

        {{-- QUICK TAGS --}}
        <div class="tags-section">
          <span class="form-label">Quick Tags (optional)</span>
          <div class="tag-options">
            <button type="button" class="tag-chip" onclick="this.classList.toggle('selected')">✓ Arrived safely packed</button>
            <button type="button" class="tag-chip" onclick="this.classList.toggle('selected')">✓ Sounds perfect</button>
            <button type="button" class="tag-chip" onclick="this.classList.toggle('selected')">✓ Looks like new</button>
            <button type="button" class="tag-chip" onclick="this.classList.toggle('selected')">✓ Proactive updates</button>
            <button type="button" class="tag-chip" onclick="this.classList.toggle('selected')">✓ On time</button>
            <button type="button" class="tag-chip" onclick="this.classList.toggle('selected')">✓ Would use again</button>
            <button type="button" class="tag-chip" onclick="this.classList.toggle('selected')">⚠ Slight delay</button>
            <button type="button" class="tag-chip" onclick="this.classList.toggle('selected')">⚠ Minor cosmetic issue</button>
          </div>
        </div>

      </form>
    </div>

    <div class="review-form-footer">
      <p class="submit-note">Your review will be publicly visible on {{ $job->booking->technicianProfile->user->name ?? 'the technician' }}'s profile and will help other collectors. You can only submit one review per job.</p>
      <button class="btn-submit-review" id="submit-btn" type="button" onclick="submitReview(event)" disabled>
        Submit Review
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
    </div>
  </div>
</div>

{{-- SUCCESS MODAL --}}
<div class="success-overlay" id="success-overlay">
  <div class="success-box">
    <div class="success-icon">⭐</div>
    <h2>Review Submitted!</h2>
    <p>Thank you for taking the time to review {{ $job->booking->technicianProfile->user->name ?? 'your technician' }}. Your feedback helps other collectors find great technicians.</p>
    <button class="btn-success" onclick="window.location='/collector-portfolio'">Back to My Portfolio →</button>
  </div>
</div>

<script>
  const ratingLabels = ['','Poor','Fair','Good','Great','Outstanding!'];
  let currentRating = 0;

  function setRating(val) {
    currentRating = val;
    document.getElementById('rating-val').value = val;
    document.getElementById('rating-label').textContent = ratingLabels[val];
    document.querySelectorAll('.star-btn').forEach(btn => {
      btn.classList.toggle('lit', parseInt(btn.dataset.val) <= val);
    });
    checkSubmit();
  }

  function setAspect(aspect, val) {
    const row = document.getElementById('asp-' + aspect);
    row.querySelectorAll('.aspect-star').forEach((s, i) => {
      s.classList.toggle('lit', i < val);
    });
  }

  function checkSubmit() {
    const text = document.getElementById('review-text').value.trim();
    document.getElementById('submit-btn').disabled = !(currentRating > 0 && text.length > 10);
  }
  document.getElementById('review-text').addEventListener('input', checkSubmit);

  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const jobId = {{ $job->id }};

  function submitReview(e) {
    if (e) e.preventDefault();
    if (currentRating === 0) { alert('Please select a star rating.'); return; }
    const comment = document.getElementById('review-text').value.trim();
    if (!comment) { alert('Please write a review.'); return; }

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.textContent = 'Submitting...';

    fetch('/jobs/' + jobId + '/review', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
      body: JSON.stringify({ rating: currentRating, comment }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        document.getElementById('success-overlay').classList.add('open');
      } else {
        alert(data.message || 'Could not submit review.');
        btn.disabled = false;
        btn.textContent = 'Submit Review';
      }
    })
    .catch(() => {
      alert('Network error. Please try again.');
      btn.disabled = false;
      btn.textContent = 'Submit Review';
    });
  }

  // hover preview on stars
  document.querySelectorAll('.star-btn').forEach(btn => {
    btn.addEventListener('mouseenter', () => {
      const val = parseInt(btn.dataset.val);
      document.querySelectorAll('.star-btn').forEach(b => {
        b.classList.toggle('active', parseInt(b.dataset.val) <= val);
      });
    });
    btn.addEventListener('mouseleave', () => {
      document.querySelectorAll('.star-btn').forEach(b => {
        b.classList.remove('active');
        b.classList.toggle('lit', parseInt(b.dataset.val) <= currentRating);
      });
    });
  });
</script>
@endsection