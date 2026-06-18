<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $project->title }} — Film Editor | {{ getcong('site_name') }}</title>
    @if(getcong('site_favicon'))
    <link rel="shortcut icon" href="{{ URL::asset('/'.getcong('site_favicon')) }}">
    @endif
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('site_assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('editor_assets/css/film-editor.css') }}">
    <script src="{{ URL::asset('site_assets/js/sweetalert2@11.js') }}"></script>
</head>
<body>

<!-- Film Grain Overlay -->
<div class="film-grain"></div>

<!-- Mobile Warning -->
<div class="editor-mobile-warning">
    <div>
        <i class="fa fa-desktop"></i>
        <h3>Desktop Required</h3>
        <p>The Film Editor requires a screen width of at least 1024px. Please open this page on a desktop or laptop computer.</p>
    </div>
</div>

<!-- ═══ TOP BAR ═══ -->
<div class="editor-topbar">
    <div class="editor-topbar-left">
        <a href="{{ URL::to('user/editor') }}" class="editor-topbar-back">
            <i class="fa fa-arrow-left"></i> Projects
        </a>
        <div class="editor-topbar-divider"></div>
        <span class="editor-topbar-title">{{ $project->title }}</span>
    </div>
    <div class="editor-topbar-center">
        <span class="editor-topbar-status" id="saveStatus">
            <span class="dot"></span>
            <span id="saveStatusText">Saved</span>
        </span>
    </div>
    <div class="editor-topbar-right">
        <button class="editor-topbar-btn btn-save" id="btnSave" onclick="FilmEditor.saveTimeline()">
            <i class="fa fa-save"></i> Save
        </button>
        <button class="editor-topbar-btn btn-export" id="btnExport" onclick="FilmEditor.startExport()">
            <i class="fa fa-download"></i> Export MP4
        </button>
    </div>
</div>

<!-- ═══ MAIN LAYOUT ═══ -->
<div class="editor-main">

    <!-- ── LEFT PANEL: Source Reel + Clip Bin ── -->
    <div class="editor-left-panel">

        <!-- Source Reel -->
        <div class="reel-container">
            <div class="reel">
                <div class="reel-outer" id="sourceReel">
                    <div class="reel-spoke"></div>
                    <div class="reel-spoke"></div>
                    <div class="reel-spoke"></div>
                    <div class="reel-spoke"></div>
                    <div class="reel-spoke"></div>
                    <div class="reel-spoke"></div>
                    <div class="reel-hub"></div>
                </div>
                <div class="reel-label">Source Reel</div>
            </div>
        </div>

        <!-- Upload Zone -->
        <div class="clip-upload-zone">
            <div class="clip-upload-dropzone" id="uploadDropzone">
                <i class="fa fa-film"></i>
                <p>Drop video clips here or <span onclick="document.getElementById('clipFileInput').click()">browse</span></p>
                <input type="file" id="clipFileInput" accept="video/*" multiple style="display:none;" onchange="FilmEditor.handleFileSelect(this.files)">
            </div>
            <div class="clip-upload-progress" id="uploadProgress">
                <div class="clip-upload-progress-bar">
                    <div class="clip-upload-progress-fill" id="uploadProgressFill"></div>
                </div>
                <div class="clip-upload-progress-text" id="uploadProgressText">Uploading…</div>
            </div>
        </div>

        <!-- Clip Bin Header -->
        <div class="editor-panel-header">
            <h4><i class="fa fa-film"></i> Clip Bin</h4>
            <span style="font-size:11px;color:var(--text-muted);" id="clipCount">{{ $clips->count() }} clips</span>
        </div>

        <!-- Clip Bin List -->
        <div class="clip-bin" id="clipBin">
            @if($clips->count())
                @foreach($clips as $clip)
                    <div class="clip-bin-item" draggable="true"
                         data-clip-id="{{ $clip->id }}"
                         data-duration="{{ $clip->duration }}"
                         data-filename="{{ $clip->original_filename }}"
                         data-filepath="{{ URL::asset($clip->file_path) }}"
                         data-thumbnails='@json($clip->getThumbnailUrls())'
                         ondragstart="FilmEditor.onClipDragStart(event, this)"
                         ondragend="FilmEditor.onClipDragEnd(event, this)">
                        <div class="clip-bin-item-thumb">
                            @if($clip->thumbnail_strip && count($clip->thumbnail_strip) > 0)
                                <img src="{{ URL::asset($clip->thumbnail_strip[0]) }}" alt="">
                            @endif
                            <span class="duration-badge">{{ gmdate('i:s', intval($clip->duration)) }}</span>
                        </div>
                        <div class="clip-bin-item-info">
                            <div class="clip-bin-item-name">{{ $clip->original_filename }}</div>
                            <div class="clip-bin-item-meta">
                                {{ $clip->width }}×{{ $clip->height }} · {{ round($clip->file_size / 1048576, 1) }}MB
                            </div>
                        </div>
                        <button class="clip-bin-item-remove" onclick="FilmEditor.removeClip({{ $clip->id }}, this)" title="Remove clip">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                @endforeach
            @else
                <div class="clip-bin-empty" id="clipBinEmpty">
                    <i class="fa fa-film"></i>
                    <p>Upload some video clips to get started</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ── CENTER: Preview + Timeline ── -->
    <div class="editor-center">

        <!-- Preview Monitor -->
        <div class="preview-area">
            <div class="preview-monitor" id="previewMonitor">
                <video id="previewVideo" preload="auto"></video>
                <div class="preview-placeholder" id="previewPlaceholder">
                    <i class="fa fa-film"></i>
                    <p>Add clips to the timeline to preview</p>
                </div>
                <div class="preview-timecode" id="previewTimecode">00:00:00.00</div>
            </div>
        </div>

        <!-- Transport Controls -->
        <div class="transport-controls">
            <button class="transport-btn" onclick="FilmEditor.skipToStart()" title="Skip to start">
                <i class="fa fa-step-backward"></i>
            </button>
            <button class="transport-btn" onclick="FilmEditor.frameBack()" title="Previous frame">
                <i class="fa fa-backward"></i>
            </button>
            <button class="transport-btn btn-play" id="btnPlay" onclick="FilmEditor.togglePlay()" title="Play/Pause">
                <i class="fa fa-play" id="playIcon"></i>
            </button>
            <button class="transport-btn" onclick="FilmEditor.frameForward()" title="Next frame">
                <i class="fa fa-forward"></i>
            </button>
            <button class="transport-btn" onclick="FilmEditor.skipToEnd()" title="Skip to end">
                <i class="fa fa-step-forward"></i>
            </button>

            <div class="transport-divider"></div>

            <button class="transport-btn btn-tool" id="btnSelect" onclick="FilmEditor.setTool('select')" title="Select tool">
                <i class="fa fa-mouse-pointer"></i>
            </button>
            <button class="transport-btn btn-tool" id="btnRazor" onclick="FilmEditor.setTool('razor')" title="Cut tool (razor blade)">
                <i class="fa fa-cut"></i>
            </button>

            <div class="transport-divider"></div>

            <button class="transport-btn btn-tool" onclick="FilmEditor.deleteSelectedClip()" title="Delete selected clip">
                <i class="fa fa-trash"></i>
            </button>
        </div>

        <!-- Timeline Area -->
        <div class="timeline-area" id="timelineArea">
            <!-- Ruler -->
            <div class="timeline-ruler" id="timelineRuler">
                <div class="timeline-playhead" id="timelinePlayhead"></div>
            </div>

            <!-- Film Strip Track -->
            <div class="timeline-track-container" id="timelineTrackContainer"
                 ondragover="FilmEditor.onTimelineDragOver(event)"
                 ondrop="FilmEditor.onTimelineDrop(event)"
                 ondragleave="FilmEditor.onTimelineDragLeave(event)">
                <div class="timeline-track" id="timelineTrack">
                    <!-- Clips will be rendered here dynamically -->
                </div>
                <div class="timeline-empty" id="timelineEmpty">
                    <i class="fa fa-hand-pointer-o"></i>
                    <p>Drag clips from the bin to the timeline<br>or <span>double-click</span> a clip to add it</p>
                </div>
            </div>

            <!-- Audio Track -->
            <div class="audio-track-area">
                <span class="audio-track-label"><i class="fa fa-music" style="margin-right:6px;color:var(--accent);"></i> Audio</span>
                <div class="audio-track-waveform" id="audioWaveform"></div>
                <button class="audio-track-add-btn" onclick="FilmEditor.addAudioTrack()" title="Add background audio">
                    <i class="fa fa-plus"></i> Add Audio
                </button>
                <input type="file" id="audioFileInput" accept="audio/*" style="display:none;" onchange="FilmEditor.handleAudioSelect(this.files)">
            </div>
        </div>
    </div>

    <!-- ── RIGHT PANEL: Properties / Colour Grading ── -->
    <div class="editor-right-panel">
        <!-- Tabs -->
        <div class="editor-panel-tabs">
            <button class="editor-panel-tab active" onclick="FilmEditor.switchTab('properties', this)">
                <i class="fa fa-info-circle"></i> Properties
            </button>
            <button class="editor-panel-tab" onclick="FilmEditor.switchTab('grading', this)">
                <i class="fa fa-adjust"></i> Grading
            </button>
            <button class="editor-panel-tab" onclick="FilmEditor.switchTab('transitions', this)">
                <i class="fa fa-exchange"></i> FX
            </button>
        </div>

        <!-- Properties Tab -->
        <div class="editor-panel-content" id="tabProperties">
            <div class="prop-section">
                <div class="prop-section-title">Project</div>
                <div class="prop-row">
                    <span class="prop-label">Duration</span>
                    <span class="prop-value" id="propTotalDuration">00:00:00</span>
                </div>
                <div class="prop-row">
                    <span class="prop-label">Clips</span>
                    <span class="prop-value" id="propClipCount">0</span>
                </div>
                <div class="prop-row">
                    <span class="prop-label">Status</span>
                    <span class="prop-value" id="propStatus">{{ ucfirst($project->status) }}</span>
                </div>
            </div>
            <div class="prop-section" id="selectedClipProps" style="display:none;">
                <div class="prop-section-title">Selected Clip</div>
                <div class="prop-row">
                    <span class="prop-label">Name</span>
                    <span class="prop-value" id="propClipName">—</span>
                </div>
                <div class="prop-row">
                    <span class="prop-label">Duration</span>
                    <span class="prop-value" id="propClipDuration">—</span>
                </div>
                <div class="prop-row">
                    <span class="prop-label">In Point</span>
                    <span class="prop-value" id="propClipIn">—</span>
                </div>
                <div class="prop-row">
                    <span class="prop-label">Out Point</span>
                    <span class="prop-value" id="propClipOut">—</span>
                </div>
            </div>

            <!-- Master Reel (bottom of right panel) -->
            <div style="margin-top:30px;">
                <div class="reel-container" style="border:none;padding:10px 0;">
                    <div class="reel">
                        <div class="reel-outer" id="masterReel">
                            <div class="reel-spoke"></div>
                            <div class="reel-spoke"></div>
                            <div class="reel-spoke"></div>
                            <div class="reel-spoke"></div>
                            <div class="reel-spoke"></div>
                            <div class="reel-spoke"></div>
                            <div class="reel-hub"></div>
                        </div>
                        <div class="reel-label">Master Reel</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colour Grading Tab -->
        <div class="editor-panel-content hidden" id="tabGrading">
            <div class="prop-section">
                <div class="prop-section-title">Colour Grading</div>

                <div class="grading-slider-group">
                    <div class="grading-slider-label">
                        <span>Brightness</span>
                        <span class="grading-slider-value" id="valBrightness">0</span>
                    </div>
                    <input type="range" class="grading-slider" id="sliderBrightness" min="-100" max="100" value="0"
                           oninput="FilmEditor.updateGrading('brightness', this.value)">
                </div>

                <div class="grading-slider-group">
                    <div class="grading-slider-label">
                        <span>Contrast</span>
                        <span class="grading-slider-value" id="valContrast">100</span>
                    </div>
                    <input type="range" class="grading-slider" id="sliderContrast" min="0" max="200" value="100"
                           oninput="FilmEditor.updateGrading('contrast', this.value)">
                </div>

                <div class="grading-slider-group">
                    <div class="grading-slider-label">
                        <span>Saturation</span>
                        <span class="grading-slider-value" id="valSaturation">100</span>
                    </div>
                    <input type="range" class="grading-slider" id="sliderSaturation" min="0" max="200" value="100"
                           oninput="FilmEditor.updateGrading('saturation', this.value)">
                </div>

                <div class="grading-slider-group">
                    <div class="grading-slider-label">
                        <span>Warmth</span>
                        <span class="grading-slider-value" id="valWarmth">0</span>
                    </div>
                    <input type="range" class="grading-slider" id="sliderWarmth" min="-50" max="50" value="0"
                           oninput="FilmEditor.updateGrading('warmth', this.value)">
                </div>

                <div class="grading-slider-group">
                    <div class="grading-slider-label">
                        <span>Sepia</span>
                        <span class="grading-slider-value" id="valSepia">0%</span>
                    </div>
                    <input type="range" class="grading-slider" id="sliderSepia" min="0" max="100" value="0"
                           oninput="FilmEditor.updateGrading('sepia', this.value)">
                </div>

                <button class="editor-topbar-btn btn-save" style="width:100%;justify-content:center;margin-top:16px;"
                        onclick="FilmEditor.resetGrading()">
                    <i class="fa fa-undo"></i> Reset All
                </button>
            </div>
        </div>

        <!-- Transitions Tab -->
        <div class="editor-panel-content hidden" id="tabTransitions">
            <div class="prop-section">
                <div class="prop-section-title">Transition Type</div>
                <div class="transition-selector">
                    <button class="transition-option active" onclick="FilmEditor.setTransition('cut', this)">Cut</button>
                    <button class="transition-option" onclick="FilmEditor.setTransition('crossfade', this)">Crossfade</button>
                    <button class="transition-option" onclick="FilmEditor.setTransition('fade', this)">Fade</button>
                    <button class="transition-option" onclick="FilmEditor.setTransition('wipeleft', this)">Wipe ←</button>
                    <button class="transition-option" onclick="FilmEditor.setTransition('wiperight', this)">Wipe →</button>
                    <button class="transition-option" onclick="FilmEditor.setTransition('wipeup', this)">Wipe ↑</button>
                </div>
            </div>
            <div class="prop-section">
                <div class="prop-section-title">Transition Duration</div>
                <div class="grading-slider-group">
                    <div class="grading-slider-label">
                        <span>Duration</span>
                        <span class="grading-slider-value" id="valTransDuration">1.0s</span>
                    </div>
                    <input type="range" class="grading-slider" id="sliderTransDuration" min="0.2" max="3.0" step="0.1" value="1.0"
                           oninput="FilmEditor.setTransitionDuration(this.value)">
                </div>
            </div>
            <div class="prop-section">
                <p style="font-size:12px;color:var(--text-muted);line-height:1.6;">
                    Select a splice point on the timeline to apply the chosen transition between two clips.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- ═══ EXPORT MODAL ═══ -->
<div class="export-overlay" id="exportOverlay">
    <div class="export-modal" style="position:relative;">
        <button class="export-close-btn" onclick="FilmEditor.closeExport()"><i class="fa fa-times"></i></button>
        <h3 id="exportTitle">Exporting Your Film…</h3>
        <p id="exportSubtitle">Please wait while FFmpeg renders your masterpiece.</p>
        <div class="export-progress-ring">
            <svg viewBox="0 0 120 120">
                <circle class="ring-bg" cx="60" cy="60" r="56"></circle>
                <circle class="ring-fill" id="exportRingFill" cx="60" cy="60" r="56"></circle>
            </svg>
            <span class="export-progress-percent" id="exportPercent">0%</span>
        </div>
        <div class="export-status-text" id="exportStatusText">Initializing…</div>
        <a href="#" class="export-download-btn" id="exportDownloadBtn">
            <i class="fa fa-download"></i> Download MP4
        </a>
    </div>
</div>

<!-- ═══ PROJECT DATA (for JS) ═══ -->
@php
    $defaultTimeline = ['clips' => [], 'audioTracks' => [], 'colorGrading' => ['brightness' => 0, 'contrast' => 1.0, 'saturation' => 1.0, 'warmth' => 0, 'sepia' => 0]];
    $timelineJson = $project->timeline_data ?? $defaultTimeline;
@endphp
<script>
    window.EDITOR_CONFIG = {
        projectId: {{ $project->id }},
        csrfToken: '{{ csrf_token() }}',
        saveUrl: '{{ URL::to("user/editor/{$project->id}/save") }}',
        uploadClipUrl: '{{ URL::to("user/editor/{$project->id}/upload-clip") }}',
        deleteClipUrl: '{{ URL::to("user/editor/{$project->id}/delete-clip") }}',
        exportUrl: '{{ URL::to("user/editor/{$project->id}/export") }}',
        exportStatusUrl: '{{ URL::to("user/editor/{$project->id}/export-status") }}',
        downloadUrl: '{{ URL::to("user/editor/{$project->id}/download") }}',
        timelineData: @json($timelineJson)
    };
</script>

<script src="{{ URL::asset('editor_assets/js/film-editor.js') }}"></script>

</body>
</html>
