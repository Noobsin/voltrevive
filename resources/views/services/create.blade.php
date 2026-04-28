@extends('layout')
@section('title', 'List a Service')

@section('styles')
<style>
  .page-wrap { max-width:1100px; margin:0 auto; padding:3rem 2rem 5rem; position:relative; z-index:1; }
  .form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; overflow:hidden; animation:fadeUp 0.4s ease; }
  .form-card-header { padding:2rem 2rem 1.5rem; border-bottom:1px solid var(--border); background:var(--bg-card2); }
  .form-card-header h1 { font-size:1.8rem; line-height:1.15; }
  .form-card-header h1 em { font-style:italic; color:var(--amber-lt); }
  .form-card-header p { color:var(--muted); font-size:0.9rem; margin-top:0.5rem; }
  .form-body { padding:2rem; }
  .steps { display:flex; margin-bottom:2rem; }
  .step { flex:1; text-align:center; position:relative; padding-bottom:1rem; }
  .step::after { content:''; position:absolute; bottom:0; left:50%; right:-50%; height:2px; background:var(--border); }
  .step:last-child::after { display:none; }
  .step.active::after,.step.done::after { background:var(--amber); }
  .step-num { width:32px; height:32px; border-radius:50%; border:2px solid var(--border); display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700; margin-bottom:0.4rem; background:var(--bg); }
  .step.active .step-num { border-color:var(--amber); color:var(--amber); background:rgba(212,137,26,0.1); }
  .step.done .step-num { border-color:var(--amber); background:var(--amber); color:#161310; }
  .step-label { font-size:0.72rem; color:var(--muted); display:block; }
  .step.active .step-label { color:var(--amber-lt); }
  .form-section { margin-bottom:2rem; }
  .section-title { font-size:0.7rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:var(--amber); margin-bottom:1.25rem; display:flex; align-items:center; gap:0.75rem; }
  .section-title::after { content:''; flex:1; height:1px; background:var(--border); }
  .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
  .form-group { margin-bottom:1.25rem; }
  .form-label { display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.4rem; color:var(--cream); }
  .form-label .req { color:var(--amber); margin-left:2px; }
  .form-input,.form-select,.form-textarea { width:100%; background:var(--bg); border:1px solid var(--border); border-radius:8px; color:var(--cream); font-family:'DM Sans',sans-serif; font-size:0.9rem; padding:0.75rem 1rem; transition:border-color 0.2s,box-shadow 0.2s; outline:none; appearance:none; }
  .form-input:focus,.form-select:focus,.form-textarea:focus { border-color:var(--amber); box-shadow:0 0 0 3px rgba(212,137,26,0.12); }
  .form-input::placeholder,.form-textarea::placeholder { color:var(--muted); }
  .form-textarea { resize:vertical; min-height:100px; }
  .hint { font-size:0.75rem; color:var(--muted); margin-top:0.3rem; }
  .tag-input-wrap { background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:0.5rem; display:flex; flex-wrap:wrap; gap:0.4rem; cursor:text; min-height:48px; align-items:center; }
  .tag-input-wrap:focus-within { border-color:var(--amber); }
  .tag { background:rgba(212,137,26,0.15); border:1px solid rgba(212,137,26,0.3); color:var(--amber-lt); padding:0.25rem 0.6rem; border-radius:5px; font-size:0.8rem; font-weight:600; display:flex; align-items:center; gap:0.35rem; }
  .tag button { background:none; border:none; color:var(--amber); cursor:pointer; font-size:1rem; line-height:1; padding:0; }
  .tag-input { flex:1; min-width:120px; background:none; border:none; outline:none; color:var(--cream); font-family:'DM Sans',sans-serif; font-size:0.88rem; padding:0.25rem 0.3rem; }
  .tag-input::placeholder { color:var(--muted); }
  .price-row { display:grid; grid-template-columns:1fr auto 1fr; gap:0.75rem; align-items:center; }
  .price-sep { color:var(--muted); text-align:center; padding-top:1.5rem; }
  .upload-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
  .upload-zone { border:2px dashed var(--border); border-radius:12px; padding:1.5rem 1rem; text-align:center; cursor:pointer; transition:all 0.2s; position:relative; overflow:hidden; }
  .upload-zone:hover { border-color:var(--amber); background:rgba(212,137,26,0.04); }
  .upload-zone.has-file { border-style:solid; border-color:var(--amber); }
  .upload-zone input[type="file"] { display:none; }
  .upload-icon { font-size:2rem; margin-bottom:0.5rem; }
  .upload-label { font-size:0.75rem; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; }
  .upload-label.before { color:var(--muted); } .upload-label.after { color:var(--amber-lt); }
  .upload-hint { font-size:0.7rem; color:var(--muted); margin-top:0.25rem; }
  .upload-preview { position:absolute; inset:0; display:none; background-size:cover; background-position:center; }
  .upload-preview.visible { display:flex; align-items:flex-end; padding:0.5rem; }
  .upload-req-note { grid-column:1/-1; display:flex; align-items:center; gap:0.5rem; padding:0.75rem 1rem; background:rgba(212,137,26,0.07); border:1px solid rgba(212,137,26,0.2); border-radius:8px; font-size:0.78rem; color:var(--muted); }
  .upload-req-note span:first-child { color:var(--amber); }
  .days-grid { display:flex; flex-wrap:wrap; gap:0.5rem; }
  .day-chip { padding:0.4rem 0.9rem; border-radius:6px; border:1px solid var(--border); font-size:0.8rem; cursor:pointer; transition:all 0.15s; user-select:none; }
  .day-chip:hover { border-color:var(--amber); color:var(--amber-lt); }
  .day-chip.active { background:rgba(212,137,26,0.12); border-color:var(--amber); color:var(--amber-lt); }
  .form-actions { display:flex; gap:1rem; justify-content:flex-end; padding-top:1.5rem; border-top:1px solid var(--border); margin-top:0.5rem; }
  .btn-cancel { background:transparent; border:1px solid var(--border); color:var(--muted); padding:0.7rem 1.5rem; border-radius:8px; cursor:pointer; font-size:0.9rem; font-family:'DM Sans',sans-serif; transition:all 0.2s; }
  .btn-cancel:hover { border-color:var(--muted); color:var(--cream); }
  .btn-submit { background:var(--amber); border:none; color:#161310; padding:0.7rem 2rem; border-radius:8px; font-weight:700; font-size:0.9rem; cursor:pointer; font-family:'DM Sans',sans-serif; transition:background 0.2s; display:flex; align-items:center; gap:0.5rem; }
  .btn-submit:hover { background:var(--amber-lt); }
  .sidebar { display:flex; flex-direction:column; gap:1.25rem; }
  .info-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; padding:1.5rem; animation:fadeUp 0.45s ease; }
  .info-card-title { font-family:'Playfair Display',serif; font-size:1rem; margin-bottom:1rem; }
  .status-badge { display:inline-flex; align-items:center; gap:0.4rem; padding:0.35rem 0.9rem; border-radius:20px; font-size:0.78rem; font-weight:700; background:rgba(212,137,26,0.1); border:1px solid rgba(212,137,26,0.3); color:var(--amber-lt); margin-bottom:1rem; }
  .status-dot { width:6px; height:6px; border-radius:50%; background:var(--amber); animation:pulse 2s infinite; }
  @keyframes pulse{0%,100%{opacity:1}50%{opacity:0.4}}
  .process-steps { display:flex; flex-direction:column; gap:0.75rem; }
  .process-step { display:flex; align-items:flex-start; gap:0.75rem; }
  .process-num { width:24px; height:24px; border-radius:50%; background:var(--bg-card2); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; flex-shrink:0; color:var(--amber); }
  .process-text p { font-size:0.82rem; font-weight:600; }
  .process-text span { font-size:0.75rem; color:var(--muted); }
  .requirements { display:flex; flex-direction:column; gap:0.6rem; }
  .req-item { display:flex; align-items:flex-start; gap:0.6rem; font-size:0.82rem; }
  .req-icon { color:var(--amber); flex-shrink:0; }
  .preview-card { border:1px solid var(--amber); border-radius:12px; overflow:hidden; background:var(--bg-card2); }
  .preview-header { padding:0.75rem 1rem; font-size:0.7rem; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; color:var(--amber); background:rgba(212,137,26,0.08); border-bottom:1px solid var(--border); }
  .preview-images { height:120px; display:grid; grid-template-columns:1fr 1fr; gap:2px; }
  .preview-img-slot { background:var(--bg); display:flex; align-items:center; justify-content:center; font-size:0.7rem; color:var(--muted); text-align:center; padding:0.5rem; }
  .preview-body { padding:0.75rem 1rem; }
  .preview-title { font-family:'Playfair Display',serif; font-size:0.95rem; margin-bottom:0.3rem; min-height:1.2em; }
  .preview-meta { font-size:0.75rem; color:var(--muted); }
  @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:999; align-items:center; justify-content:center; }
  .modal-overlay.open { display:flex; }
  .modal-box { background:var(--bg-card); border:1px solid var(--amber); border-radius:16px; padding:2.5rem; max-width:420px; text-align:center; animation:fadeUp 0.3s ease; }
  .btn-modal { background:var(--amber); border:none; color:#161310; padding:0.75rem 2rem; border-radius:8px; font-weight:700; font-size:0.9rem; cursor:pointer; font-family:'DM Sans',sans-serif; }
  @media(max-width:860px){ .page-wrap{grid-template-columns:1fr} .form-row{grid-template-columns:1fr} .sidebar{order:-1} }
  @media(max-width:600px){ .page-wrap{padding:1.5rem 1rem 3rem} }
</style>
@endsection

@section('content')
<div class="page-wrap">

  {{-- MAIN FORM --}}
  <div>
    <div class="form-card">
      <div class="form-card-header">
        <h1>List a <em>Restoration</em> Service</h1>
        <p>Your listing is your portfolio — make it count. Every service requires verified Before & After proof.</p>
      </div>
      <div class="form-body">
        {{-- Server-side validation errors --}}
        @if($errors->any())
          <div style="background:rgba(200,60,60,0.1);border:1px solid rgba(200,60,60,0.3);border-radius:8px;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:0.85rem;color:#f09090;">
            <strong>Please fix the following:</strong>
            <ul style="margin:0.5rem 0 0 1.25rem;">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="steps">
          <div class="step done">
            <div class="step-num">✓</div>
            <span class="step-label">Service Info</span>
          </div>
          <div class="step active">
            <div class="step-num">2</div>
            <span class="step-label">Portfolio Proof</span>
          </div>
          <div class="step">
            <div class="step-num">3</div>
            <span class="step-label">Availability</span>
          </div>
        </div>

        <form method="POST" action="/services" id="listing-form" enctype="multipart/form-data">
          @csrf

          {{-- SERVICE INFO --}}
          <div class="form-section">
            <div class="section-title">Service Information</div>
            <div class="form-group">
              <label class="form-label">Service Title <span class="req">*</span></label>
              <input type="text" class="form-input" id="service-title" name="title" placeholder="e.g. Roland Juno-106 Full Voice Chip Restoration" oninput="updatePreview()" required/>
              <p class="hint">Be specific — include the device model and what you're fixing.</p>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Device Category <span class="req">*</span></label>
                <select class="form-select" name="category" required>
                  <option value="" disabled selected>Select category…</option>
                  <option>Synthesizers</option>
                  <option>Retro Gaming</option>
                  <option>Hi-Fi Audio</option>
                  <option>Vintage Radio</option>
                  <option>Cameras</option>
                  <option>Vintage Computer</option>
                  <option>Other</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Price Range (BDT) <span class="req">*</span></label>
                <div class="price-row">
                  <input type="number" class="form-input" name="price_min" id="price-min" placeholder="Min" min="0" required/>
                  <div class="price-sep">—</div>
                  <input type="number" class="form-input" name="price_max" id="price-max" placeholder="Max" min="0" required/>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Supported Device Models <span class="req">*</span></label>
              <div class="tag-input-wrap" id="model-tags" onclick="this.querySelector('input').focus()">
                <div class="tag">Roland Juno-106 <button type="button" onclick="this.closest('.tag').remove()">×</button></div>
                <input type="text" class="tag-input" placeholder="Type model, press Enter…" onkeydown="addTag(event)"/>
              </div>
              <p class="hint">Press Enter to add each model.</p>
              <input type="hidden" name="supported_models" id="supported-models-input"/>
            </div>
            <div class="form-group">
              <label class="form-label">Service Description <span class="req">*</span></label>
              <textarea class="form-textarea" name="description" placeholder="Describe exactly what you do: which components you replace, the process, and what the device will be like after restoration…" rows="4" required></textarea>
            </div>
          </div>

          {{-- PORTFOLIO PROOF --}}
          <div class="form-section">
            <div class="section-title">Portfolio Proof — Before & After</div>
            <div class="upload-grid">
              <div class="upload-req-note">
                <span>⚡</span>
                <span>Both images are <strong>mandatory</strong>. Listings without verified Before & After pairs will not be approved.</span>
              </div>
              <div class="upload-zone" id="before-zone" onclick="document.getElementById('before-file').click()">
                <input type="file" id="before-file" name="before_image" accept="image/*" onchange="previewImage(this,'before-zone','before')"/>
                <div class="upload-preview" id="before-preview"></div>
                <div class="upload-icon">📷</div>
                <div class="upload-label before">BEFORE Photo</div>
                <div class="upload-hint">Show the device's fault condition clearly</div>
              </div>
              <div class="upload-zone" id="after-zone" onclick="document.getElementById('after-file').click()">
                <input type="file" id="after-file" name="after_image" accept="image/*" onchange="previewImage(this,'after-zone','after')"/>
                <div class="upload-preview" id="after-preview"></div>
                <div class="upload-icon">✨</div>
                <div class="upload-label after">AFTER Photo</div>
                <div class="upload-hint">Show the fully restored device</div>
              </div>
            </div>
          </div>

          {{-- AVAILABILITY --}}
          <div class="form-section">
            <div class="section-title">Availability & Capacity</div>
            <div class="form-group">
              <label class="form-label">Available Days <span class="req">*</span></label>
              <input type="hidden" name="availability_days" id="availability-days-input"/>
              <div class="days-grid">
                <div class="day-chip" onclick="this.classList.toggle('active')">Mon</div>
                <div class="day-chip active" onclick="this.classList.toggle('active')">Tue</div>
                <div class="day-chip active" onclick="this.classList.toggle('active')">Wed</div>
                <div class="day-chip active" onclick="this.classList.toggle('active')">Thu</div>
                <div class="day-chip" onclick="this.classList.toggle('active')">Fri</div>
                <div class="day-chip" onclick="this.classList.toggle('active')">Sat</div>
                <div class="day-chip" onclick="this.classList.toggle('active')">Sun</div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-cancel" onclick="history.back()">Cancel</button>
            <button type="submit" class="btn-submit">
              Submit for Review
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- SIDEBAR --}}
  <aside class="sidebar">
  </aside>
</div>

{{-- SUCCESS MODAL --}}
<div class="modal-overlay" id="success-modal">
  <div class="modal-box">
    <div style="font-size:3rem;margin-bottom:1rem;">⚡</div>
    <h2 style="font-size:1.5rem;margin-bottom:0.75rem;">Listing Submitted!</h2>
    <p style="color:var(--muted);font-size:0.9rem;margin-bottom:1.75rem;">Your service listing is now <strong style="color:var(--amber-lt)">Pending Admin Review</strong>. You'll be notified once it's approved.</p>
    <button class="btn-modal" onclick="window.location='/browse'">Go to Browse →</button>
  </div>
</div>

<script>
  function addTag(e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    const input = e.target;
    const val = input.value.trim();
    if (!val) return;
    const wrap = document.getElementById('model-tags');
    const tag = document.createElement('div');
    tag.className = 'tag';
    tag.innerHTML = `${val} <button type="button" onclick="this.closest('.tag').remove()">×</button>`;
    wrap.insertBefore(tag, input);
    input.value = '';
  }

  function previewImage(input, zoneId, type) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
      const zone = document.getElementById(zoneId);
      zone.classList.add('has-file');
      const preview = document.getElementById(type + '-preview');
      preview.style.backgroundImage = `url('${e.target.result}')`;
      preview.classList.add('visible');
      preview.innerHTML = `<span style="background:rgba(22,19,16,0.8);color:var(--cream);font-size:0.7rem;font-weight:600;padding:0.2rem 0.5rem;border-radius:4px;">${type === 'before' ? '📷 Before' : '✨ After'}</span>`;
      const slot = document.getElementById('prev-' + type);
      slot.style.backgroundImage = `url('${e.target.result}')`;
      slot.style.backgroundSize = 'cover';
      slot.style.backgroundPosition = 'center';
      slot.textContent = '';
    };
    reader.readAsDataURL(input.files[0]);
  }

  function updatePreview() {
    const title = document.getElementById('service-title').value;
    document.getElementById('prev-title').textContent = title || 'Your service title will appear here';
  }

  // Collect tag and day values into hidden inputs, then submit
  document.getElementById('listing-form').addEventListener('submit', function(e) {

    // 1. Collect supported models from visible tag divs
    const tags = document.querySelectorAll('#model-tags .tag');
    const models = Array.from(tags).map(function(t) {
      // firstChild is the text node before the × button
      return t.childNodes[0].textContent.trim();
    }).filter(function(m) { return m.length > 0; });
    document.getElementById('supported-models-input').value = JSON.stringify(models);

    // 2. Collect selected availability days
    const activeDays = document.querySelectorAll('.day-chip.active');
    const days = Array.from(activeDays).map(function(d) {
      return d.textContent.trim();
    });
    document.getElementById('availability-days-input').value = JSON.stringify(days);

    // 3. Client-side guard — stop submit if either is empty
    if (models.length === 0) {
      e.preventDefault();
      alert('Please add at least one supported device model.');
      return;
    }
    if (days.length === 0) {
      e.preventDefault();
      alert('Please select at least one available day.');
      return;
    }

    // Form submits normally to POST /services → ServiceListingController@store
  });

  document.getElementById('success-modal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
  });
</script>
@endsection