@extends('site_app')

@section('head_title', 'Upload Film | ' . getcong('site_name'))
@section('head_url', Request::url())

@section('content')
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
    .cine-upload-field input:not([type="radio"]), .cine-upload-field select, .cine-upload-field textarea {
        width: 100%; color: #fff; background: rgba(8,5,23,.72); border: 1px solid rgba(255,255,255,.15); border-radius: 8px; padding: 12px 14px; outline: none; transition: border-color .18s, box-shadow .18s;
    }
    .cine-upload-field textarea { min-height: 128px; resize: vertical; }
    .cine-upload-field input:not([type="radio"]):focus, .cine-upload-field select:focus, .cine-upload-field textarea:focus { border-color: #ff0f78; box-shadow: 0 0 0 3px rgba(255,15,120,.13); }
    .cine-upload-field select option { background: #181035; color: #fff; }
    .cine-upload-help { display: block; margin-top: 7px; color: rgba(255,255,255,.52); font-size: 12px; line-height: 1.5; }
    .cine-upload-choice-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px; }
    .cine-upload-choice { position: relative; display: flex; align-items: center; gap: 12px; min-height: 66px; padding: 13px 15px; cursor: pointer; background: rgba(8,5,23,.54); border: 1px solid rgba(255,255,255,.13); border-radius: 10px; transition: border-color .18s, background .18s, transform .18s; }
    .cine-upload-choice:hover { border-color: rgba(255,15,120,.65); transform: translateY(-1px); }
    .cine-upload-choice input { position: absolute; opacity: 0; pointer-events: none; }
    .cine-upload-choice-mark { display: grid; place-items: center; width: 20px; height: 20px; flex: 0 0 20px; border: 2px solid rgba(255,255,255,.42); border-radius: 50%; }
    .cine-upload-choice-mark::after { content: ''; width: 8px; height: 8px; border-radius: 50%; background: #fff; transform: scale(0); transition: transform .18s; }
    .cine-upload-choice-copy strong { display: block; font-size: 14px; color: #fff; }
    .cine-upload-choice-copy small { display: block; margin-top: 3px; color: rgba(255,255,255,.55); font-size: 11px; }
    .cine-upload-choice input:checked + .cine-upload-choice-mark { border-color: #ff0f78; background: #ff0f78; box-shadow: 0 0 0 4px rgba(255,15,120,.12); }
    .cine-upload-choice input:checked + .cine-upload-choice-mark::after { transform: scale(1); }
    .cine-upload-choice:has(input:checked) { background: linear-gradient(135deg, rgba(255,15,120,.18), rgba(255,126,0,.08)); border-color: rgba(255,15,120,.82); }
    .cine-upload-sale-options { display: none; margin-top: 14px; padding: 16px; border: 1px solid rgba(255,15,120,.30); border-radius: 10px; background: rgba(255,15,120,.06); }
    .cine-upload-sale-options.is-visible { display: block; }
    .cine-upload-note { display: flex; gap: 10px; align-items: flex-start; padding: 13px 14px; margin-bottom: 18px; border-radius: 9px; background: rgba(79, 168, 255, .10); border: 1px solid rgba(79, 168, 255, .22); color: rgba(255,255,255,.76); font-size: 12px; line-height: 1.55; }
    .cine-upload-note i { color: #62b4ff; margin-top: 2px; }
    .cine-upload-checklist { list-style: none; padding: 0; margin: 0; }
    .cine-upload-checklist li { display: flex; gap: 10px; align-items: flex-start; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,.08); color: rgba(255,255,255,.70); font-size: 13px; line-height: 1.45; }
    .cine-upload-checklist li:last-child { border-bottom: 0; padding-bottom: 0; }
    .cine-upload-checklist i { color: #64d69a; margin-top: 3px; }
    .cine-upload-actions { display: flex; justify-content: flex-end; align-items: center; gap: 12px; margin-top: 22px; }
    .cine-upload-back { color: rgba(255,255,255,.68) !important; font-size: 13px; text-decoration: none !important; }
    .cine-upload-submit { border: 0; cursor: pointer; }
    .cine-upload-error { margin-bottom: 20px; padding: 14px 16px; border-radius: 9px; background: rgba(229,9,20,.13); border: 1px solid rgba(229,9,20,.42); color: #ffd7da; }
    .cine-upload-error ul { margin: 0; padding-left: 18px; }
    @media (max-width: 900px) { .cine-upload-layout { grid-template-columns: 1fr; } }
    @media (max-width: 560px) { .cine-upload-card { padding: 18px; } .cine-upload-choice-grid { grid-template-columns: 1fr; } .cine-upload-hero h1 { font-size: 26px; } .cine-upload-actions { justify-content: stretch; flex-direction: column-reverse; } .cine-upload-actions > * { width: 100%; text-align: center; } }
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

            <form method="POST" action="{{ URL::to('user/films/store') }}" id="movie_form">
                @csrf
                <input type="hidden" name="creator_currency" value="{{ getcong('currency_code') ?: 'USD' }}">

                <div class="cine-upload-layout">
                    <main>
                        <section class="cine-upload-card">
                            <h3 class="cine-upload-section-title"><i class="fa fa-film"></i> Film details <span></span></h3>
                            <div class="cine-upload-field"><label for="video_title">Film title <em>*</em></label><input id="video_title" name="video_title" value="{{ old('video_title') }}" maxlength="255" required placeholder="Enter the title of your film"><small class="cine-upload-help">Use the official title viewers should see.</small></div>
                            <div class="cine-upload-field"><label for="elm1">Description <em>*</em></label><textarea id="elm1" name="video_description" required placeholder="Tell viewers what this film is about...">{{ old('video_description') }}</textarea></div>
                            <div class="row">
                                <div class="col-md-6 cine-upload-field"><label for="movie_language">Language <em>*</em></label><select id="movie_language" name="movie_language" required><option value="">Select language</option>@foreach($language_list as $lang)<option value="{{ $lang->id }}" @selected(old('movie_language') == $lang->id)>{{ $lang->language_name }}</option>@endforeach</select></div>
                                <div class="col-md-6 cine-upload-field"><label for="movie_genre_id">Genre(s) <em>*</em></label><select id="movie_genre_id" name="genres[]" multiple required>@foreach($genre_list as $genre)<option value="{{ $genre->id }}" @selected(is_array(old('genres')) && in_array($genre->id, old('genres')))>{{ $genre->genre_name }}</option>@endforeach</select><small class="cine-upload-help">Select at least one genre.</small></div>
                            </div>
                        </section>

                        <section class="cine-upload-card">
                            <h3 class="cine-upload-section-title"><i class="fa fa-shield"></i> Rights and access <span></span></h3>
                            <div class="cine-upload-field"><label>Are you the film owner? <em>*</em></label><div class="cine-upload-choice-grid">
                                <label class="cine-upload-choice"><input type="radio" name="is_owner" id="is_owner_yes" value="1" @checked(old('is_owner') === '1') required><span class="cine-upload-choice-mark"></span><span class="cine-upload-choice-copy"><strong>Yes, I own it</strong><small>You can request paid access for viewers.</small></span></label>
                                <label class="cine-upload-choice"><input type="radio" name="is_owner" id="is_owner_no" value="0" @checked(old('is_owner', '0') !== '1')><span class="cine-upload-choice-mark"></span><span class="cine-upload-choice-copy"><strong>No, I am sharing it</strong><small>This film will stay free to watch.</small></span></label>
                            </div></div>
                            <div class="cine-upload-field"><label>How should viewers access it? <em>*</em></label><div class="cine-upload-choice-grid">
                                <label class="cine-upload-choice"><input type="radio" name="creator_sale_enabled" id="creator_sale_yes" value="1" @checked(old('creator_sale_enabled') === '1')><span class="cine-upload-choice-mark"></span><span class="cine-upload-choice-copy"><strong>Paid film</strong><small>Owner approval and payment required.</small></span></label>
                                <label class="cine-upload-choice"><input type="radio" name="creator_sale_enabled" id="creator_sale_no" value="0" @checked(old('creator_sale_enabled', '0') !== '1')><span class="cine-upload-choice-mark"></span><span class="cine-upload-choice-copy"><strong>Free film</strong><small>Available after normal admin review.</small></span></label>
                            </div>
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
                            <div class="cine-upload-field"><label for="video_type">Source type <em>*</em></label><select id="video_type" name="video_type" required><option value="">Select source type</option><option value="URL" @selected(old('video_type') === 'URL')>Direct MP4 URL</option><option value="GoogleDrive" @selected(old('video_type') === 'GoogleDrive')>Google Drive</option><option value="HLS" @selected(old('video_type') === 'HLS')>HLS / YouTube / Vimeo</option></select></div>
                            <div class="cine-upload-field"><label for="video_url">Video URL <em>*</em></label><input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}" required placeholder="https://..."><small class="cine-upload-help">Make sure the link is active before submitting.</small></div>
                            <div class="cine-upload-field"><label for="poster_link">Poster URL</label><input type="url" id="poster_link" name="poster_link" value="{{ old('poster_link') }}" placeholder="https://example.com/poster.jpg"><small class="cine-upload-help">Optional. YouTube/Vimeo thumbnails can be detected automatically.</small></div>
                        </section>
                        <section class="cine-upload-card">
                            <h3 class="cine-upload-section-title"><i class="fa fa-check-circle"></i> Before you submit <span></span></h3>
                            <ul class="cine-upload-checklist"><li><i class="fa fa-check"></i><span>Title, description, language and genre are complete.</span></li><li><i class="fa fa-check"></i><span>The video link is working and accessible.</span></li><li><i class="fa fa-check"></i><span>You have permission to share the film.</span></li><li><i class="fa fa-clock-o"></i><span>New submissions are reviewed before going live.</span></li></ul>
                        </section>
                    </aside>
                </div>
                <div class="cine-upload-actions"><a class="cine-upload-back" href="{{ URL::to('user/films') }}">Cancel and go back</a><button type="submit" class="vfx-item-btn-danger text-uppercase cine-upload-submit"><i class="fa fa-paper-plane"></i> Submit film for review</button></div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var saleYes = document.getElementById('creator_sale_yes');
    var ownerNo = document.getElementById('is_owner_no');
    var saleBox = document.getElementById('creator_sale_options');
    var price = document.getElementById('creator_price');
    function toggleSale() {
        var paid = saleYes.checked && !ownerNo.checked;
        saleBox.classList.toggle('is-visible', paid);
        price.required = paid;
        if (saleYes.checked && ownerNo.checked) {
            document.getElementById('creator_sale_no').checked = true;
            saleBox.classList.remove('is-visible');
            price.required = false;
        }
    }
    document.querySelectorAll('input[name="creator_sale_enabled"], input[name="is_owner"]').forEach(function (input) { input.addEventListener('change', toggleSale); });
    toggleSale();
});
</script>
@endsection
