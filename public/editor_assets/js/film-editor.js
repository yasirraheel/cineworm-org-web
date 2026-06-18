/**
 * ============================================================================
 * CINEWORM VINTAGE FILM EDITOR — Core Engine
 * ============================================================================
 * A reel-to-reel film editing engine built with vanilla JS.
 * Handles: clip management, timeline rendering, playback, cutting,
 * trimming, drag-and-drop, auto-save, colour grading, and export.
 * ============================================================================
 */

const FilmEditor = (function() {
    'use strict';

    // ── State ─────────────────────────────────────────────────────────────
    const state = {
        timeline: { clips: [], audioTracks: [], colorGrading: {} },
        clips: [],              // All available clips from the clip bin
        selectedClipIndex: -1,  // Index of the selected clip on the timeline
        currentTool: 'select',  // 'select' or 'razor'
        isPlaying: false,
        playheadPosition: 0,    // Current time in seconds
        totalDuration: 0,
        pixelsPerSecond: 80,    // Timeline zoom level
        isDirty: false,         // Has unsaved changes?
        autoSaveTimer: null,
        currentTransition: { type: 'cut', duration: 1.0 },
        exportPollTimer: null,
        animationFrame: null,
    };

    // ── Helpers ────────────────────────────────────────────────────────────
    function formatTime(seconds) {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);
        const ms = Math.floor((seconds % 1) * 100);
        return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}.${String(ms).padStart(2,'0')}`;
    }

    function formatDuration(seconds) {
        const m = Math.floor(seconds / 60);
        const s = Math.floor(seconds % 60);
        return `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    }

    function ajax(url, method, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', window.EDITOR_CONFIG.csrfToken);
        xhr.setRequestHeader('Accept', 'application/json');
        if (data instanceof FormData) {
            // Don't set Content-Type, let browser set multipart boundary
        } else if (data) {
            xhr.setRequestHeader('Content-Type', 'application/json');
            data = JSON.stringify(data);
        }
        xhr.onload = function() {
            try {
                const resp = JSON.parse(xhr.responseText);
                callback(null, resp);
            } catch(e) {
                callback('Invalid server response');
            }
        };
        xhr.onerror = function() { callback('Network error'); };
        xhr.send(data);
        return xhr;
    }

    function ajaxUpload(url, formData, onProgress, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', window.EDITOR_CONFIG.csrfToken);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable && onProgress) {
                onProgress(Math.round((e.loaded / e.total) * 100));
            }
        };
        xhr.onload = function() {
            try {
                const resp = JSON.parse(xhr.responseText);
                callback(null, resp);
            } catch(e) {
                callback('Invalid server response');
            }
        };
        xhr.onerror = function() { callback('Network error'); };
        xhr.send(formData);
        return xhr;
    }

    // ── Initialize ────────────────────────────────────────────────────────
    function init() {
        // Load existing timeline data
        if (window.EDITOR_CONFIG.timelineData) {
            state.timeline = window.EDITOR_CONFIG.timelineData;
            if (!state.timeline.clips) state.timeline.clips = [];
            if (!state.timeline.audioTracks) state.timeline.audioTracks = [];
            if (!state.timeline.colorGrading) {
                state.timeline.colorGrading = { brightness: 0, contrast: 1.0, saturation: 1.0, warmth: 0, sepia: 0 };
            }
        }

        // Parse clips from DOM
        parseClipBin();

        // Render timeline from saved data
        renderTimeline();

        // Setup event listeners
        setupDragDrop();
        setupKeyboardShortcuts();
        setupTimelineClick();

        // Load colour grading values into sliders
        loadGradingValues();

        // Set default tool
        setTool('select');

        // Start auto-save
        state.autoSaveTimer = setInterval(function() {
            if (state.isDirty) {
                saveTimeline(true);
            }
        }, 30000); // Auto-save every 30 seconds

        // Warn on unsaved changes before leaving
        window.onbeforeunload = function() {
            if (state.isDirty) return 'You have unsaved changes. Leave anyway?';
        };
    }

    // ── Parse existing clips from DOM ─────────────────────────────────────
    function parseClipBin() {
        state.clips = [];
        const items = document.querySelectorAll('.clip-bin-item');
        items.forEach(function(el) {
            state.clips.push({
                id: parseInt(el.dataset.clipId),
                duration: parseFloat(el.dataset.duration),
                filename: el.dataset.filename,
                filepath: el.dataset.filepath,
                thumbnails: JSON.parse(el.dataset.thumbnails || '[]'),
                element: el,
            });
        });
    }

    // ── File Upload ───────────────────────────────────────────────────────
    function handleFileSelect(files) {
        for (let i = 0; i < files.length; i++) {
            uploadClip(files[i]);
        }
        // Reset input so same file can be re-selected
        document.getElementById('clipFileInput').value = '';
    }

    function uploadClip(file) {
        const progressEl = document.getElementById('uploadProgress');
        const progressFill = document.getElementById('uploadProgressFill');
        const progressText = document.getElementById('uploadProgressText');

        // Show progress bar — use flex so it respects its layout properly
        progressEl.style.display = 'block';
        progressEl.style.opacity = '1';
        progressFill.style.width = '0%';
        progressText.textContent = `Uploading ${file.name}…`;

        const formData = new FormData();
        formData.append('clip', file);

        ajaxUpload(
            window.EDITOR_CONFIG.uploadClipUrl,
            formData,
            function(percent) {
                progressFill.style.width = percent + '%';
                progressText.textContent = percent >= 100
                    ? 'Processing… please wait'
                    : `Uploading ${file.name}… ${percent}%`;
            },
            function(err, resp) {
                // Animate out the progress bar
                progressFill.style.width = '100%';
                setTimeout(function() {
                    progressEl.style.opacity = '0';
                    setTimeout(function() {
                        progressEl.style.display = 'none';
                        progressEl.style.opacity = '1';
                        progressFill.style.width = '0%';
                    }, 300);
                }, 400);

                if (err || !resp || !resp.success) {
                    Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000,
                        icon: 'error', title: (resp && resp.message) || err || 'Upload failed' });
                    return;
                }

                // Add clip to bin
                addClipToBin(resp.data);

                Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
                    icon: 'success', title: 'Clip uploaded successfully' });
            }
        );
    }

    function addClipToBin(clipData) {
        // Remove empty state
        const emptyEl = document.getElementById('clipBinEmpty');
        if (emptyEl) emptyEl.remove();

        const clip = {
            id: clipData.id,
            duration: clipData.duration,
            filename: clipData.original_filename,
            filepath: clipData.file_url,
            thumbnails: clipData.thumbnail_urls || [],
        };
        state.clips.push(clip);

        // Create DOM element
        const div = document.createElement('div');
        div.className = 'clip-bin-item';
        div.draggable = true;
        div.dataset.clipId = clip.id;
        div.dataset.duration = clip.duration;
        div.dataset.filename = clip.filename;
        div.dataset.filepath = clip.filepath;
        div.dataset.thumbnails = JSON.stringify(clip.thumbnails);
        div.ondragstart = function(e) { onClipDragStart(e, div); };
        div.ondragend = function(e) { onClipDragEnd(e, div); };
        div.ondblclick = function() { addClipToTimeline(clip.id); };

        const thumbSrc = clip.thumbnails.length > 0 ? clip.thumbnails[0] : '';
        div.innerHTML = `
            <div class="clip-bin-item-thumb">
                ${thumbSrc ? `<img src="${thumbSrc}" alt="">` : ''}
                <span class="duration-badge">${formatDuration(clip.duration)}</span>
            </div>
            <div class="clip-bin-item-info">
                <div class="clip-bin-item-name">${clip.filename}</div>
                <div class="clip-bin-item-meta">${clipData.width || '?'}×${clipData.height || '?'} · ${(clipData.file_size / 1048576).toFixed(1)}MB</div>
            </div>
            <button class="clip-bin-item-remove" onclick="FilmEditor.removeClip(${clip.id}, this)" title="Remove clip">
                <i class="fa fa-times"></i>
            </button>
        `;

        clip.element = div;
        document.getElementById('clipBin').appendChild(div);
        updateClipCount();
    }

    function removeClip(clipId, btnEl) {
        Swal.fire({
            icon: 'warning',
            title: 'Remove Clip?',
            text: 'This will also remove it from the timeline.',
            confirmButtonText: 'Remove',
            confirmButtonColor: '#fe0278',
            showCancelButton: true,
        }).then(function(result) {
            if (!result.isConfirmed) return;

            const formData = new FormData();
            formData.append('clip_id', clipId);

            ajax(window.EDITOR_CONFIG.deleteClipUrl, 'POST', null, function(err, resp) {
                // We'll use FormData for delete too
            });

            // Delete via form data POST
            const xhr = new XMLHttpRequest();
            xhr.open('POST', window.EDITOR_CONFIG.deleteClipUrl, true);
            xhr.setRequestHeader('X-CSRF-TOKEN', window.EDITOR_CONFIG.csrfToken);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                try {
                    const resp = JSON.parse(xhr.responseText);
                    if (resp.success) {
                        // Remove from state
                        state.clips = state.clips.filter(c => c.id !== clipId);
                        // Remove from timeline
                        state.timeline.clips = state.timeline.clips.filter(c => c.clipId !== clipId);
                        // Remove DOM element
                        const item = btnEl.closest('.clip-bin-item');
                        if (item) item.remove();

                        updateClipCount();
                        renderTimeline();
                        markDirty();

                        Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
                            icon: 'success', title: 'Clip removed' });
                    }
                } catch(e) {}
            };
            xhr.send('clip_id=' + clipId);
        });
    }

    function updateClipCount() {
        const countEl = document.getElementById('clipCount');
        if (countEl) countEl.textContent = state.clips.length + ' clip' + (state.clips.length !== 1 ? 's' : '');
    }

    // ── Drag & Drop ───────────────────────────────────────────────────────
    function setupDragDrop() {
        const dropzone = document.getElementById('uploadDropzone');
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });
        dropzone.addEventListener('dragleave', function() {
            dropzone.classList.remove('dragover');
        });
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            if (e.dataTransfer.files.length > 0) {
                handleFileSelect(e.dataTransfer.files);
            }
        });

        // Double-click on clip bin items to add to timeline
        document.getElementById('clipBin').addEventListener('dblclick', function(e) {
            const item = e.target.closest('.clip-bin-item');
            if (item) {
                addClipToTimeline(parseInt(item.dataset.clipId));
            }
        });
    }

    function onClipDragStart(e, el) {
        el.classList.add('dragging');
        e.dataTransfer.setData('text/plain', el.dataset.clipId);
        e.dataTransfer.effectAllowed = 'copy';
    }

    function onClipDragEnd(e, el) {
        el.classList.remove('dragging');
    }

    function onTimelineDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
        document.getElementById('timelineTrackContainer').style.borderColor = 'rgba(254,2,120,0.4)';
    }

    function onTimelineDragLeave(e) {
        document.getElementById('timelineTrackContainer').style.borderColor = '';
    }

    function onTimelineDrop(e) {
        e.preventDefault();
        document.getElementById('timelineTrackContainer').style.borderColor = '';
        const clipId = parseInt(e.dataTransfer.getData('text/plain'));
        if (clipId) {
            addClipToTimeline(clipId);
        }
    }

    // ── Timeline Management ───────────────────────────────────────────────
    function addClipToTimeline(clipId) {
        const clip = state.clips.find(c => c.id === clipId);
        if (!clip) return;

        const timelineClip = {
            clipId: clip.id,
            inPoint: 0,
            outPoint: clip.duration,
            position: state.timeline.clips.length,
            transition: { type: 'cut', duration: 0 },
        };

        state.timeline.clips.push(timelineClip);
        renderTimeline();
        markDirty();

        Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000,
            icon: 'success', title: `Added "${clip.filename}" to timeline` });
    }

    function renderTimeline() {
        const track = document.getElementById('timelineTrack');
        const empty = document.getElementById('timelineEmpty');
        track.innerHTML = '';

        if (state.timeline.clips.length === 0) {
            empty.style.display = '';
            updateTotalDuration();
            return;
        }
        empty.style.display = 'none';

        state.timeline.clips.forEach(function(tc, index) {
            const clip = state.clips.find(c => c.id === tc.clipId);
            if (!clip) return;

            // Add splice point between clips (except before first)
            if (index > 0) {
                const splice = document.createElement('div');
                splice.className = 'timeline-splice';
                splice.dataset.index = index;
                splice.innerHTML = '<div class="timeline-splice-line"></div><span class="timeline-splice-icon"><i class="fa fa-exchange"></i></span>';
                splice.onclick = function() { selectSplice(index); };
                track.appendChild(splice);
            }

            // Create film strip clip
            const clipDuration = (tc.outPoint || clip.duration) - (tc.inPoint || 0);
            const clipWidth = Math.max(clipDuration * state.pixelsPerSecond, 60);

            const clipEl = document.createElement('div');
            clipEl.className = 'timeline-clip' + (index === state.selectedClipIndex ? ' selected' : '');
            clipEl.style.width = clipWidth + 'px';
            clipEl.dataset.index = index;
            clipEl.dataset.clipId = tc.clipId;

            // Thumbnail frames
            const framesContainer = document.createElement('div');
            framesContainer.className = 'timeline-clip-frames';

            const numFrames = Math.max(Math.floor(clipWidth / 50), 1);
            const thumbnails = clip.thumbnails || [];

            for (let i = 0; i < numFrames; i++) {
                const img = document.createElement('img');
                img.className = 'timeline-clip-frame';
                const thumbIndex = Math.min(Math.floor(i / numFrames * thumbnails.length), thumbnails.length - 1);
                img.src = thumbnails[thumbIndex] || '';
                img.alt = '';
                img.onerror = function() { this.style.background = 'var(--film-strip-bg)'; this.src = ''; };
                framesContainer.appendChild(img);
            }
            clipEl.appendChild(framesContainer);

            // Clip label
            const label = document.createElement('div');
            label.className = 'timeline-clip-label';
            label.textContent = clip.filename;
            clipEl.appendChild(label);

            // Trim handles
            const trimLeft = document.createElement('div');
            trimLeft.className = 'timeline-clip-trim-handle left';
            trimLeft.onmousedown = function(e) { startTrim(e, index, 'left'); };
            clipEl.appendChild(trimLeft);

            const trimRight = document.createElement('div');
            trimRight.className = 'timeline-clip-trim-handle right';
            trimRight.onmousedown = function(e) { startTrim(e, index, 'right'); };
            clipEl.appendChild(trimRight);

            // Click to select
            clipEl.onclick = function(e) {
                if (state.currentTool === 'razor') {
                    cutClipAt(index, e);
                } else {
                    selectTimelineClip(index);
                }
            };

            // Drag to reorder
            clipEl.draggable = true;
            clipEl.ondragstart = function(e) {
                e.dataTransfer.setData('timeline-index', index);
                clipEl.classList.add('dragging');
            };
            clipEl.ondragend = function() { clipEl.classList.remove('dragging'); };
            clipEl.ondragover = function(e) { e.preventDefault(); };
            clipEl.ondrop = function(e) {
                e.preventDefault();
                e.stopPropagation();
                const fromIndex = parseInt(e.dataTransfer.getData('timeline-index'));
                if (!isNaN(fromIndex) && fromIndex !== index) {
                    reorderClip(fromIndex, index);
                }
            };

            track.appendChild(clipEl);
        });

        updateTotalDuration();
        renderRuler();
        updatePlayhead();
    }

    function updateTotalDuration() {
        let total = 0;
        state.timeline.clips.forEach(function(tc) {
            total += (tc.outPoint || 0) - (tc.inPoint || 0);
        });
        state.totalDuration = total;

        document.getElementById('propTotalDuration').textContent = formatTime(total);
        document.getElementById('propClipCount').textContent = state.timeline.clips.length;

        // Update timeline track min-width
        const trackWidth = Math.max(total * state.pixelsPerSecond + 100, document.getElementById('timelineTrackContainer').offsetWidth);
        document.getElementById('timelineTrack').style.minWidth = trackWidth + 'px';
    }

    // ── Timeline Ruler ────────────────────────────────────────────────────
    function renderRuler() {
        const ruler = document.getElementById('timelineRuler');
        // Remove old marks
        ruler.querySelectorAll('.timeline-ruler-mark, .timeline-ruler-label').forEach(el => el.remove());

        const totalWidth = state.totalDuration * state.pixelsPerSecond + 100;
        const interval = state.pixelsPerSecond >= 60 ? 1 : 5; // Mark every 1 or 5 seconds

        for (let t = 0; t <= state.totalDuration + 5; t += interval) {
            const x = 20 + t * state.pixelsPerSecond;
            const mark = document.createElement('div');
            mark.className = 'timeline-ruler-mark ' + (t % 5 === 0 ? 'major' : 'minor');
            mark.style.left = x + 'px';
            mark.style.height = (t % 5 === 0 ? '12px' : '6px');
            ruler.appendChild(mark);

            if (t % 5 === 0) {
                const label = document.createElement('span');
                label.className = 'timeline-ruler-label';
                label.style.left = x + 'px';
                label.textContent = formatDuration(t);
                ruler.appendChild(label);
            }
        }
    }

    // ── Playhead & Playback ───────────────────────────────────────────────
    function setupTimelineClick() {
        document.getElementById('timelineRuler').addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left + this.parentElement.scrollLeft - 20;
            const time = Math.max(0, x / state.pixelsPerSecond);
            seekTo(time);
        });
    }

    function updatePlayhead() {
        const playhead = document.getElementById('timelinePlayhead');
        const x = 20 + state.playheadPosition * state.pixelsPerSecond;
        playhead.style.left = x + 'px';

        document.getElementById('previewTimecode').textContent = formatTime(state.playheadPosition);
    }

    function seekTo(time) {
        state.playheadPosition = Math.max(0, Math.min(time, state.totalDuration));
        updatePlayhead();
        updateVideoPlayback();
    }

    function togglePlay() {
        if (state.isPlaying) {
            pausePlayback();
        } else {
            startPlayback();
        }
    }

    function startPlayback() {
        if (state.totalDuration <= 0) {
            Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500,
                icon: 'info', title: 'Add clips to the timeline first' });
            return;
        }
        state.isPlaying = true;

        document.getElementById('playIcon').className = 'fa fa-pause';
        document.getElementById('sourceReel').classList.add('spinning');
        document.getElementById('masterReel').classList.add('spinning');

        // Load the correct clip at the current playhead position
        loadClipAtPlayhead();

        // rAF loop only updates the visual playhead — the video drives its own time
        function tick() {
            if (!state.isPlaying) return;

            const video = document.getElementById('previewVideo');

            // Sync playhead with actual video time
            if (video.src && !video.paused && !video.ended) {
                // Find which clip is loaded and compute global time from video.currentTime
                let elapsed = 0;
                for (let i = 0; i < state.timeline.clips.length; i++) {
                    const tc = state.timeline.clips[i];
                    const clipDuration = (tc.outPoint || 0) - (tc.inPoint || 0);
                    if (video.dataset.currentClipId == tc.clipId) {
                        state.playheadPosition = elapsed + (video.currentTime - (tc.inPoint || 0));
                        break;
                    }
                    elapsed += clipDuration;
                }
            }

            if (state.playheadPosition >= state.totalDuration) {
                state.playheadPosition = state.totalDuration;
                pausePlayback();
                return;
            }

            updatePlayhead();
            state.animationFrame = requestAnimationFrame(tick);
        }
        state.animationFrame = requestAnimationFrame(tick);
    }

    function pausePlayback() {
        state.isPlaying = false;
        if (state.animationFrame) cancelAnimationFrame(state.animationFrame);

        document.getElementById('playIcon').className = 'fa fa-play';
        document.getElementById('sourceReel').classList.remove('spinning');
        document.getElementById('masterReel').classList.remove('spinning');

        const video = document.getElementById('previewVideo');
        video.pause();
    }

    function skipToStart() {
        pausePlayback();
        seekTo(0);
    }

    function skipToEnd() {
        pausePlayback();
        seekTo(state.totalDuration);
    }

    function frameBack() {
        pausePlayback();
        seekTo(state.playheadPosition - (1/30));
    }

    function frameForward() {
        pausePlayback();
        seekTo(state.playheadPosition + (1/30));
    }

    // Load the right video clip for the current playhead position and seek to correct time
    function loadClipAtPlayhead() {
        const video = document.getElementById('previewVideo');
        const placeholder = document.getElementById('previewPlaceholder');

        let elapsed = 0;
        for (let i = 0; i < state.timeline.clips.length; i++) {
            const tc = state.timeline.clips[i];
            const clipDuration = (tc.outPoint || 0) - (tc.inPoint || 0);

            if (state.playheadPosition >= elapsed && state.playheadPosition < elapsed + clipDuration) {
                const clip = state.clips.find(c => c.id === tc.clipId);
                if (!clip) break;

                placeholder.style.display = 'none';
                video.style.display = 'block';

                const targetTime = (tc.inPoint || 0) + (state.playheadPosition - elapsed);

                if (String(video.dataset.currentClipId) !== String(tc.clipId)) {
                    // New clip — set src, wait for it to be ready, then seek and play
                    video.dataset.currentClipId = String(tc.clipId);

                    const onReady = function() {
                        video.currentTime = targetTime;
                        if (state.isPlaying) {
                            video.play().catch(function() {});
                        }
                    };

                    video.oncanplay = onReady;
                    video.src = clip.filepath;
                    video.load();
                } else {
                    // Same clip — just ensure we are playing
                    if (Math.abs(video.currentTime - targetTime) > 0.3) {
                        video.currentTime = targetTime;
                    }
                    if (state.isPlaying && video.paused) {
                        video.play().catch(function() {});
                    }
                }

                applyVideoFilters(video);
                return;
            }
            elapsed += clipDuration;
        }

        // Nothing at playhead
        placeholder.style.display = '';
        video.style.display = 'none';
        video.oncanplay = null;
        video.pause();
        video.removeAttribute('src');
        video.dataset.currentClipId = '';
    }

    function updateVideoPlayback() {
        loadClipAtPlayhead();
    }

    function applyVideoFilters(video) {
        const g = state.timeline.colorGrading || {};
        let filters = [];
        const brightness = (g.brightness || 0) / 100;
        const contrast = (g.contrast || 100) / 100;
        const saturation = (g.saturation || 100) / 100;
        const sepia = (g.sepia || 0) / 100;

        filters.push(`brightness(${1 + brightness})`);
        filters.push(`contrast(${contrast})`);
        filters.push(`saturate(${saturation})`);
        if (sepia > 0) filters.push(`sepia(${sepia})`);

        video.style.filter = filters.join(' ');
    }

    // ── Selection ─────────────────────────────────────────────────────────
    function selectTimelineClip(index) {
        state.selectedClipIndex = index;
        renderTimeline();

        const tc = state.timeline.clips[index];
        const clip = state.clips.find(c => c.id === tc.clipId);

        const propsSection = document.getElementById('selectedClipProps');
        propsSection.style.display = '';
        document.getElementById('propClipName').textContent = clip ? clip.filename : '—';
        document.getElementById('propClipDuration').textContent = formatTime((tc.outPoint || 0) - (tc.inPoint || 0));
        document.getElementById('propClipIn').textContent = formatTime(tc.inPoint || 0);
        document.getElementById('propClipOut').textContent = formatTime(tc.outPoint || 0);
    }

    function deleteSelectedClip() {
        if (state.selectedClipIndex < 0 || state.selectedClipIndex >= state.timeline.clips.length) {
            Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000,
                icon: 'info', title: 'Select a clip on the timeline first' });
            return;
        }

        state.timeline.clips.splice(state.selectedClipIndex, 1);
        // Reindex positions
        state.timeline.clips.forEach((c, i) => c.position = i);
        state.selectedClipIndex = -1;
        document.getElementById('selectedClipProps').style.display = 'none';
        renderTimeline();
        markDirty();
    }

    // ── Reorder ───────────────────────────────────────────────────────────
    function reorderClip(fromIndex, toIndex) {
        const clip = state.timeline.clips.splice(fromIndex, 1)[0];
        state.timeline.clips.splice(toIndex, 0, clip);
        state.timeline.clips.forEach((c, i) => c.position = i);
        renderTimeline();
        markDirty();
    }

    // ── Tools ─────────────────────────────────────────────────────────────
    function setTool(tool) {
        state.currentTool = tool;
        document.getElementById('btnSelect').classList.toggle('active', tool === 'select');
        document.getElementById('btnRazor').classList.toggle('active', tool === 'razor');
        document.body.classList.toggle('cursor-razor', tool === 'razor');
    }

    // ── Cut Tool ──────────────────────────────────────────────────────────
    function cutClipAt(index, mouseEvent) {
        const tc = state.timeline.clips[index];
        const clip = state.clips.find(c => c.id === tc.clipId);
        if (!clip) return;

        const clipEl = mouseEvent.currentTarget;
        const rect = clipEl.getBoundingClientRect();
        const clickX = mouseEvent.clientX - rect.left;
        const clipDuration = (tc.outPoint || clip.duration) - (tc.inPoint || 0);
        const clickTime = (clickX / clipEl.offsetWidth) * clipDuration;

        // Don't cut if too close to the edges
        if (clickTime < 0.5 || clickTime > clipDuration - 0.5) return;

        const cutPoint = (tc.inPoint || 0) + clickTime;

        // Create two new clips from the cut
        const clip1 = {
            clipId: tc.clipId,
            inPoint: tc.inPoint || 0,
            outPoint: cutPoint,
            position: index,
            transition: tc.transition || { type: 'cut', duration: 0 },
        };
        const clip2 = {
            clipId: tc.clipId,
            inPoint: cutPoint,
            outPoint: tc.outPoint || clip.duration,
            position: index + 1,
            transition: { type: 'cut', duration: 0 },
        };

        // Replace the original clip with the two new ones
        state.timeline.clips.splice(index, 1, clip1, clip2);
        state.timeline.clips.forEach((c, i) => c.position = i);

        renderTimeline();
        markDirty();

        Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000,
            icon: 'success', title: 'Clip split at ' + formatTime(cutPoint) });
    }

    // ── Trim Tool ─────────────────────────────────────────────────────────
    function startTrim(e, clipIndex, side) {
        e.preventDefault();
        e.stopPropagation();

        const tc = state.timeline.clips[clipIndex];
        const clip = state.clips.find(c => c.id === tc.clipId);
        if (!clip) return;

        const startX = e.clientX;
        const originalIn = tc.inPoint || 0;
        const originalOut = tc.outPoint || clip.duration;

        function onMouseMove(ev) {
            const deltaX = ev.clientX - startX;
            const deltaTime = deltaX / state.pixelsPerSecond;

            if (side === 'left') {
                tc.inPoint = Math.max(0, Math.min(originalIn + deltaTime, tc.outPoint - 0.5));
            } else {
                tc.outPoint = Math.max(tc.inPoint + 0.5, Math.min(originalOut + deltaTime, clip.duration));
            }
            renderTimeline();
        }

        function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
            markDirty();
        }

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    }

    // ── Transitions ───────────────────────────────────────────────────────
    function selectSplice(index) {
        // Apply current transition to this splice point
        if (index > 0 && index < state.timeline.clips.length) {
            state.timeline.clips[index].transition = {
                type: state.currentTransition.type,
                duration: state.currentTransition.type === 'cut' ? 0 : state.currentTransition.duration,
            };
            markDirty();

            Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000,
                icon: 'success', title: `Applied ${state.currentTransition.type} transition` });
        }
    }

    function setTransition(type, btnEl) {
        state.currentTransition.type = type;
        document.querySelectorAll('.transition-option').forEach(b => b.classList.remove('active'));
        if (btnEl) btnEl.classList.add('active');
    }

    function setTransitionDuration(val) {
        state.currentTransition.duration = parseFloat(val);
        document.getElementById('valTransDuration').textContent = parseFloat(val).toFixed(1) + 's';
    }

    // ── Colour Grading ────────────────────────────────────────────────────
    function loadGradingValues() {
        const g = state.timeline.colorGrading || {};
        setSlider('Brightness', g.brightness || 0);
        setSlider('Contrast', g.contrast !== undefined ? g.contrast * 100 : 100);
        setSlider('Saturation', g.saturation !== undefined ? g.saturation * 100 : 100);
        setSlider('Warmth', g.warmth || 0);
        setSlider('Sepia', g.sepia || 0);
    }

    function setSlider(name, val) {
        const slider = document.getElementById('slider' + name);
        const display = document.getElementById('val' + name);
        if (slider) slider.value = val;
        if (display) {
            display.textContent = name === 'Sepia' ? Math.round(val) + '%' : Math.round(val);
        }
    }

    function updateGrading(property, value) {
        value = parseFloat(value);
        const display = document.getElementById('val' + property.charAt(0).toUpperCase() + property.slice(1));

        if (property === 'brightness') {
            state.timeline.colorGrading.brightness = value;
            if (display) display.textContent = Math.round(value);
        } else if (property === 'contrast') {
            state.timeline.colorGrading.contrast = value / 100;
            if (display) display.textContent = Math.round(value);
        } else if (property === 'saturation') {
            state.timeline.colorGrading.saturation = value / 100;
            if (display) display.textContent = Math.round(value);
        } else if (property === 'warmth') {
            state.timeline.colorGrading.warmth = value;
            if (display) display.textContent = Math.round(value);
        } else if (property === 'sepia') {
            state.timeline.colorGrading.sepia = value;
            if (display) display.textContent = Math.round(value) + '%';
        }

        // Live preview
        const video = document.getElementById('previewVideo');
        if (video) applyVideoFilters(video);
        markDirty();
    }

    function resetGrading() {
        state.timeline.colorGrading = { brightness: 0, contrast: 1.0, saturation: 1.0, warmth: 0, sepia: 0 };
        loadGradingValues();
        const video = document.getElementById('previewVideo');
        if (video) video.style.filter = '';
        markDirty();
    }

    // ── Audio Track ───────────────────────────────────────────────────────
    function addAudioTrack() {
        document.getElementById('audioFileInput').click();
    }

    function handleAudioSelect(files) {
        if (files.length === 0) return;
        // For now, just store the audio info in timeline data
        // Full audio upload would need its own endpoint
        Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
            icon: 'info', title: 'Audio track support coming soon!' });
    }

    // ── Tab Switching ─────────────────────────────────────────────────────
    function switchTab(tabName, btnEl) {
        document.querySelectorAll('.editor-panel-tab').forEach(t => t.classList.remove('active'));
        btnEl.classList.add('active');

        document.getElementById('tabProperties').classList.toggle('hidden', tabName !== 'properties');
        document.getElementById('tabGrading').classList.toggle('hidden', tabName !== 'grading');
        document.getElementById('tabTransitions').classList.toggle('hidden', tabName !== 'transitions');
    }

    // ── Save ──────────────────────────────────────────────────────────────
    function markDirty() {
        state.isDirty = true;
        const statusEl = document.getElementById('saveStatus');
        statusEl.classList.add('unsaved');
        document.getElementById('saveStatusText').textContent = 'Unsaved changes';
    }

    function saveTimeline(silent) {
        const btn = document.getElementById('btnSave');
        btn.classList.add('saving');
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving…';

        ajax(window.EDITOR_CONFIG.saveUrl, 'POST', {
            timeline_data: state.timeline,
            total_duration: state.totalDuration,
        }, function(err, resp) {
            btn.classList.remove('saving');
            btn.innerHTML = '<i class="fa fa-save"></i> Save';

            if (err || !resp.success) {
                if (!silent) {
                    Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 4000,
                        icon: 'error', title: 'Failed to save: ' + ((resp && resp.message) || err) });
                }
                return;
            }

            state.isDirty = false;
            const statusEl = document.getElementById('saveStatus');
            statusEl.classList.remove('unsaved');
            document.getElementById('saveStatusText').textContent = 'Saved';

            if (!silent) {
                Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000,
                    icon: 'success', title: 'Project saved' });
            }
        });
    }

    // ── Export ─────────────────────────────────────────────────────────────
    function startExport() {
        if (state.timeline.clips.length === 0) {
            Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000,
                icon: 'warning', title: 'Add clips to the timeline before exporting' });
            return;
        }

        // Save first
        saveTimeline(true);

        Swal.fire({
            icon: 'question',
            title: 'Export Film?',
            text: 'This will render your timeline into a final MP4 video.',
            confirmButtonText: 'Start Export',
            confirmButtonColor: '#fe0278',
            showCancelButton: true,
        }).then(function(result) {
            if (!result.isConfirmed) return;

            // Show export modal
            document.getElementById('exportOverlay').classList.add('active');
            document.getElementById('exportPercent').textContent = '0%';
            document.getElementById('exportRingFill').style.strokeDashoffset = '351.86';
            document.getElementById('exportTitle').textContent = 'Exporting Your Film…';
            document.getElementById('exportSubtitle').textContent = 'Please wait while FFmpeg renders your masterpiece.';
            document.getElementById('exportStatusText').textContent = 'Initializing…';
            document.getElementById('exportDownloadBtn').style.display = 'none';

            // Trigger export
            ajax(window.EDITOR_CONFIG.exportUrl, 'POST', {}, function(err, resp) {
                if (err || !resp.success) {
                    document.getElementById('exportStatusText').textContent = 'Export failed: ' + ((resp && resp.message) || err);
                    return;
                }
                // Start polling for progress
                pollExportStatus();
            });
        });
    }

    function pollExportStatus() {
        state.exportPollTimer = setInterval(function() {
            ajax(window.EDITOR_CONFIG.exportStatusUrl, 'GET', null, function(err, resp) {
                if (err) return;

                const data = resp.data || resp;
                const progress = data.progress || 0;
                const status = data.status;

                // Update progress ring
                const circumference = 351.86;
                const offset = circumference - (progress / 100 * circumference);
                document.getElementById('exportRingFill').style.strokeDashoffset = offset;
                document.getElementById('exportPercent').textContent = Math.round(progress) + '%';
                document.getElementById('exportStatusText').textContent = 'Rendering… ' + Math.round(progress) + '%';

                if (status === 'completed') {
                    clearInterval(state.exportPollTimer);
                    document.getElementById('exportRingFill').style.strokeDashoffset = '0';
                    document.getElementById('exportPercent').textContent = '100%';
                    document.getElementById('exportTitle').textContent = 'Export Complete!';
                    document.getElementById('exportSubtitle').textContent = 'Your film is ready to download.';
                    document.getElementById('exportStatusText').textContent = 'Done!';
                    document.getElementById('exportDownloadBtn').style.display = 'inline-flex';
                    document.getElementById('exportDownloadBtn').href = window.EDITOR_CONFIG.downloadUrl;
                    document.getElementById('propStatus').textContent = 'Completed';
                } else if (status === 'failed') {
                    clearInterval(state.exportPollTimer);
                    document.getElementById('exportTitle').textContent = 'Export Failed';
                    document.getElementById('exportSubtitle').textContent = 'Something went wrong during rendering.';
                    document.getElementById('exportStatusText').textContent = 'Error occurred';
                    document.getElementById('propStatus').textContent = 'Failed';
                }
            });
        }, 3000); // Poll every 3 seconds
    }

    function closeExport() {
        document.getElementById('exportOverlay').classList.remove('active');
        if (state.exportPollTimer) clearInterval(state.exportPollTimer);
    }

    // ── Keyboard Shortcuts ────────────────────────────────────────────────
    function setupKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            // Don't trigger shortcuts when typing in an input
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

            switch(e.key) {
                case ' ': // Space = Play/Pause
                    e.preventDefault();
                    togglePlay();
                    break;
                case 's':
                    if (e.ctrlKey || e.metaKey) {
                        e.preventDefault();
                        saveTimeline();
                    }
                    break;
                case 'c':
                    if (!e.ctrlKey && !e.metaKey) setTool('razor');
                    break;
                case 'v':
                    if (!e.ctrlKey && !e.metaKey) setTool('select');
                    break;
                case 'Delete':
                case 'Backspace':
                    deleteSelectedClip();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    frameBack();
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    frameForward();
                    break;
                case 'Home':
                    e.preventDefault();
                    skipToStart();
                    break;
                case 'End':
                    e.preventDefault();
                    skipToEnd();
                    break;
            }
        });
    }

    // ── Timeline Zoom ─────────────────────────────────────────────────────
    function zoomTimeline(delta) {
        const newZoom = Math.max(20, Math.min(300, state.pixelsPerSecond + delta));
        setZoom(newZoom);
    }

    function setZoom(value) {
        state.pixelsPerSecond = Math.max(20, Math.min(300, value));

        // Sync slider and label
        const slider = document.getElementById('timelineZoomSlider');
        const label  = document.getElementById('timelineZoomLabel');
        if (slider) slider.value = state.pixelsPerSecond;
        if (label)  label.textContent = state.pixelsPerSecond + 'px/s';

        // Re-render timeline at new zoom level
        renderTimeline();
    }

    // ── Public API ────────────────────────────────────────────────────────
    return {
        init: init,
        handleFileSelect: handleFileSelect,
        removeClip: removeClip,
        onClipDragStart: onClipDragStart,
        onClipDragEnd: onClipDragEnd,
        onTimelineDragOver: onTimelineDragOver,
        onTimelineDragLeave: onTimelineDragLeave,
        onTimelineDrop: onTimelineDrop,
        togglePlay: togglePlay,
        skipToStart: skipToStart,
        skipToEnd: skipToEnd,
        frameBack: frameBack,
        frameForward: frameForward,
        setTool: setTool,
        deleteSelectedClip: deleteSelectedClip,
        saveTimeline: saveTimeline,
        startExport: startExport,
        closeExport: closeExport,
        switchTab: switchTab,
        updateGrading: updateGrading,
        resetGrading: resetGrading,
        setTransition: setTransition,
        setTransitionDuration: setTransitionDuration,
        addAudioTrack: addAudioTrack,
        handleAudioSelect: handleAudioSelect,
        zoomTimeline: zoomTimeline,
        setZoom: setZoom,
    };
})();

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    FilmEditor.init();
});
