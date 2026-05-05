@extends('admin.admin_app')

@section('content')

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">

                        {{-- Header --}}
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{ URL::to('user/films') }}">
                                    <h4 class="header-title m-t-0 m-b-30 text-primary pull-left" style="font-size:20px;">
                                        <i class="fa fa-arrow-left"></i> My Films
                                    </h4>
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <h4 class="header-title m-t-0 m-b-30 pull-right" style="font-size:20px;color:#aaa;">
                                    <i class="fa fa-clock-o"></i> Submitted films require admin approval
                                </h4>
                            </div>
                        </div>

                        {{-- Alerts --}}
                        @if(count($errors) > 0)
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                            </div>
                        @endif
                        @if(Session::has('flash_message'))
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                                {{ Session::get('flash_message') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ URL::to('user/films/store') }}"
                              class="form-horizontal" name="movie_form" id="movie_form">
                            @csrf

                            <div class="row">

                                {{-- ── LEFT: Film Info ── --}}
                                <div class="col-md-6">
                                    <h4 class="m-t-0 m-b-30 header-title" style="font-size:20px;">Film Information</h4>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Film Title *</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="video_title" id="video_title"
                                                   value="{{ old('video_title') }}" class="form-control"
                                                   placeholder="Enter film title">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-12 col-form-label">Description</label>
                                        <div class="col-sm-12">
                                            <div class="card-box pl-0 description_box">
                                                <textarea id="elm1" name="video_description">{{ old('video_description') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Upcoming?</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="upcoming" id="upcoming">
                                                <option value="0" @if(old('upcoming')=='0') selected @endif>No — release now</option>
                                                <option value="1" @if(old('upcoming')=='1') selected @endif>Yes — upcoming film</option>
                                            </select>
                                            <small class="form-text text-muted">(Upcoming films show on Home page only)</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Are you the owner? *</label>
                                        <div class="col-sm-8">
                                            <div class="radio radio-success form-check-inline pl-2" style="margin-top:8px;">
                                                <input type="radio" name="is_owner" id="is_owner_yes" value="1">
                                                <label for="is_owner_yes">Yes, I am the owner</label>
                                            </div>
                                            <div class="radio form-check-inline" style="margin-top:8px;">
                                                <input type="radio" name="is_owner" id="is_owner_no" value="0" checked>
                                                <label for="is_owner_no">No, sharing someone else's work</label>
                                            </div>
                                            <small class="form-text text-muted">(Owners get digital awards based on likes)</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Language *</label>
                                        <div class="col-sm-8">
                                            <select class="form-control select2" name="movie_language" id="movie_language">
                                                <option value="">— Select Language —</option>
                                                @foreach($language_list as $lang)
                                                    <option value="{{ $lang->id }}" @if(old('movie_language')==$lang->id) selected @endif>
                                                        {{ $lang->language_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Genre(s) *</label>
                                        <div class="col-sm-8">
                                            <select name="genres[]" class="select2 select2-multiple"
                                                    multiple="multiple" id="movie_genre_id"
                                                    data-placeholder="Select Genres...">
                                                @foreach($genre_list as $g)
                                                    <option value="{{ $g->id }}" @if(is_array(old('genres')) && in_array($g->id, old('genres'))) selected @endif>
                                                        {{ $g->genre_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Actors <small class="text-muted">(comma separated)</small></label>
                                        <div class="col-sm-8">
                                            <input type="text" name="actors" id="actors"
                                                   value="{{ old('actors') }}" class="form-control"
                                                   placeholder="e.g. John Doe, Jane Smith">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Directors <small class="text-muted">(comma separated)</small></label>
                                        <div class="col-sm-8">
                                            <input type="text" name="director" id="director"
                                                   value="{{ old('director') }}" class="form-control"
                                                   placeholder="e.g. James Cameron">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Funding URL</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="funding_url" id="funding_url"
                                                   value="{{ old('funding_url') }}" class="form-control"
                                                   placeholder="https://kickstarter.com/…">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Webpage URL</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="webpage_url" id="webpage_url"
                                                   value="{{ old('webpage_url') }}" class="form-control"
                                                   placeholder="https://myfilm.com">
                                        </div>
                                    </div>

                                </div>{{-- /col-md-6 left --}}

                                {{-- ── RIGHT: Poster + Video + Subtitles ── --}}
                                <div class="col-md-6">
                                    <h4 class="m-t-0 m-b-30 header-title" style="font-size:20px;">Poster / Video / Subtitles</h4>

                                    {{-- Poster --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Poster URL</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="poster_link" id="poster_link"
                                                   value="{{ old('poster_link') }}" class="form-control"
                                                   placeholder="https://example.com/poster.jpg">
                                            <small class="form-text text-muted">Leave blank — auto-fetched for YouTube & Vimeo.</small>
                                        </div>
                                    </div>

                                    <hr/>

                                    {{-- Video Type --}}
                                    <div class="form-group row" id="hide_when_upcoming">
                                        <label class="col-sm-3 col-form-label">Video Type</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="video_type" id="video_type">
                                                <option value="" disabled selected>Select video type</option>
                                                <option value="URL"   @if(old('video_type')=='URL')   selected @endif>URL (Direct MP4)</option>
                                                <option value="HLS"   @if(old('video_type')=='HLS')   selected @endif>HLS / m3u8 / MPEG-DASH / YouTube / Vimeo</option>
                                                <option value="Embed" @if(old('video_type')=='Embed') selected @endif>Embed Code</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Video Quality --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Multi-Quality (480/720/1080)</label>
                                        <div class="col-sm-8">
                                            <div class="radio radio-success form-check-inline pl-2" style="margin-top:8px;">
                                                <input type="radio" name="video_quality" id="vq_active" value="1">
                                                <label for="vq_active">Active</label>
                                            </div>
                                            <div class="radio form-check-inline" style="margin-top:8px;">
                                                <input type="radio" name="video_quality" id="vq_inactive" value="0" checked>
                                                <label for="vq_inactive">Inactive</label>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- URL inputs --}}
                                    <div id="url_id">
                                        <small class="form-text text-muted" style="margin-bottom:10px;">Supported: MP4 URL. External files must be CORS-enabled.</small>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Video URL (Default)</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="video_url" class="form-control"
                                                       value="{{ old('video_url') }}" placeholder="http://example.com/demo.mp4">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Video URL 480p</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="video_url_480" class="form-control"
                                                       value="{{ old('video_url_480') }}" placeholder="http://example.com/480.mp4">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Video URL 720p</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="video_url_720" class="form-control"
                                                       value="{{ old('video_url_720') }}" placeholder="http://example.com/720.mp4">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Video URL 1080p</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="video_url_1080" class="form-control"
                                                       value="{{ old('video_url_1080') }}" placeholder="http://example.com/1080.mp4">
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Embed --}}
                                    <div id="embed_id" style="display:none;">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Embed Code</label>
                                            <div class="col-sm-8">
                                                <textarea name="video_embed_code" class="form-control" rows="5"
                                                          placeholder="<iframe src=&quot;…&quot;></iframe>">{{ old('video_embed_code') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- HLS --}}
                                    <div id="hls_id" style="display:none;">
                                        <small class="form-text text-muted" style="margin-bottom:10px;">Supported: MP4, YouTube, Vimeo, HLS/m3u8. External files must be CORS-enabled.</small>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">HLS / Stream URL</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="video_url_hls" class="form-control"
                                                       value="{{ old('video_url_hls') }}" placeholder="http://example.com/test.m3u8">
                                            </div>
                                        </div>
                                    </div>

                                    <hr/>

                                    {{-- Subtitles --}}
                                    <h5 class="header-title m-b-20">Subtitles</h5>
                                    <small class="form-text text-muted" style="margin-bottom:16px;display:block;">Supported: .srt or .vtt file URLs only. External files must be CORS-enabled.</small>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Subtitles</label>
                                        <div class="col-sm-8">
                                            <div class="radio radio-success form-check-inline pl-2" style="margin-top:8px;">
                                                <input type="radio" id="inlineRadio5" value="1" name="subtitle_on_off">
                                                <label for="inlineRadio5">Active</label>
                                            </div>
                                            <div class="radio form-check-inline" style="margin-top:8px;">
                                                <input type="radio" id="inlineRadio6" value="0" name="subtitle_on_off" checked>
                                                <label for="inlineRadio6">Inactive</label>
                                            </div>
                                        </div>
                                    </div>

                                    @foreach([['1','English'],['2','French'],['3','Spanish']] as [$n, $ph])
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Subtitle Language {{ $n }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="subtitle_language{{ $n }}" id="subtitle_language{{ $n }}"
                                                   value="{{ old('subtitle_language'.$n) }}"
                                                   placeholder="{{ $ph }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Subtitle URL {{ $n }}</label>
                                        <div class="col-sm-8">
                                            <input type="text" name="subtitle_url{{ $n }}" id="subtitle_url{{ $n }}"
                                                   value="{{ old('subtitle_url'.$n) }}"
                                                   class="form-control" placeholder="http://example.com/demo.srt">
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-info btn-xs"
                                                        onclick="document.getElementById('upload_srt{{ $n }}').click()">Upload SRT</button>
                                                <input type="file" id="upload_srt{{ $n }}" style="display:none"
                                                       onchange="uploadSrt(this, 'subtitle_url{{ $n }}')">
                                                <button type="button" class="btn btn-warning btn-xs"
                                                        onclick="showPasteModal('subtitle_url{{ $n }}')">Paste SRT Content</button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach

                                </div>{{-- /col-md-6 right --}}

                            </div>{{-- /row --}}

                            <div class="form-group">
                                <div class="offset-sm-3 col-sm-9" style="display:flex;gap:12px;margin-top:20px;">
                                    <a href="{{ URL::to('user/films') }}" class="btn btn-secondary waves-effect">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" id="add_btn_id" class="btn btn-primary waves-effect waves-light">
                                        <i class="fa fa-upload"></i> Submit Film for Review
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>{{-- /card-box --}}
                </div>
            </div>
        </div>
    </div>
    @include('admin.copyright')
</div>

{{-- Paste SRT Modal --}}
<div id="pasteSrtModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Paste SRT Content</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Paste your SRT content here:</label>
                    <textarea id="srt_content" class="form-control" rows="15"
                              placeholder="1&#10;00:00:01,000 --> 00:00:04,000&#10;Subtitle text here..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="generateSrt()">Generate &amp; Use</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // SRT Upload
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
                        $('#' + targetId).val(response.url);
                        autoEnableSubtitles(targetId);
                        $(input).val('');
                        Swal.fire('Success', 'SRT uploaded successfully', 'success');
                    } else {
                        Swal.fire('Error', response.error, 'error');
                    }
                },
                error: function() { Swal.fire('Error', 'Upload failed', 'error'); }
            });
        }
    }

    var currentPasteTarget = '';
    function showPasteModal(targetId) {
        currentPasteTarget = targetId;
        $('#srt_content').val('');
        $('#pasteSrtModal').modal('show');
    }

    function generateSrt() {
        var content = $('#srt_content').val();
        if (!content) { Swal.fire('Error', 'Please paste content', 'error'); return; }
        $.ajax({
            url: '{{ url("admin/movies/generate_srt") }}',
            type: 'POST',
            data: {content: content},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response) {
                if (response.url) {
                    $('#' + currentPasteTarget).val(response.url);
                    autoEnableSubtitles(currentPasteTarget);
                    $('#pasteSrtModal').modal('hide');
                    Swal.fire('Success', 'SRT generated successfully', 'success');
                } else {
                    Swal.fire('Error', response.error, 'error');
                }
            },
            error: function() { Swal.fire('Error', 'Generation failed', 'error'); }
        });
    }

    function autoEnableSubtitles(targetId) {
        $('#inlineRadio5').prop('checked', true);
        var langMap = {subtitle_url1:'subtitle_language1', subtitle_url2:'subtitle_language2', subtitle_url3:'subtitle_language3'};
        var lf = document.getElementById(langMap[targetId]);
        if (lf && !lf.value.trim()) lf.value = 'English';
    }

    // upcoming toggle
    $(document).ready(function(){
        $('#upcoming').on('change', function(){
            $('#hide_when_upcoming').toggle($(this).val() != '1');
        });
    });
</script>

@if(Session::has('flash_message'))
<script>
    const Toast = Swal.mixin({toast:true, position:'top-end', showConfirmButton:false, timer:3000});
    Toast.fire({icon:'success', title:'{{ Session::get("flash_message") }}'});
</script>
@endif
@if(count($errors) > 0)
<script>
    Swal.fire({icon:'error', title:'Oops...', html:'<p>@foreach($errors->all() as $error){{ $error }}<br/>@endforeach</p>', showConfirmButton:true, confirmButtonColor:'#10c469', background:"#1a2234", color:"#fff"});
</script>
@endif

@endsection
