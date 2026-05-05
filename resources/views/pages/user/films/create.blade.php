@extends('site_app')

@section('head_title', 'Upload Film | '.getcong('site_name'))
@section('head_url', Request::url())

@section('content')
<style>
/* ── Film Upload Dark Theme ───────────────────────────────────────────────── */
.fu-wrap { max-width: 1100px; margin: 0 auto; padding-bottom: 50px; }

.fu-back-bar {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
}
.fu-back-link {
    display: inline-flex; align-items: center; gap: 8px;
    color: rgba(255,255,255,0.55) !important; font-size: 13px; font-weight: 600;
    text-decoration: none; transition: color 0.18s;
}
.fu-back-link:hover { color: #ff0f28 !important; }

.fu-panel {
    background: rgba(18,18,22,0.97);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 16px;
    padding: 28px 32px;
    margin-bottom: 22px;
}
.fu-panel-title {
    font-size: 16px; font-weight: 700; color: #fff;
    margin: 0 0 22px; display: flex; align-items: center; gap: 10px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.fu-panel-title i { color: #ff0f28; font-size: 17px; }

.fu-label {
    display: block; font-size: 11.5px; font-weight: 700; letter-spacing: 0.06em;
    text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 7px;
}
.fu-input,
.fu-select,
.fu-textarea {
    width: 100% !important;
    background: #1e1e24 !important;
    background-color: #1e1e24 !important;
    border: 1px solid rgba(255,255,255,0.13) !important;
    border-radius: 10px !important;
    color: #ffffff !important;
    font-size: 14px !important;
    padding: 12px 16px !important;
    transition: border-color 0.18s ease, box-shadow 0.18s ease !important;
    box-sizing: border-box !important;
    outline: none !important;
    box-shadow: none !important;
}

.fu-input:focus, .fu-input:active,
.fu-select:focus, .fu-select:active,
.fu-textarea:focus, .fu-textarea:active {
    border-color: #ff0f28 !important;
    background: #1e1e24 !important;
    background-color: #1e1e24 !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(255,15,40,0.18) !important;
    outline: none !important;
}

.fu-input:-webkit-autofill,
.fu-input:-webkit-autofill:hover,
.fu-input:-webkit-autofill:focus,
.fu-select:-webkit-autofill,
.fu-textarea:-webkit-autofill {
    -webkit-box-shadow: 0 0 0 1000px #1e1e24 inset !important;
    -webkit-text-fill-color: #ffffff !important;
    caret-color: #ffffff !important;
    border-color: rgba(255,255,255,0.13) !important;
}

.fu-input::placeholder, .fu-textarea::placeholder { color: rgba(255,255,255,0.3) !important; }

/* ── Select: closed dropdown with arrow (exactly like promo-select) ─────── */
.fu-select {
    height: 46px !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' fill='none'%3E%3Cpath d='M1 1l5 5 5-5' stroke='rgba(255,255,255,0.5)' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 16px center !important;
    background-color: #1e1e24 !important;
    padding-right: 42px !important;
    cursor: pointer !important;
}

.fu-select option { background: #1a1a1e; color: #fff; }

/* ── Multi-select overrides back to auto height, no arrow ───────────────── */
select.fu-select[multiple] {
    height: auto !important;
    min-height: 160px !important;
    background-image: none !important;
    padding-right: 16px !important;
}

.fu-textarea { min-height: 110px; resize: vertical; }

/* ── Radio toggle pills ──────────────────────────────────────────────────── */
.fu-radio-group { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 6px; }
.fu-radio-group input[type=radio] { display: none; }
.fu-radio-group label {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 18px;
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 9px;
    color: rgba(255,255,255,0.55);
    font-size: 13px; font-weight: 600;
    cursor: pointer;
    transition: all 0.18s ease;
    background: rgba(255,255,255,0.03);
    user-select: none;
}
.fu-radio-group label:before {
    content: '';
    display: inline-block;
    width: 8px; height: 8px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.25);
    background: transparent;
    transition: all 0.18s ease;
    flex-shrink: 0;
}
.fu-radio-group input[type=radio]:checked + label {
    border-color: #ff0f28;
    background: rgba(255,15,40,0.1);
    color: #fff;
}
.fu-radio-group input[type=radio]:checked + label:before {
    background: #ff0f28;
    border-color: #ff0f28;
    box-shadow: 0 0 0 2px rgba(255,15,40,0.3);
}

.fu-hint {
    font-size: 12px; color: rgba(255,255,255,0.35);
    margin: 5px 0 0; line-height: 1.5;
}
.fu-fg { margin-bottom: 18px; }

.fu-info-box {
    display: flex; gap: 10px; align-items: flex-start;
    background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.22);
    border-radius: 10px; padding: 12px 16px;
    color: rgba(255,255,255,0.7); font-size: 13px; margin-bottom: 22px;
}
.fu-info-box i { color: #60a5fa; margin-top: 2px; flex-shrink: 0; }

.fu-btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 30px;
    background: linear-gradient(135deg,#ff0f28,#c8001f);
    border: none; border-radius: 10px; color: #fff !important;
    font-size: 14px; font-weight: 700; cursor: pointer;
    box-shadow: 0 4px 14px rgba(255,15,40,0.3);
    transition: all 0.18s ease;
}
.fu-btn-primary:hover {
    background: linear-gradient(135deg,#ff2e44,#e0001f);
    box-shadow: 0 6px 20px rgba(255,15,40,0.46);
    transform: translateY(-1px);
    color: #fff !important;
}
.fu-btn-ghost {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 11px 22px;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 10px; color: rgba(255,255,255,0.7) !important;
    font-size: 14px; font-weight: 600; cursor: pointer;
    text-decoration: none; transition: all 0.18s ease;
}
.fu-btn-ghost:hover {
    background: rgba(255,255,255,0.06);
    border-color: rgba(255,255,255,0.36);
    color: #fff !important;
}
.fu-btn-sm {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 14px; font-size: 12px; font-weight: 600;
    border-radius: 7px; cursor: pointer; border: none;
    transition: all 0.15s ease;
}
.fu-btn-blue { background: #3b82f6; color: #fff; }
.fu-btn-blue:hover { background: #2563eb; color: #fff; }
.fu-btn-amber { background: #f59e0b; color: #fff; }
.fu-btn-amber:hover { background: #d97706; color: #fff; }

.fu-section-divider { border: none; border-top: 1px solid rgba(255,255,255,0.06); margin: 22px 0; }

.fu-pending-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 14px; border-radius: 999px;
    background: rgba(245,158,11,0.15); color: #f59e0b;
    font-size: 12px; font-weight: 700;
}
</style>

<div class="breadcrumb-section bg-xs" style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
    <div class="container-fluid"><div class="row"><div class="col-xl-12">
        <h2>Upload Film</h2>
        <nav id="breadcrumbs"><ul>
            <li><a href="{{ URL::to('/') }}">Home</a></li>
            <li><a href="{{ URL::to('dashboard') }}">Dashboard</a></li>
            <li><a href="{{ URL::to('user/films') }}">My Films</a></li>
            <li>Upload</li>
        </ul></nav>
    </div></div></div>
</div>

<div class="vfx-item-ptb vfx-item-info">
<div class="container-fluid">
<div class="fu-wrap">

    <div class="fu-back-bar">
        <a href="{{ URL::to('user/films') }}" class="fu-back-link">
            <i class="fa fa-arrow-left"></i> Back to My Films
        </a>
        <span class="fu-pending-badge">
            <i class="fa fa-clock-o"></i> Submitted films require admin approval before going live
        </span>
    </div>

    <div class="fu-info-box">
        <i class="fa fa-info-circle"></i>
        <span>All fields are identical to the admin upload panel. Your film will be set to <strong>Pending Review</strong> until approved. Make sure your video URL is publicly accessible.</span>
    </div>

    <form method="POST" action="{{ URL::to('user/films/store') }}" id="movie_form" name="movie_form">
        @csrf

        <div class="row">
            {{-- ── LEFT COL ── --}}
            <div class="col-md-6">

                {{-- Film Info --}}
                <div class="fu-panel">
                    <h4 class="fu-panel-title"><i class="fa fa-film"></i> Film Information</h4>

                    <div class="fu-fg">
                        <label class="fu-label">Film Title *</label>
                        <input type="text" name="video_title" id="video_title" class="fu-input"
                               value="{{ old('video_title') }}" placeholder="Enter film title">
                    </div>

                    <div class="fu-fg">
                        <label class="fu-label">Description</label>
                        <textarea name="video_description" id="elm1" rows="5" class="fu-textarea"
                                  placeholder="Film synopsis…">{{ old('video_description') }}</textarea>
                    </div>

                    <div class="fu-fg">
                        <label class="fu-label">Upcoming?</label>
                        <select name="upcoming" id="upcoming" class="fu-select form-control">
                            <option value="0">No — release now</option>
                            <option value="1">Yes — upcoming film</option>
                        </select>
                        <p class="fu-hint">Upcoming films show on the home page only.</p>
                    </div>

                    <div class="fu-fg">
                        <label class="fu-label">Are you the owner? *</label>
                        <div class="fu-radio-group">
                            <input type="radio" name="is_owner" id="is_owner_yes" value="1">
                            <label for="is_owner_yes">Yes, I am the owner</label>
                            <input type="radio" name="is_owner" id="is_owner_no" value="0" checked>
                            <label for="is_owner_no">No, sharing someone else's work</label>
                        </div>
                        <p class="fu-hint">Owners receive digital awards based on likes.</p>
                    </div>

                    <div class="fu-fg">
                        <label class="fu-label">Language *</label>
                        <select name="movie_language" id="movie_language" class="fu-select form-control">
                            <option value="">— Select Language —</option>
                            @foreach($language_list as $lang)
                                <option value="{{ $lang->id }}" @if(old('movie_language') == $lang->id) selected @endif>
                                    {{ $lang->language_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="fu-fg">
                        <label class="fu-label">Genre(s) *</label>
                        <select name="genres[]" id="movie_genre_id" class="fu-select" multiple>
                            @foreach($genre_list as $g)
                                <option value="{{ $g->id }}" @if(is_array(old('genres')) && in_array($g->id, old('genres'))) selected @endif>
                                    {{ $g->genre_name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="fu-hint">Hold Ctrl / ⌘ to select multiple.</p>
                    </div>

                    <div class="fu-fg">
                        <label class="fu-label">Actors <span style="color:rgba(255,255,255,0.3);font-weight:400;">(comma separated)</span></label>
                        <input type="text" name="actors" id="actors" class="fu-input"
                               value="{{ old('actors') }}" placeholder="e.g. John Doe, Jane Smith">
                    </div>

                    <div class="fu-fg">
                        <label class="fu-label">Directors <span style="color:rgba(255,255,255,0.3);font-weight:400;">(comma separated)</span></label>
                        <input type="text" name="director" id="director" class="fu-input"
                               value="{{ old('director') }}" placeholder="e.g. James Cameron">
                    </div>

                    <div class="fu-fg">
                        <label class="fu-label">Funding URL</label>
                        <input type="text" name="funding_url" id="funding_url" class="fu-input"
                               value="{{ old('funding_url') }}" placeholder="https://kickstarter.com/…">
                    </div>

                    <div class="fu-fg" style="margin-bottom:0;">
                        <label class="fu-label">Project Website URL</label>
                        <input type="text" name="webpage_url" id="webpage_url" class="fu-input"
                               value="{{ old('webpage_url') }}" placeholder="https://myfilm.com">
                    </div>
                </div>

            </div>

            {{-- ── RIGHT COL ── --}}
            <div class="col-md-6">

                {{-- Poster --}}
                <div class="fu-panel">
                    <h4 class="fu-panel-title"><i class="fa fa-image"></i> Poster / Thumbnail</h4>

                    <div class="fu-fg" style="margin-bottom:0;">
                        <label class="fu-label">Poster URL <span style="color:rgba(255,255,255,0.3);font-weight:400;">(optional)</span></label>
                        <input type="text" name="poster_link" id="poster_link" class="fu-input"
                               value="{{ old('poster_link') }}" placeholder="https://example.com/poster.jpg">
                        <p class="fu-hint">Leave blank — thumbnail auto-fetched for YouTube & Vimeo.</p>
                    </div>
                </div>

                {{-- Video Source --}}
                <div class="fu-panel" id="hide_when_upcoming">
                    <h4 class="fu-panel-title"><i class="fa fa-play-circle"></i> Video Source</h4>

                    <div class="fu-fg">
                        <label class="fu-label">Video Upload Type</label>
                        <select name="video_type" id="video_type" class="fu-select form-control">
                            <option value="" disabled selected>Select video type</option>
                            <option value="URL"   @if(old('video_type')=='URL')   selected @endif>URL (Direct MP4)</option>
                            <option value="HLS"   @if(old('video_type')=='HLS')   selected @endif>HLS / m3u8 / MPEG-DASH / YouTube / Vimeo</option>
                            <option value="Embed" @if(old('video_type')=='Embed') selected @endif>Embed Code</option>
                        </select>
                    </div>

                    <div class="fu-fg">
                        <label class="fu-label">Video Quality (480p / 720p / 1080p)</label>
                        <div class="fu-radio-group">
                            <input type="radio" name="video_quality" id="vq_active" value="1">
                            <label for="vq_active">Active</label>
                            <input type="radio" name="video_quality" id="vq_inactive" value="0" checked>
                            <label for="vq_inactive">Inactive</label>
                        </div>
                    </div>

                    {{-- URL inputs --}}
                    <div id="url_id">
                        <p class="fu-hint" style="margin-bottom:10px;">Supported: MP4 URL. External files must be CORS-enabled.</p>
                        <div class="fu-fg">
                            <label class="fu-label">Video URL (Default)</label>
                            <input type="text" name="video_url" class="fu-input"
                                   value="{{ old('video_url') }}" placeholder="http://example.com/demo.mp4">
                        </div>
                        <div class="fu-fg">
                            <label class="fu-label">Video URL 480p</label>
                            <input type="text" name="video_url_480" class="fu-input"
                                   value="{{ old('video_url_480') }}" placeholder="http://example.com/demo480.mp4">
                        </div>
                        <div class="fu-fg">
                            <label class="fu-label">Video URL 720p</label>
                            <input type="text" name="video_url_720" class="fu-input"
                                   value="{{ old('video_url_720') }}" placeholder="http://example.com/demo720.mp4">
                        </div>
                        <div class="fu-fg" style="margin-bottom:0;">
                            <label class="fu-label">Video URL 1080p</label>
                            <input type="text" name="video_url_1080" class="fu-input"
                                   value="{{ old('video_url_1080') }}" placeholder="http://example.com/demo1080.mp4">
                        </div>
                    </div>

                    {{-- Embed --}}
                    <div id="embed_id" style="display:none;">
                        <div class="fu-fg" style="margin-bottom:0;">
                            <label class="fu-label">Embed Code</label>
                            <textarea name="video_embed_code" class="fu-textarea" rows="5"
                                      placeholder="&lt;iframe src=&quot;…&quot;&gt;&lt;/iframe&gt;">{{ old('video_embed_code') }}</textarea>
                        </div>
                    </div>

                    {{-- HLS --}}
                    <div id="hls_id" style="display:none;">
                        <p class="fu-hint" style="margin-bottom:10px;">Supported: MP4, YouTube, Vimeo, HLS/m3u8. External files must be CORS-enabled.</p>
                        <div class="fu-fg" style="margin-bottom:0;">
                            <label class="fu-label">HLS Streaming URL</label>
                            <input type="text" name="video_url_hls" class="fu-input"
                                   value="{{ old('video_url_hls') }}" placeholder="http://example.com/test.m3u8">
                        </div>
                    </div>

                </div>

                {{-- Subtitles --}}
                <div class="fu-panel">
                    <h4 class="fu-panel-title"><i class="fa fa-cc"></i> Subtitles</h4>
                    <p class="fu-hint" style="margin-bottom:16px;">Supported: .srt or .vtt file URLs only. External files must be CORS-enabled.</p>

                    <div class="fu-fg">
                        <label class="fu-label">Subtitles</label>
                        <div class="fu-radio-group">
                            <input type="radio" name="subtitle_on_off" id="inlineRadio5" value="1">
                            <label for="inlineRadio5">Active</label>
                            <input type="radio" name="subtitle_on_off" id="inlineRadio6" value="0" checked>
                            <label for="inlineRadio6">Inactive</label>
                        </div>
                    </div>

                    @foreach([['1','English'],['2','French'],['3','Spanish']] as [$n, $placeholder])
                    <hr class="fu-section-divider">
                    <div class="fu-fg">
                        <label class="fu-label">Subtitle Language {{ $n }}</label>
                        <input type="text" name="subtitle_language{{ $n }}" id="subtitle_language{{ $n }}"
                               class="fu-input" value="{{ old('subtitle_language'.$n) }}"
                               placeholder="{{ $placeholder }}">
                    </div>
                    <div class="fu-fg" @if(!$loop->last) style="margin-bottom:6px;" @else style="margin-bottom:0;" @endif>
                        <label class="fu-label">Subtitle URL {{ $n }}</label>
                        <input type="text" name="subtitle_url{{ $n }}" id="subtitle_url{{ $n }}"
                               class="fu-input" value="{{ old('subtitle_url'.$n) }}"
                               placeholder="http://example.com/sub.srt">
                        <div style="display:flex;gap:8px;margin-top:8px;">
                            <button type="button" class="fu-btn-sm fu-btn-blue"
                                    onclick="document.getElementById('upload_srt{{ $n }}').click()">
                                <i class="fa fa-upload"></i> Upload SRT
                            </button>
                            <input type="file" id="upload_srt{{ $n }}" style="display:none"
                                   onchange="uploadSrt(this, 'subtitle_url{{ $n }}')">
                            <button type="button" class="fu-btn-sm fu-btn-amber"
                                    onclick="showPasteModal('subtitle_url{{ $n }}')">
                                <i class="fa fa-paste"></i> Paste SRT Content
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Actions --}}
                <div style="display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;">
                    <a href="{{ URL::to('user/films') }}" class="fu-btn-ghost">
                        <i class="fa fa-times"></i> Cancel
                    </a>
                    <button type="submit" id="add_btn_id" class="fu-btn-primary">
                        <i class="fa fa-upload"></i> Submit Film for Review
                    </button>
                </div>

            </div>
        </div>
    </form>

</div>
</div>
</div>

{{-- Paste SRT Modal --}}
<div id="pasteSrtModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background:#1e1e24;border:1px solid rgba(255,255,255,0.1);border-radius:14px;">
            <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,0.08);">
                <h4 class="modal-title" style="color:#fff;">Paste SRT Content</h4>
                <button type="button" class="close" data-dismiss="modal" style="color:rgba(255,255,255,0.6);">×</button>
            </div>
            <div class="modal-body">
                <label class="fu-label">Paste your SRT content here:</label>
                <textarea id="srt_content" class="fu-textarea" rows="15"
                    placeholder="1&#10;00:00:01,000 --> 00:00:04,000&#10;Subtitle text here..."></textarea>
            </div>
            <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,0.08);">
                <button type="button" class="fu-btn-ghost" data-dismiss="modal">Close</button>
                <button type="button" class="fu-btn-primary" onclick="generateSrt()">
                    <i class="fa fa-magic"></i> Generate &amp; Use
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ── Video type switcher ────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    var vtSel = document.getElementById('video_type');
    if (vtSel) {
        vtSel.addEventListener('change', switchVideoType);
        switchVideoType(); // init
    }

    var upcoming = document.getElementById('upcoming');
    if (upcoming) {
        upcoming.addEventListener('change', function () {
            var panel = document.getElementById('hide_when_upcoming');
            if (panel) panel.style.display = this.value == '1' ? 'none' : '';
        });
    }
});

function switchVideoType() {
    var vt = document.getElementById('video_type').value;
    document.getElementById('url_id').style.display   = (vt === 'URL')   ? '' : 'none';
    document.getElementById('embed_id').style.display = (vt === 'Embed') ? '' : 'none';
    document.getElementById('hls_id').style.display   = (vt === 'HLS')   ? '' : 'none';
}

// ── SRT Upload ─────────────────────────────────────────────────────────────
function uploadSrt(input, targetId) {
    if (input.files && input.files[0]) {
        var formData = new FormData();
        formData.append('file', input.files[0]);
        $.ajax({
            url: '{{ url("admin/movies/upload_srt") }}',
            type: 'POST',
            data: formData,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.url) {
                    document.getElementById(targetId).value = response.url;
                    autoEnableSubtitles(targetId);
                    input.value = '';
                    Swal.fire({icon:'success', title:'SRT uploaded successfully', background:'#1a1a1d', color:'#fff', confirmButtonColor:'#ff0015'});
                } else {
                    Swal.fire({icon:'error', title: response.error, background:'#1a1a1d', color:'#fff'});
                }
            },
            error: function() {
                Swal.fire({icon:'error', title:'Upload failed', background:'#1a1a1d', color:'#fff'});
            }
        });
    }
}

var currentPasteTarget = '';
function showPasteModal(targetId) {
    currentPasteTarget = targetId;
    document.getElementById('srt_content').value = '';
    $('#pasteSrtModal').modal('show');
}

function generateSrt() {
    var content = document.getElementById('srt_content').value;
    if (!content) { Swal.fire({icon:'error', title:'Please paste content', background:'#1a1a1d', color:'#fff'}); return; }
    $.ajax({
        url: '{{ url("admin/movies/generate_srt") }}',
        type: 'POST',
        data: {content: content},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            if (response.url) {
                document.getElementById(currentPasteTarget).value = response.url;
                autoEnableSubtitles(currentPasteTarget);
                $('#pasteSrtModal').modal('hide');
                Swal.fire({icon:'success', title:'SRT generated successfully', background:'#1a1a1d', color:'#fff', confirmButtonColor:'#ff0015'});
            } else {
                Swal.fire({icon:'error', title: response.error, background:'#1a1a1d', color:'#fff'});
            }
        },
        error: function() {
            Swal.fire({icon:'error', title:'Generation failed', background:'#1a1a1d', color:'#fff'});
        }
    });
}

function autoEnableSubtitles(targetId) {
    document.getElementById('inlineRadio5').checked = true;
    var langMap = {subtitle_url1:'subtitle_language1', subtitle_url2:'subtitle_language2', subtitle_url3:'subtitle_language3'};
    var langField = document.getElementById(langMap[targetId]);
    if (langField && !langField.value.trim()) langField.value = 'English';
}

// ── Flash messages ─────────────────────────────────────────────────────────
@if(Session::has('flash_message'))
Swal.fire({toast:true, position:'top-end', showConfirmButton:false, timer:4000, icon:'success', title:'{{ Session::get("flash_message") }}', background:'#1a1a1d', color:'#fff'});
@endif
@if(count($errors) > 0)
Swal.fire({icon:'error', title:'Please fix the errors below', html:'<p>@foreach($errors->all() as $error){{ $error }}<br/>@endforeach</p>', background:'#1a1a1d', color:'#fff', confirmButtonColor:'#ff0015'});
@endif
</script>
@endsection
