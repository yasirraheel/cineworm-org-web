<?php

namespace App\Http\Controllers;

use App\EditingClip;
use App\EditingProject;
use App\Services\FFmpegService;
use App\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * VideoEditorController
 *
 * Handles all HTTP endpoints for the Cineworm Vintage Film Editor:
 *   – Project CRUD (list, create, edit, delete)
 *   – Clip upload / delete (AJAX)
 *   – Timeline save (AJAX)
 *   – Background FFmpeg export, progress polling, and download
 *
 * Access is gated behind the `film_editing_access` subscription feature.
 */
class VideoEditorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // =====================================================================
    //  Subscription guard — mirrors UserFilmController::ensureFilmUploadAccess
    // =====================================================================

    /**
     * Ensure the current user's subscription plan includes the
     * "film_editing_access" feature key.  Returns a redirect response
     * if access is denied, or null if the user may proceed.
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function ensureEditorAccess()
    {
        $user = Auth::user();

        // Admin / staff should use the admin dashboard instead
        if (in_array($user->usertype, ['Admin', 'Sub_Admin', 'Moderator'], true)) {
            return redirect('admin/dashboard');
        }

        $plan     = !empty($user->plan_id) ? SubscriptionPlan::find($user->plan_id) : null;
        $features = $plan ? $plan->getEffectiveFeatureKeys() : [];

        if (!in_array('film_editing_access', $features, true)) {
            \Session::flash('error_flash_message', 'Your subscription plan does not include Film Editing Access. Please upgrade your plan.');
            return redirect('dashboard');
        }

        return null;
    }

    // =====================================================================
    //  index — list all projects
    // =====================================================================

    /**
     * Show the user's list of editing projects.
     */
    public function index()
    {
        if ($redirect = $this->ensureEditorAccess()) {
            return $redirect;
        }

        $projects   = EditingProject::where('user_id', Auth::id())
                        ->orderBy('id', 'DESC')
                        ->paginate(12);
        $page_title = 'My Editor Projects';

        return view('pages.user.editor.index', compact('projects', 'page_title'));
    }

    // =====================================================================
    //  create — new project form
    // =====================================================================

    /**
     * Show the "Create New Project" form.
     */
    public function create()
    {
        if ($redirect = $this->ensureEditorAccess()) {
            return $redirect;
        }

        $page_title = 'Create New Project';

        return view('pages.user.editor.create', compact('page_title'));
    }

    // =====================================================================
    //  store — persist new project
    // =====================================================================

    /**
     * Validate and store a new editing project, then redirect to the editor.
     */
    public function store(Request $request)
    {
        if ($redirect = $this->ensureEditorAccess()) {
            return $redirect;
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $project = EditingProject::create([
            'user_id'       => Auth::id(),
            'title'         => trim($request->title),
            'description'   => $request->description ?? null,
            'timeline_data' => [], // empty timeline to start
            'status'        => 'draft',
        ]);

        return redirect('user/editor/' . $project->id);
    }

    // =====================================================================
    //  edit — open the editor workspace
    // =====================================================================

    /**
     * Load a project and its clips, then render the editor view.
     */
    public function edit($id)
    {
        if ($redirect = $this->ensureEditorAccess()) {
            return $redirect;
        }

        $project = EditingProject::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        $clips      = $project->clips()->orderBy('id')->get();
        $page_title = 'Edit: ' . $project->title;

        return view('pages.user.editor.edit', compact('project', 'clips', 'page_title'));
    }

    // =====================================================================
    //  saveTimeline — AJAX: persist the timeline JSON
    // =====================================================================

    /**
     * Save the timeline edit-decision-list JSON sent from the front-end.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveTimeline($id, Request $request)
    {
        $project = EditingProject::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        // Validate that timeline_data is an array (since Laravel decodes application/json automatically)
        $validator = Validator::make($request->all(), [
            'timeline_data' => 'required|array',
            'total_duration' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid timeline data.',
                'data'    => $validator->messages(),
            ], 422);
        }

        $project->update([
            'timeline_data'  => $request->timeline_data,
            'total_duration' => $request->total_duration ?? $project->total_duration,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Timeline saved successfully.',
            'data'    => null,
        ]);
    }

    // =====================================================================
    //  uploadClip — AJAX: upload a video clip
    // =====================================================================

    /**
     * Accept a video upload, probe it with FFmpeg, generate thumbnails,
     * and create an EditingClip record.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadClip($id, Request $request)
    {
        $project = EditingProject::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        // Validate the uploaded file
        $validator = Validator::make($request->all(), [
            'clip' => 'required|file|max:512000|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/webm,video/x-matroska',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file. Please upload a valid video (MP4, MOV, AVI, WebM, MKV) under 500 MB.',
                'data'    => $validator->messages(),
            ], 422);
        }

        $userId    = Auth::id();
        $projectId = $project->id;
        $file      = $request->file('clip');

        // Capture original filename before move() consumes the UploadedFile
        $originalName = $file->getClientOriginalName();

        // Generate a unique filename preserving the original extension
        $extension = $file->getClientOriginalExtension();
        $filename  = Str::random(20) . '.' . $extension;

        // Destination directories
        $clipDir      = public_path("user_editor/{$userId}/{$projectId}/clips");
        $thumbnailDir = public_path("user_editor/{$userId}/{$projectId}/thumbnails");

        // Ensure directories exist
        if (!is_dir($clipDir)) {
            mkdir($clipDir, 0755, true);
        }
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }

        // Move the uploaded file into the clips directory
        $file->move($clipDir, $filename);

        $absolutePath = $clipDir . '/' . $filename;
        $relativePath = "user_editor/{$userId}/{$projectId}/clips/{$filename}";

        // ── Probe video metadata with FFprobe ─────────────────────────────
        $ffmpeg = new FFmpegService();
        $info   = $ffmpeg->getVideoInfo($absolutePath);

        // ── Extract thumbnails for the timeline strip ─────────────────────
        // Create a subdirectory for this clip's thumbnails
        $clipThumbDir = $thumbnailDir . '/' . pathinfo($filename, PATHINFO_FILENAME);
        $thumbFiles   = $ffmpeg->extractThumbnails($absolutePath, $clipThumbDir, 20);

        // Convert filenames to relative paths for storage
        $thumbRelativePaths = array_map(function ($thumbFile) use ($userId, $projectId, $filename) {
            $stem = pathinfo($filename, PATHINFO_FILENAME);
            return "user_editor/{$userId}/{$projectId}/thumbnails/{$stem}/{$thumbFile}";
        }, $thumbFiles);

        // ── Create the database record ────────────────────────────────────
        $clip = EditingClip::create([
            'project_id'        => $projectId,
            'user_id'           => $userId,
            'original_filename' => $originalName,
            'file_path'         => $relativePath,
            'duration'          => $info['duration'],
            'width'             => $info['width'],
            'height'            => $info['height'],
            'fps'               => $info['fps'],
            'thumbnail_strip'   => $thumbRelativePaths,
            'file_size'         => filesize($absolutePath),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clip uploaded successfully.',
            'data'    => [
                'id'                => $clip->id,
                'original_filename' => $clip->original_filename,
                'file_path'         => $clip->file_path,
                'file_url'          => url($clip->file_path),
                'duration'          => $clip->duration,
                'width'             => $clip->width,
                'height'            => $clip->height,
                'fps'               => $clip->fps,
                'thumbnail_urls'    => $clip->getThumbnailUrls(),
                'file_size'         => $clip->file_size,
            ],
        ]);
    }

    // =====================================================================
    //  deleteClip — AJAX: remove a clip
    // =====================================================================

    /**
     * Delete a clip's files (video + thumbnails) and its database record.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteClip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clip_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Clip ID is required.',
                'data'    => null,
            ], 422);
        }

        $clip = EditingClip::where('id', $request->clip_id)
                    ->where('user_id', Auth::id())
                    ->first();

        if (!$clip) {
            return response()->json([
                'success' => false,
                'message' => 'Clip not found.',
                'data'    => null,
            ], 404);
        }

        // Delete the physical video file
        $clipPath = public_path($clip->file_path);
        if (file_exists($clipPath)) {
            unlink($clipPath);
        }

        // Delete thumbnail files
        if (!empty($clip->thumbnail_strip) && is_array($clip->thumbnail_strip)) {
            foreach ($clip->thumbnail_strip as $thumbPath) {
                $absThumb = public_path($thumbPath);
                if (file_exists($absThumb)) {
                    unlink($absThumb);
                }
            }

            // Remove the thumbnail subdirectory if it's now empty
            $thumbDir = dirname(public_path($clip->thumbnail_strip[0] ?? ''));
            if (is_dir($thumbDir) && count(glob($thumbDir . '/*')) === 0) {
                rmdir($thumbDir);
            }
        }

        $clip->delete();

        return response()->json([
            'success' => true,
            'message' => 'Clip deleted successfully.',
            'data'    => null,
        ]);
    }

    // =====================================================================
    //  exportProject — AJAX: start background export
    // =====================================================================

    /**
     * Validate the project has clips in its timeline and launch
     * the FFmpeg export as a background process.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportProject($id)
    {
        $project = EditingProject::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        $timeline = $project->timeline_data;

        // Must have at least one clip in the timeline
        if (empty($timeline) || empty($timeline['clips']) || count($timeline['clips']) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Your timeline must contain at least one clip before exporting.',
                'data'    => null,
            ], 422);
        }

        // Set status to exporting
        $project->update(['status' => 'exporting']);

        // Launch the background FFmpeg export
        $ffmpeg     = new FFmpegService();
        $outputFile = $ffmpeg->exportTimeline($project);

        if (!$outputFile) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed — could not process timeline. Please check your clips.',
                'data'    => null,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Export started. You can track progress on this page.',
            'data'    => [
                'status'    => 'exporting',
                'projectId' => $project->id,
            ],
        ]);
    }

    // =====================================================================
    //  exportStatus — AJAX: poll export progress
    // =====================================================================

    /**
     * Check the current export progress by parsing the FFmpeg progress log.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportStatus($id)
    {
        $project = EditingProject::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        $userId    = $project->user_id;
        $projectId = $project->id;
        $logFile   = public_path("user_editor/{$userId}/{$projectId}/exports/ffmpeg_progress.log");

        $ffmpeg   = new FFmpegService();
        $progress = $ffmpeg->getExportProgress($logFile);

        // Calculate percentage from microseconds if total_duration is known
        $percentage = 0;
        if ($progress['finished']) {
            $percentage = 100;

            // If FFmpeg finished, also verify the output file exists and update status
            if ($project->status === 'exporting') {
                $exportedPath = public_path($project->exported_file ?? '');
                if ($project->exported_file && file_exists($exportedPath)) {
                    $project->update(['status' => 'completed']);
                } else {
                    $project->update(['status' => 'failed']);
                }
            }
        } elseif ($project->total_duration > 0 && $progress['progress'] > 0) {
            // out_time_us is in microseconds, total_duration is in seconds
            $percentage = min(99, round(($progress['progress'] / ($project->total_duration * 1000000)) * 100));
        }

        // Build download URL if completed
        $downloadUrl = null;
        if ($project->status === 'completed' && $project->exported_file) {
            $downloadUrl = url("user/editor/{$projectId}/download");
        }

        return response()->json([
            'success' => true,
            'message' => 'Export status retrieved.',
            'data'    => [
                'status'      => $project->fresh()->status,
                'progress'    => $percentage,
                'downloadUrl' => $downloadUrl,
            ],
        ]);
    }

    // =====================================================================
    //  downloadExport — serve the exported MP4
    // =====================================================================

    /**
     * Return a file download response for the completed export.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadExport($id)
    {
        $project = EditingProject::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        if (!$project->exported_file || !file_exists(public_path($project->exported_file))) {
            abort(404, 'Export file not found.');
        }

        $filePath     = public_path($project->exported_file);
        $downloadName = Str::slug($project->title) . '.mp4';

        return response()->download($filePath, $downloadName);
    }

    // =====================================================================
    //  destroy — delete entire project
    // =====================================================================

    /**
     * Delete the entire project: all files (clips, thumbnails, exports)
     * and all database records.
     */
    public function destroy($id)
    {
        if ($redirect = $this->ensureEditorAccess()) {
            return $redirect;
        }

        $project = EditingProject::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        $userId    = $project->user_id;
        $projectId = $project->id;

        // Remove the entire project directory from disk
        $projectDir = public_path("user_editor/{$userId}/{$projectId}");
        if (is_dir($projectDir)) {
            File::deleteDirectory($projectDir);
        }

        // Database cleanup — clips are cascade-deleted via FK, but let's be explicit
        $project->clips()->delete();
        $project->delete();

        \Session::flash('flash_message', 'Project deleted successfully.');

        return redirect('user/editor');
    }
}
