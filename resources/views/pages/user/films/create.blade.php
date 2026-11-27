@extends('site_app')

@section('head_title', 'Upload Film | ' . getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<link rel="stylesheet" href="{{ URL::asset('site_assets/css/nice-select.css') }}">
<style>
    .cine-upload-page { color: #fff; }
    .cine-upload-page .breadcrumb-section { margin-bottom: 0; }
    .cine-upload-hero { padding: 34px 0 26px; }
    .cine-upload-hero h1 { margin: 0 0 8px; font-size: 32px; font-weight: 800; }
    .cine-upload-hero p { margin: 0; color: rgba(255,255,255,.68); font-size: 15px; }
    .cine-upload-layout { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(300px, .75fr); gap: 24px; align-items: start; }
    .cine-upload-card { background: rgba(24, 16, 53, .96); border: 1px solid rgba(255,255,255,.10); border-radius: 14px; padding: 26px; box-shadow: 0 12px 35px rgba(0,0,0,.16); }
    .cine-upload-card + .cine-upload-card { margin-top: 18px; }
    .cine-upload-section-title { display: flex; align-items: center; gap: 12px; margin: 0 0 20px; font-size: 20px; font-weight: 800; }
    .cine-upload-section-title i { color: #ff0f78; font-size: 18px; }
    .cine-upload-section-title span { display: block; width: 34px; height: 3px; margin-left: auto; background: linear-gradient(90deg,#ff0f78,#ff8a00); border-radius: 3px; }
    .cine-upload-field { margin-bottom: 18px; }
    .cine-upload-field:last-child { margin-bottom: 0; }
    .cine-upload-field label { display: block; margin-bottom: 8px; color: rgba(255,255,255,.92); font-size: 13px; font-weight: 700; }
    .cine-upload-field label em { color: #ff7b93; font-style: normal; }
    .cine-upload-field input:not([type="radio"]):not([type="checkbox"]):not([type="hidden"]), .cine-upload-field select, .cine-upload-field textarea {
        width: 100%; color: #fff; background: rgba(8,5,23,.72); border: 1px solid rgba(255,255,255,.15); border-radius: 8px; padding: 12px 14px; outline: none; transition: border-color .18s, box-shadow .18s;
    }
    .cine-upload-field textarea { min-height: 128px; resize: vertical; }
    .cine-upload-field input:not([type="radio"]):not([type="checkbox"]):not([type="hidden"]):focus, .cine-upload-field select:focus, .cine-upload-field textarea:focus { border-color: #ff0f78; box-shadow: 0 0 0 3px rgba(255,15,120,.13); }
    .cine-upload-field { position: relative; }
    .cine-upload-field .nice-select { float: none; width: 100%; height: 47px; line-height: 45px; color: #fff; background: rgba(8,5,23,.72); border: 1px solid rgba(255,255,255,.15); border-radius: 8px; padding-left: 14px; padding-right: 38px; }
    .cine-upload-field .nice-select:hover, .cine-upload-field .nice-select.open, .cine-upload-field .nice-select:focus { border-color: #ff0f78; box-shadow: 0 0 0 3px rgba(255,15,120,.13); }
    .cine-upload-field .nice-select .current { color: #fff; }
    .cine-upload-field .nice-select::after { border-color: #ff72ae; right: 16px; }
    .cine-upload-field .nice-select .list { left: 0; right: 0; width: 100%; max-height: 250px; overflow-y: auto; margin-top: 6px; background: #181035; border: 1px solid rgba(255,255,255,.14); box-shadow: 0 14px 28px rgba(0,0,0,.35); }
    .cine-upload-field .nice-select .option { color: rgba(255,255,255,.82); line-height: 42px; min-height: 42px; padding: 0 14px; }
    .cine-upload-field .nice-select .option:hover, .cine-upload-field .nice-select .option.focus, .cine-upload-field .nice-select .option.selected.focus { background: rgba(255,15,120,.18); color: #fff; }
    .cine-upload-field .nice-select .option.selected { color: #fff; font-weight: 700; }
    .cine-upload-file-picker { display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-top:10px; padding:10px; border:1px solid rgba(255,255,255,.10); border-radius:8px; background:rgba(8,5,23,.38); }
    .cine-upload-file-input { position:absolute; width:1px; height:1px; opacity:0; overflow:hidden; }
    .cine-upload-file-button { display:inline-flex; align-items:center; gap:7px; min-height:38px; padding:8px 12px; cursor:pointer; font-size:12px; }
    .cine-upload-file-name { max-width:100%; overflow:hidden; color:rgba(255,255,255,.58); font-size:12px; text-overflow:ellipsis; white-space:nowrap; }
    .cine-upload-poster-preview { display:none; margin-top:10px; }
    .cine-upload-poster-preview.is-visible { display:block; }
    .cine-upload-poster-preview img { display:block; width:74px; height:94px; object-fit:cover; border:1px solid rgba(255,255,255,.18); border-radius:6px; }
    .cine-upload-multiselect { position: relative; }
    .cine-upload-multiselect-trigger { display:flex; align-items:center; justify-content:space-between; width:100%; min-height:47px; padding:12px 14px; border:1px solid rgba(255,255,255,.15); border-radius:8px; background:rgba(8,5,23,.72); color:#fff; text-align:left; cursor:pointer; transition:border-color .18s, box-shadow .18s; }
    .cine-upload-multiselect-trigger:hover, .cine-upload-multiselect.is-open .cine-upload-multiselect-trigger { border-color:#ff0f78; box-shadow:0 0 0 3px rgba(255,15,120,.13); }
    .cine-upload-multiselect-trigger i { color:#ff72ae; transition:transform .18s; }
    .cine-upload-multiselect.is-open .cine-upload-multiselect-trigger i { transform:rotate(180deg); }
    .cine-upload-multiselect-menu { display:none; position:absolute; z-index:30; top:calc(100% + 6px); left:0; width:100%; max-height:250px; overflow-y:auto; padding:7px; border:1px solid rgba(255,255,255,.14); border-radius:8px; background:#181035; box-shadow:0 14px 28px rgba(0,0,0,.35); }
    .cine-upload-multiselect.is-open .cine-upload-multiselect-menu { display:block; }
    .cine-upload-multiselect-option { display:flex; align-items:center; gap:10px; padding:9px 10px; border-radius:6px; color:rgba(255,255,255,.82); cursor:pointer; font-size:13px; font-weight:500; transition:background .15s, color .15s; }
    .cine-upload-multiselect-option:hover, .cine-upload-multiselect-option.is-selected { background:rgba(255,15,120,.18); color:#fff; }
    .cine-upload-multiselect-option input { position:absolute; width:1px; height:1px; opacity:0; }
    .cine-upload-multiselect-option span:first-of-type { display:inline-flex; align-items:center; justify-content:center; flex:0 0 17px; width:17px; height:17px; border:1px solid rgba(255,255,255,.34); border-radius:4px; color:transparent; font-size:10px; }
    .cine-upload-multiselect-option input:checked + span { border-color:#ff0f78; background:linear-gradient(135deg,#ff0f78,#ff7b00); color:#fff; }
    .cine-upload-help { display: block; margin-top: 7px; color: rgba(255,255,255,.52); font-size: 12px; line-height: 1.5; }
    .cine-upload-toggle-card { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:15px 16px; background:rgba(8,5,23,.54); border:1px solid rgba(255,255,255,.13); border-radius:10px; transition:border-color .18s, background .18s, transform .18s; }
    .cine-upload-toggle-card + .cine-upload-toggle-card { margin-top:10px; }
    .cine-upload-toggle-card:hover { border-color:rgba(255,15,120,.62); transform:translateY(-1px); }
    .cine-upload-toggle-card.is-disabled { opacity:.52; }
    .cine-upload-toggle-copy strong { display:block; color:#fff; font-size:14px; }
    .cine-upload-toggle-copy small { display:block; margin-top:4px; color:rgba(255,255,255,.55); font-size:11px; line-height:1.45; }
    .cine-upload-switch { position:relative; display:inline-flex; flex:0 0 auto; cursor:pointer; }
    .cine-upload-switch input { position:absolute; width:1px; height:1px; opacity:0; pointer-events:none; }
    .cine-upload-switch-track { position:relative; display:block; width:54px; height:30px; border:1px solid rgba(255,255,255,.25); border-radius:999px; background:rgba(255,255,255,.10); transition:background .2s, border-color .2s, box-shadow .2s; }
    .cine-upload-switch-track::after { content:''; position:absolute; top:4px; left:4px; width:20px; height:20px; border-radius:50%; background:rgba(255,255,255,.72); box-shadow:0 2px 6px rgba(0,0,0,.25); transition:transform .2s, background .2s; }
    .cine-upload-switch input:checked + .cine-upload-switch-track { border-color:#ff0f78; background:linear-gradient(90deg,#ff0f78,#ff7b00); box-shadow:0 0 0 4px rgba(255,15,120,.12); }
    .cine-upload-switch input:checked + .cine-upload-switch-track::after { transform:translateX(24px); background:#fff; }
    .cine-upload-switch input:focus-visible + .cine-upload-switch-track { outline:2px solid #ffb000; outline-offset:3px; }
    .cine-upload-switch-state { min-width:30px; margin-left:8px; color:rgba(255,255,255,.50); font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; }
    .cine-upload-switch input:checked ~ .cine-upload-switch-state { color:#ff72ae; }
    .cine-upload-sale-options { display: none; margin-top: 14px; padding: 16px; border: 1px solid rgba(255,15,120,.30); border-radius: 10px; background: rgba(255,15,120,.06); }
    .cine-upload-sale-options.is-visible { display: block; }
    .cine-upload-note { display: flex; gap: 10px; align-items: flex-start; padding: 13px 14px; margin-bottom: 18px; border-radius: 9px; background: rgba(79, 168, 255, .10); border: 1px solid rgba(79, 168, 255, .22); color: rgba(255,255,255,.76); font-size: 12px; line-height: 1.55; }
    .cine-upload-note i { color: #62b4ff; margin-top: 2px; }
    .cine-upload-checklist { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; list-style:none; padding:0; margin:0; }
    .cine-upload-checklist li { display:flex; gap:9px; align-items:flex-start; min-height:62px; padding:11px; border:1px solid rgba(255,255,255,.09); border-radius:8px; background:rgba(8,5,23,.34); color:rgba(255,255,255,.72); font-size:12px; line-height:1.45; }
    .cine-upload-checklist li:last-child { border-bottom:1px solid rgba(255,255,255,.09); padding-bottom:11px; }
    .cine-upload-checklist i { display:inline-flex; align-items:center; justify-content:center; flex:0 0 18px; width:18px; height:18px; border-radius:50%; background:rgba(100,214,154,.12); color:#64d69a; font-size:10px; margin-top:1px; }
    .cine-upload-actions { display: flex; justify-content: flex-end; align-items: center; gap: 12px; margin-top: 22px; }
    .cine-upload-back { display:inline-flex; align-items:center; justify-content:center; gap:7px; min-height:44px; color:#fff !important; font-size:13px; text-decoration:none !important; background:rgba(255,255,255,.08) !important; border:1px solid rgba(255,255,255,.20) !important; }
    .cine-upload-back:hover { background:rgba(255,15,120,.18) !important; border-color:rgba(255,15,120,.68) !important; color:#fff !important; }
    .cine-upload-submit { border: 0; cursor: pointer; }
    .cine-upload-error { margin-bottom: 20px; padding: 14px 16px; border-radius: 9px; background: rgba(229,9,20,.13); border: 1px solid rgba(229,9,20,.42); color: #ffd7da; }
    .cine-upload-error ul { margin: 0; padding-left: 18px; }
    @media (max-width: 900px) { .cine-upload-layout { grid-template-columns: 1fr; } }
    @media (max-width: 560px) { .cine-upload-card { padding: 18px; } .cine-upload-toggle-card { align-items:flex-start; } .cine-upload-hero h1 { font-size: 26px; } .cine-upload-actions { justify-content: stretch; flex-direction: column-reverse; } .cine-upload-actions > * { width: 100%; text-align: center; } .cine-upload-checklist { grid-template-columns: 1fr; } }
</style>

<div class="cine-upload-page">
    <div class="breadcrumb-section bg-xs" style="background-image:url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
        <div class="container-fluid">
            <div class="row"><div class="col-xl-12">
                <h2>Upload Film</h2>
                <nav id="breadcrumbs"><ul><li><a href="{{ URL::to('/') }}">Home</a></li><li><a href="{{ URL::to('dashboard') }}">Dashboard</a></li><li>Upload Film</li></ul></nav>
            </div></div>
        </div>
    </div>

    <div class="vfx-item-ptb">
        <div class="container-fluid">
            <div class="cine-upload-hero">
                <h1>Share your film with Cineworm</h1>
                <p>Submit the film details and source below. Our team will review it before it goes live.</p>
            </div>

            @if($errors->any())
                <div class="cine-upload-error"><strong>Please check the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <form method="POST" action="{{ URL::to('user/films/store') }}" id="movie_form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="creator_currency" value="{{ getcong('currency_code') ?: 'USD' }}">

                <div class="cine-upload-layout">
                    <main>
                        <section class="cine-upload-card">
                            <h3 class="cine-upload-section-title"><i class="fa fa-film"></i> Film details <span></span></h3>
                            <div class="cine-upload-field"><label for="video_title">Film title <em>*</em></label><input id="video_title" name="video_title" value="{{ old('video_title') }}" maxlength="255" required placeholder="Enter the title of your film"><small class="cine-upload-help">Use the official title viewers should see.</small></div>
                            <div class="cine-upload-field"><label for="elm1">Description <em>*</em></label><textarea id="elm1" name="video_description" required placeholder="Tell viewers what this film is about...">{{ old('video_description') }}</textarea></div>
                            <div class="row">
                                @php($selected_genres = (array) old('genres', []))
                                <div class="col-md-6 cine-upload-field"><label for="movie_language">Language <em>*</em></label><select class="selectpicker show-menu-arrow wide" id="movie_language" name="movie_language" required><option value="">Select language</option>@foreach($language_list as $lang)<option value="{{ $lang->id }}" @selected(old('movie_language') == $lang->id)>{{ $lang->language_name }}</option>@endforeach</select></div>
                                <div class="col-md-6 cine-upload-field"><label for="genre_picker_trigger">Genre(s) <em>*</em></label><div class="cine-upload-multiselect" id="genre_picker"><button type="button" class="cine-upload-multiselect-trigger" id="genre_picker_trigger" aria-expanded="false"><span data-genre-label>Select genre(s)</span><i class="fa fa-chevron-down"></i></button><div class="cine-upload-multiselect-menu" role="listbox">@foreach($genre_list as $genre)<label class="cine-upload-multiselect-option"><input type="checkbox" name="genres[]" value="{{ $genre->id }}" @checked(in_array((string) $genre->id, array_map('strval', $selected_genres), true))><span><i class="fa fa-check"></i></span><span>{{ $genre->genre_name }}</span></label>@endforeach</div></div><small class="cine-upload-help">Select at least one genre.</small></div>
                            </div>
                        </section>

                        <section class="cine-upload-card">
                            <h3 class="cine-upload-section-title"><i class="fa fa-shield"></i> Rights and access <span></span></h3>
                            <div class="cine-upload-field"><label>Film ownership <em>*</em></label>
                                <div class="cine-upload-toggle-card"><span class="cine-upload-toggle-copy"><strong>I own this film</strong><small>Turn this on only if you own the rights to the film.</small></span><label class="cine-upload-switch" for="is_owner_toggle"><input type="hidden" name="is_owner" value="0"><input type="checkbox" name="is_owner" id="is_owner_toggle" value="1" @checked((string) old('is_owner', '0') === '1')><span class="cine-upload-switch-track"></span><span class="cine-upload-switch-state">Off</span></label></div>
                            </div>
                            <div class="cine-upload-field"><label>Viewer access <em>*</em></label>
                                <div class="cine-upload-toggle-card"><span class="cine-upload-toggle-copy"><strong>Paid film</strong><small>Turn on film ownership first, then enable this to set the viewer price below. Admin approval is required.</small></span><label class="cine-upload-switch" for="creator_sale_toggle"><input type="hidden" name="creator_sale_enabled" value="0"><input type="checkbox" name="creator_sale_enabled" id="creator_sale_toggle" value="1" @checked((string) old('creator_sale_enabled', '0') === '1')><span class="cine-upload-switch-track"></span><span class="cine-upload-switch-state">Off</span></label></div>
                            <div id="creator_sale_options" class="cine-upload-sale-options">
                                <div class="cine-upload-field"><label for="creator_price">Viewer price <em>*</em></label><input type="number" id="creator_price" name="creator_price" min="0.01" step="0.01" value="{{ old('creator_price') }}" placeholder="e.g. 4.99"><small class="cine-upload-help">Currency: {{ getcong('currency_code') ?: 'USD' }}. Paid films must use a public Google Drive source.</small></div>
                                <div class="cine-upload-field"><label for="creator_trailer_url">Free trailer URL</label><input type="url" id="creator_trailer_url" name="creator_trailer_url" value="{{ old('creator_trailer_url') }}" placeholder="https://youtube.com/... or https://vimeo.com/..."><small class="cine-upload-help">Optional preview viewers can watch before purchase.</small></div>
                            </div></div>
                        </section>
                    </main>

                    <aside>
                        <section class="cine-upload-card">
                            <h3 class="cine-upload-section-title"><i class="fa fa-link"></i> Video source <span></span></h3>
                            <div class="cine-upload-note"><i class="fa fa-info-circle"></i><span>Use a source link that the admin team can access. Paid films must be public Google Drive files.</span></div>
                            <div class="cine-upload-field"><label for="video_type">Source type <em>*</em></label><select class="selectpicker show-menu-arrow wide" id="video_type" name="video_type" required><option value="">Select source type</option><option value="URL" @selected(old('video_type') === 'URL')>Direct MP4 URL</option><option value="GoogleDrive" @selected(old('video_type') === 'GoogleDrive')>Google Drive</option><option value="HLS" @selected(old('video_type') === 'HLS')>HLS / YouTube / Vimeo</option></select></div>
                            <div class="cine-upload-field"><label for="video_url">Video URL <em>*</em></label><input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}" required placeholder="https://..."><small class="cine-upload-help">Make sure the link is active before submitting.</small></div>
                            <div class="cine-upload-field"><label for="poster_link">Poster URL</label><input type="url" id="poster_link" name="poster_link" value="{{ old('poster_link') }}" placeholder="https://example.com/poster.jpg"><div class="cine-upload-file-picker"><label for="poster_file" class="vfx-item-btn-danger text-uppercase cine-upload-file-button"><i class="fa fa-upload"></i> Upload poster</label><span id="poster_file_name" class="cine-upload-file-name">No file selected</span><input id="poster_file" name="poster_file" class="cine-upload-file-input" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><div id="poster_preview" class="cine-upload-poster-preview"><img id="poster_preview_image" src="" alt="Poster preview"></div></div><small class="cine-upload-help">Optional. Use a public URL or upload JPG, PNG or WebP (maximum 5 MB). If both are provided, the uploaded poster is used.</small></div>
                        </section>
                        <section class="cine-upload-card">
                            <h3 class="cine-upload-section-title"><i class="fa fa-check-circle"></i> Before you submit <span></span></h3>
                            <ul class="cine-upload-checklist"><li><i class="fa fa-check"></i><span>Title, description, language and genre are complete.</span></li><li><i class="fa fa-check"></i><span>The video link is working and accessible.</span></li><li><i class="fa fa-check"></i><span>You have permission to share the film.</span></li><li><i class="fa fa-clock-o"></i><span>New submissions are reviewed before going live.</span></li></ul>
                        </section>
                    </aside>
                </div>
                <div class="cine-upload-actions"><a class="vfx-item-btn-danger text-uppercase cine-upload-back" href="{{ URL::to('user/films') }}"><i class="fa fa-arrow-left"></i> Cancel and go back</a><button type="submit" class="vfx-item-btn-danger text-uppercase cine-upload-submit"><i class="fa fa-paper-plane"></i> Submit film for review</button></div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var saleToggle = document.getElementById('creator_sale_toggle');
    var ownerToggle = document.getElementById('is_owner_toggle');
    var saleBox = document.getElementById('creator_sale_options');
    var price = document.getElementById('creator_price');
    var saleCard = saleToggle.closest('.cine-upload-toggle-card');
    var saleState = saleToggle.parentElement.querySelector('.cine-upload-switch-state');
    var ownerState = ownerToggle.parentElement.querySelector('.cine-upload-switch-state');
    var posterFile = document.getElementById('poster_file');
    var posterFileName = document.getElementById('poster_file_name');
    var posterPreview = document.getElementById('poster_preview');
    var posterPreviewImage = document.getElementById('poster_preview_image');
    posterFile.addEventListener('change', function () {
        var file = posterFile.files && posterFile.files[0];
        if (!file) {
            posterFileName.textContent = 'No file selected';
            posterPreview.classList.remove('is-visible');
            posterPreviewImage.removeAttribute('src');
            return;
        }
        posterFileName.textContent = file.name;
        posterPreviewImage.src = URL.createObjectURL(file);
        posterPreview.classList.add('is-visible');
    });
    var genrePicker = document.getElementById('genre_picker');
    var genreTrigger = document.getElementById('genre_picker_trigger');
    var genreLabel = genrePicker.querySelector('[data-genre-label]');
    var genreOptions = Array.prototype.slice.call(genrePicker.querySelectorAll('input[type="checkbox"]'));
    function updateGenrePicker() {
        var selected = genreOptions.filter(function (input) { return input.checked; });
        genreLabel.textContent = selected.length ? selected.map(function (input) { return input.parentElement.querySelector('span:last-child').textContent.trim(); }).join(', ') : 'Select genre(s)';
        genreOptions.forEach(function (input) { input.parentElement.classList.toggle('is-selected', input.checked); });
    }
    genreTrigger.addEventListener('click', function () {
        var isOpen = genrePicker.classList.toggle('is-open');
        genreTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    genreOptions.forEach(function (input) { input.addEventListener('change', updateGenrePicker); });
    document.addEventListener('click', function (event) {
        if (!genrePicker.contains(event.target)) {
            genrePicker.classList.remove('is-open');
            genreTrigger.setAttribute('aria-expanded', 'false');
        }
    });
    updateGenrePicker();
    function toggleSale() {
        saleToggle.disabled = !ownerToggle.checked;
        saleCard.classList.toggle('is-disabled', saleToggle.disabled);
        if (saleToggle.disabled) {
            saleToggle.checked = false;
        }
        var paid = saleToggle.checked && ownerToggle.checked;
        if (saleToggle.checked && !ownerToggle.checked) {
            saleToggle.checked = false;
            paid = false;
        }
        saleBox.classList.toggle('is-visible', paid);
        price.required = paid;
        saleState.textContent = saleToggle.checked ? 'On' : 'Off';
        ownerState.textContent = ownerToggle.checked ? 'On' : 'Off';
    }
    saleToggle.addEventListener('change', toggleSale);
    ownerToggle.addEventListener('change', toggleSale);
    toggleSale();
});
</script>
@endsection
