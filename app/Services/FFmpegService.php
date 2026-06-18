<?php

namespace App\Services;

use App\EditingClip;
use App\EditingProject;
use Illuminate\Support\Facades\Log;

/**
 * FFmpegService
 *
 * Handles all FFmpeg / FFprobe interactions for the Cineworm Vintage Film Editor.
 *   – Probing video metadata (duration, resolution, fps)
 *   – Extracting evenly-spaced thumbnail strips for the timeline UI
 *   – Building and launching complex filter_complex exports with
 *     trim, crossfade transitions, colour grading, and audio mixing
 *   – Parsing progress logs for real-time export status
 */
class FFmpegService
{
    // ── Binary paths on the Hostinger server ──────────────────────────────
    const FFMPEG_PATH  = '/home/u273790872/bin/ffmpeg';
    const FFPROBE_PATH = '/home/u273790872/bin/ffprobe';

    // =====================================================================
    //  getVideoInfo — probe a video file for metadata
    // =====================================================================

    /**
     * Run ffprobe on a video file and return its metadata.
     *
     * @param  string $videoPath  Absolute path to the video file
     * @return array  ['duration' => float, 'width' => int, 'height' => int, 'fps' => float]
     */
    public function getVideoInfo(string $videoPath): array
    {
        $defaults = [
            'duration' => 0,
            'width'    => 0,
            'height'   => 0,
            'fps'      => 0,
        ];

        if (!file_exists($videoPath)) {
            Log::warning("FFmpegService::getVideoInfo – file not found: {$videoPath}");
            return $defaults;
        }

        // Run ffprobe to get stream and format info as JSON
        $cmd = escapeshellarg(self::FFPROBE_PATH)
             . ' -v quiet -print_format json -show_format -show_streams '
             . escapeshellarg($videoPath)
             . ' 2>&1';

        $output = shell_exec($cmd);
        $data   = json_decode($output, true);

        if (!$data) {
            Log::warning("FFmpegService::getVideoInfo – ffprobe returned no data for: {$videoPath}");
            return $defaults;
        }

        // Extract duration from format-level info
        $duration = floatval($data['format']['duration'] ?? 0);

        // Find the first video stream for resolution and fps
        $width  = 0;
        $height = 0;
        $fps    = 0;

        if (!empty($data['streams'])) {
            foreach ($data['streams'] as $stream) {
                if (($stream['codec_type'] ?? '') === 'video') {
                    $width  = intval($stream['width'] ?? 0);
                    $height = intval($stream['height'] ?? 0);

                    // fps may be in r_frame_rate (e.g. "30000/1001") or avg_frame_rate
                    $fpsRaw = $stream['r_frame_rate'] ?? $stream['avg_frame_rate'] ?? '0/1';
                    $fpsParts = explode('/', $fpsRaw);
                    if (count($fpsParts) === 2 && floatval($fpsParts[1]) > 0) {
                        $fps = round(floatval($fpsParts[0]) / floatval($fpsParts[1]), 2);
                    } else {
                        $fps = floatval($fpsRaw);
                    }
                    break; // use first video stream only
                }
            }
        }

        return compact('duration', 'width', 'height', 'fps');
    }

    // =====================================================================
    //  extractThumbnails — generate a strip of evenly-spaced frame images
    // =====================================================================

    /**
     * Extract $count evenly-spaced thumbnails from a video, scaled to 160px wide.
     *
     * @param  string $videoPath  Absolute path to the video file
     * @param  string $outputDir  Absolute path to write thumbnail JPGs into
     * @param  int    $count      Number of thumbnails to extract (default 20)
     * @return array  Array of relative filenames (e.g. ['thumb_001.jpg', ...])
     */
    public function extractThumbnails(string $videoPath, string $outputDir, int $count = 20): array
    {
        // Ensure output directory exists
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Get the video duration so we can compute even intervals
        $info     = $this->getVideoInfo($videoPath);
        $duration = $info['duration'];

        if ($duration <= 0) {
            Log::warning("FFmpegService::extractThumbnails – zero duration, skipping: {$videoPath}");
            return [];
        }

        $filenames = [];
        $interval  = $duration / $count;

        for ($i = 0; $i < $count; $i++) {
            // Seek to the computed timestamp
            $timestamp = round($i * $interval, 2);
            $filename  = sprintf('thumb_%03d.jpg', $i + 1);
            $outPath   = $outputDir . '/' . $filename;

            // -ss before -i for fast seeking, scale to 160px wide keeping aspect ratio
            $cmd = escapeshellarg(self::FFMPEG_PATH)
                 . ' -y -ss ' . escapeshellarg((string) $timestamp)
                 . ' -i ' . escapeshellarg($videoPath)
                 . ' -vframes 1 -vf "scale=160:-1"'
                 . ' -q:v 3'
                 . ' ' . escapeshellarg($outPath)
                 . ' 2>&1';

            shell_exec($cmd);

            if (file_exists($outPath)) {
                $filenames[] = $filename;
            }
        }

        return $filenames;
    }

    // =====================================================================
    //  exportTimeline — render the full project timeline to MP4
    // =====================================================================

    /**
     * Build and launch an FFmpeg export from the project's timeline_data JSON.
     * The command runs as a background process so it does not block the web request.
     *
     * Timeline JSON structure expected:
     * {
     *   "clips": [{ "clipId": int, "inPoint": float, "outPoint": float,
     *               "position": int, "transition": { "type": "crossfade", "duration": float } }],
     *   "audioTracks": [{ "filePath": string, "startTime": float, "volume": float }],
     *   "colorGrading": { "brightness": float, "contrast": float, "saturation": float }
     * }
     *
     * @param  EditingProject $project
     * @return string|null  Path to the output file, or null on validation failure
     */
    public function exportTimeline(EditingProject $project): ?string
    {
        $timeline = $project->timeline_data;

        if (empty($timeline['clips'])) {
            $project->update(['status' => 'failed']);
            Log::error("FFmpegService::exportTimeline – no clips in timeline for project #{$project->id}");
            return null;
        }

        $userId    = $project->user_id;
        $projectId = $project->id;
        $baseDir   = public_path("user_editor/{$userId}/{$projectId}");
        $exportDir = $baseDir . '/exports';
        $logFile   = $exportDir . '/ffmpeg_progress.log';

        // Ensure export directory exists
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $outputFile = $exportDir . '/export_' . time() . '.mp4';

        // ── Sort clips by position ────────────────────────────────────────
        $clips = $timeline['clips'];
        usort($clips, fn($a, $b) => ($a['position'] ?? 0) <=> ($b['position'] ?? 0));

        // ── Colour grading defaults ───────────────────────────────────────
        $cg = $timeline['colorGrading'] ?? [];
        $brightness = floatval($cg['brightness'] ?? 0);
        $contrast   = floatval($cg['contrast']   ?? 1.0);
        $saturation = floatval($cg['saturation'] ?? 1.0);

        // Build the eq filter string for colour grading
        $eqFilter = "eq=brightness={$brightness}:contrast={$contrast}:saturation={$saturation}";

        // ── Build FFmpeg command ──────────────────────────────────────────
        $inputs       = [];   // -i arguments
        $filterParts  = [];   // filter_complex segments
        $clipLabels   = [];   // labels for the final concatenation
        $inputIndex   = 0;

        foreach ($clips as $idx => $clip) {
            // Look up the actual clip record for its file path
            $clipRecord = EditingClip::find($clip['clipId'] ?? 0);
            if (!$clipRecord || !file_exists(public_path($clipRecord->file_path))) {
                Log::warning("FFmpegService::exportTimeline – clip #{$clip['clipId']} not found, skipping");
                continue;
            }

            $filePath = public_path($clipRecord->file_path);
            $inputs[] = '-i ' . escapeshellarg($filePath);

            $inPoint  = floatval($clip['inPoint']  ?? 0);
            $outPoint = floatval($clip['outPoint'] ?? $clipRecord->duration);

            // Trim and apply colour grading to each clip
            $trimLabel = "v{$idx}";
            $audioLabel = "a{$idx}";
            $filterParts[] = "[{$inputIndex}:v]trim=start={$inPoint}:end={$outPoint},setpts=PTS-STARTPTS,{$eqFilter}[{$trimLabel}]";
            $filterParts[] = "[{$inputIndex}:a]atrim=start={$inPoint}:end={$outPoint},asetpts=PTS-STARTPTS[{$audioLabel}]";

            $clipLabels[] = ['video' => $trimLabel, 'audio' => $audioLabel];
            $inputIndex++;
        }

        if (empty($clipLabels)) {
            $project->update(['status' => 'failed']);
            Log::error("FFmpegService::exportTimeline – no valid clips found for project #{$project->id}");
            return null;
        }

        // ── Apply crossfade transitions between consecutive clips ─────────
        $finalVideo = $clipLabels[0]['video'];
        $finalAudio = $clipLabels[0]['audio'];

        if (count($clipLabels) > 1) {
            for ($i = 1; $i < count($clipLabels); $i++) {
                $prevClip = $clips[$i - 1] ?? [];
                $transition = $prevClip['transition'] ?? null;

                $xfadeDuration = 0;
                if ($transition && ($transition['type'] ?? '') === 'crossfade') {
                    $xfadeDuration = floatval($transition['duration'] ?? 1.0);
                }

                $outLabel  = "xv{$i}";
                $outALabel = "xa{$i}";

                if ($xfadeDuration > 0) {
                    // Video crossfade
                    $filterParts[] = "[{$finalVideo}][{$clipLabels[$i]['video']}]xfade=transition=fade:duration={$xfadeDuration}:offset=0[{$outLabel}]";
                    // Audio crossfade
                    $filterParts[] = "[{$finalAudio}][{$clipLabels[$i]['audio']}]acrossfade=d={$xfadeDuration}[{$outALabel}]";
                } else {
                    // Simple concatenation (no transition)
                    $filterParts[] = "[{$finalVideo}][{$clipLabels[$i]['video']}]concat=n=2:v=1:a=0[{$outLabel}]";
                    $filterParts[] = "[{$finalAudio}][{$clipLabels[$i]['audio']}]concat=n=2:v=0:a=1[{$outALabel}]";
                }

                $finalVideo = $outLabel;
                $finalAudio = $outALabel;
            }
        }

        // ── Handle additional audio tracks ────────────────────────────────
        $audioTracks = $timeline['audioTracks'] ?? [];
        if (!empty($audioTracks)) {
            foreach ($audioTracks as $aIdx => $audioTrack) {
                $audioFile = $audioTrack['filePath'] ?? '';
                if ($audioFile && file_exists(public_path($audioFile))) {
                    $inputs[] = '-i ' . escapeshellarg(public_path($audioFile));
                    $volume = floatval($audioTrack['volume'] ?? 1.0);

                    $bgLabel  = "bg{$aIdx}";
                    $mixLabel = "mix{$aIdx}";

                    // Apply volume to the background track
                    $filterParts[] = "[{$inputIndex}:a]volume={$volume}[{$bgLabel}]";
                    // Mix it with the current final audio
                    $filterParts[] = "[{$finalAudio}][{$bgLabel}]amix=inputs=2:duration=first[{$mixLabel}]";

                    $finalAudio = $mixLabel;
                    $inputIndex++;
                }
            }
        }

        // ── Assemble the full FFmpeg command ──────────────────────────────
        $filterComplex = implode('; ', $filterParts);
        $inputsStr     = implode(' ', $inputs);

        $cmd = escapeshellarg(self::FFMPEG_PATH)
             . ' -y'
             . ' ' . $inputsStr
             . ' -filter_complex "' . $filterComplex . '"'
             . ' -map "[' . $finalVideo . ']" -map "[' . $finalAudio . ']"'
             . ' -c:v libx264 -preset medium -crf 23'
             . ' -c:a aac -b:a 192k'
             . ' -movflags +faststart'
             . ' ' . escapeshellarg($outputFile)
             . ' -progress ' . escapeshellarg($logFile)
             . ' > /dev/null 2>&1 &';

        Log::info("FFmpegService::exportTimeline – launching background export for project #{$project->id}");
        Log::info("FFmpegService::exportTimeline – command: {$cmd}");

        // Launch as a background process (non-blocking)
        exec($cmd);

        // Store the expected output path on the project
        $project->update([
            'exported_file' => "user_editor/{$userId}/{$projectId}/exports/" . basename($outputFile),
        ]);

        return $outputFile;
    }

    // =====================================================================
    //  getExportProgress — parse FFmpeg progress log for completion %
    // =====================================================================

    /**
     * Parse an FFmpeg progress log file and estimate the export percentage.
     *
     * FFmpeg writes lines like:
     *   out_time_us=12345678
     *   progress=continue
     *   progress=end
     *
     * @param  string $logFile  Absolute path to the progress log
     * @return array  ['progress' => int (0-100), 'finished' => bool]
     */
    public function getExportProgress(string $logFile): array
    {
        $result = ['progress' => 0, 'finished' => false];

        if (!file_exists($logFile)) {
            return $result;
        }

        $content = file_get_contents($logFile);

        // Check if FFmpeg has signalled completion
        if (strpos($content, 'progress=end') !== false) {
            $result['progress'] = 100;
            $result['finished'] = true;
            return $result;
        }

        // Extract the latest out_time_us value (microseconds of output written so far)
        preg_match_all('/out_time_us=(\d+)/', $content, $matches);
        if (!empty($matches[1])) {
            $lastTimeUs = intval(end($matches[1]));
            $result['progress'] = $lastTimeUs; // Controller will compute % from total_duration
        }

        return $result;
    }
}
