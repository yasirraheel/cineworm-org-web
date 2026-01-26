<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Movies;
use App\Genres;
use App\Language;
use App\RecentlyWatched;
use App\ActorDirector;

use App\Http\Requests;
use App\Models\GoogleDriveApi;
use App\Models\Thumbnail;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session as FacadesSession;
class MoviesController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');

        parent::__construct();
        check_verify_purchase();
    }

    public function movies_list()
    {
        if (Auth::User()->usertype != "Admin" && Auth::User()->usertype != "Sub_Admin" && Auth::User()->usertype != "Moderator") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = trans('words.movies_text');
        $language_list = Language::orderBy('language_name')->get();
        $genres_list = Genres::orderBy('genre_name')->get();

        $query = Movies::query();

        if (Auth::User()->usertype == "Sub_Admin") {
            $query->where('added_by', Auth::id());
        }

        if (isset($_GET['s'])) {
            $keyword = $_GET['s'];
            $query->where("video_title", "LIKE", "%$keyword%");
            $movies_list = $query->orderBy('video_title')->paginate(12);
            $movies_list->appends(\Request::only('s'))->links();
        } else if (isset($_GET['language_id'])) {
            $language_id = $_GET['language_id'];
            $query->where("movie_lang_id", "=", $language_id);
            $movies_list = $query->orderBy('id', 'DESC')->paginate(12);
            $movies_list->appends(\Request::only('language_id'))->links();
        } else if (isset($_GET['genres_id'])) {
            $genres_id = $_GET['genres_id'];
            $query->whereRaw("find_in_set('$genres_id',movie_genre_id)");
            $movies_list = $query->orderBy('id', 'DESC')->paginate(12);
            $movies_list->appends(\Request::only('genres_id'))->links();
        } else {
            $movies_list = $query->orderBy('id', 'DESC')->paginate(12);
        }

        return view('admin.pages.movies.list', compact('page_title', 'movies_list', 'language_list', 'genres_list'));
    }

    public function addMovie()
    {
        // Check if screen width indicates mobile device
        // $screenWidth = request()->input('screen_width');

        // if ($screenWidth && $screenWidth <= 768) { // Assuming 768px or less is mobile/tablet
        //     \Session::flash('flash_message', 'Access denied. For better experience, please use a desktop device.');

        //     return redirect()->back();
        // }

        // Check user type
        if (Auth::User()->usertype != "Admin" and Auth::User()->usertype != "Sub_Admin" and Auth::User()->usertype != "Moderator") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = trans('words.add_movie');
        $language_list = Language::orderBy('language_name')->get();
        $genre_list = Genres::orderBy('genre_name')->get();
        $actor_list = ActorDirector::where('ad_type', 'actor')->orderBy('ad_name')->get();
        $director_list = ActorDirector::where('ad_type', 'director')->orderBy('ad_name')->get();

        return view('admin.pages.movies.addedit', compact('page_title', 'language_list', 'genre_list', 'actor_list', 'director_list'));
    }

    public function addnew(Request $request)
    {
        try {
            if (!empty($request->actors)) {
                $actorNames = explode(',', $request->actors);
                foreach ($actorNames as $actorName) {
                    $actorId = $this->addActorOrDirector('actor', trim($actorName));
                    if ($actorId) {
                        $actorIds[] = $actorId;
                    }
                }
            }
            if (!empty($request->director)) {
                $directorNames = explode(',', $request->director);
                foreach ($directorNames as $directorName) {
                    $directorId = $this->addActorOrDirector('director', trim($directorName));
                    if ($directorId) {
                        $directorIds[] = $directorId;
                    }
                }
            }

            $video_type = $request->video_type;

            if($video_type == 'Local') {
                $video_url = $request->video_url_local;
            } else if($video_type == 'URL') {
                $video_url = $request->video_url;
            } else if($video_type == 'HLS') {
                $video_url = $request->video_url_hls;
            } else if($video_type == 'DASH') {
                $video_url = $request->video_url_dash;
            } else if($video_type == 'Embed') {
                $video_url = $request->video_embed_code;
            } else {
                $video_url = $request->video_url;
            }

            $video_url = trim($video_url);
            $fileId = null;
            $video_image = '';

            // Check if video URL is YouTube or Vimeo
            if (stripos($video_url, 'youtube.com') !== false || stripos($video_url, 'youtu.be') !== false) {
                $video_image = $this->getVideoThumbnail($video_url) ?? '';  // Get YouTube thumbnail
                $video_type = 'YouTube';

            } elseif (stripos($video_url, 'vimeo.com') !== false) {
                $video_image = $this->getVideoThumbnail($video_url) ?? '';  // Get Vimeo thumbnail
                $video_type = 'Vimeo';

            } elseif (stripos($video_url, 'drive.google.com') !== false || stripos($video_url, 'googleapis.com') !== false) {
                $video_type = 'GoogleDrive';
                // Handle Google Drive URL
                $google_drive_api = $this->getRandomApiKey();
                GoogleDriveApi::where('api_key', $google_drive_api)->increment('calls');

                $googleDriveUrl = $video_url;

                // Check if URL is already a Google Drive streaming URL
                if (stripos($googleDriveUrl, 'https://www.googleapis.com/drive/v3/files/') !== false) {
                    $video_url = $googleDriveUrl;
                    preg_match("/files\/(.*?)\?/", $googleDriveUrl, $matches);
                    $fileId = $matches[1] ?? null;
                } else {
                    preg_match("/\/d\/(.*?)\//", $googleDriveUrl, $matches);

                    if (isset($matches[1])) {
                        $fileId = $matches[1];
                        $video_url = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key={$google_drive_api}";
                    } else {
                        session()->flash('error', 'Invalid Google Drive URL');
                        return back();
                    }
                }

            }
            // Else: Keep the original video_type (Local, URL, HLS, Embed, etc.) without throwing error



            // Validate the form input
            $data = \Request::except(['_token']);
            $inputs = $request->all();
            $rule = [
                'genres' => 'required',
                'video_title' => 'required'
            ];

            $validator = \Validator::make($data, $rule);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator->messages());
            }

            // If updating an existing movie, retrieve it, else create a new movie object.
            $movie_obj = !empty($inputs['id']) ? Movies::findOrFail($inputs['id']) : new Movies;
            session()->put('movie_obj', $movie_obj);

            // Generate the video slug
            $video_slug = Str::slug($inputs['video_title'], '-', null);

            // Fill in movie object data
            $movie_obj->funding_url = $inputs['funding_url'];
            $movie_obj->movie_lang_id = 0;
            $movie_obj->movie_genre_id = implode(',', $inputs['genres']);
            $movie_obj->video_title = addslashes($inputs['video_title']);
            $movie_obj->video_slug = $video_slug;
            $movie_obj->video_description = addslashes($inputs['video_description']);

            $movie_obj->actor_id = isset($actorIds) ? implode(',', $actorIds) : null;

            $movie_obj->director_id = isset($directorIds) ? implode(',', $directorIds) : null;

            // Handle poster link if provided
            if (isset($inputs['poster_link']) && $inputs['poster_link'] != '') {
                $image_source = $inputs['poster_link'];
                $save_to = !empty($inputs['video_image']) ? public_path('/upload/images/' . $inputs['video_image']) : 'NA';

                grab_image($image_source, $save_to);
                $movie_obj->video_image = !empty($inputs['video_image']) ? 'upload/images/' . $inputs['video_image'] : 'NA';

            } else {
                // Use the extracted thumbnail (YouTube, Vimeo, or default image)
                $movie_obj->video_image = $video_image;
                $movie_obj->video_image_thumb = $video_image;
            }

            // Other fields
            $movie_obj->added_by = Auth::User()->id;
            $movie_obj->file_id = $fileId;
            $movie_obj->webpage_url = $inputs['webpage_url'];

            $movie_obj->status = auth()->user()->usertype == 'Admin' || auth()->user()->usertype == 'Moderator' ? $inputs['status'] : 0;

            $movie_obj->video_url = $video_url;
            $movie_obj->video_type = $video_type;

            // Optional fields for video quality, downloads, and subtitles
            if (isset($inputs['video_quality'])) {
                $movie_obj->video_quality = $inputs['video_quality'];
            }

            if (isset($inputs['download_enable'])) {
                $movie_obj->download_enable = $inputs['download_enable'];
                $movie_obj->download_url = $inputs['download_url'];
            }

            if (isset($inputs['subtitle_on_off'])) {
                $movie_obj->subtitle_on_off = $inputs['subtitle_on_off'];
            }
            // dd($movie_obj->id);

            // Remove from recently watched if status is 0 (inactive)
            if (!empty($inputs['id']) && $inputs['status'] == 0) {

                DB::table("recently_watched")
                    ->where("video_type", "=", "Movies")
                    ->where("video_id", "=", $inputs['id'])
                    ->delete();
            }


            if($video_type =='GoogleDrive'){

                $screenshotResult = $this->store_generateScreenshot($fileId);

                if (isset($screenshotResult['success'])) {
                     $movie_obj->video_image = $screenshotResult['path'];
                     $movie_obj->video_image_thumb = $screenshotResult['path'];
                } else {
                     // Screenshot failed, use default images
                     $settings = \App\Settings::findOrFail('1');

                     if (!empty($settings->site_default_movie_poster)) {
                         $poster = $settings->site_default_movie_poster;
                         if (filter_var($poster, FILTER_VALIDATE_URL)) {
                             $poster = parse_url($poster, PHP_URL_PATH);
                         }
                         $poster = ltrim($poster, '/');
                         $movie_obj->video_image = $poster;
                     } else {
                         $movie_obj->video_image = 'NA';
                     }

                     if (!empty($settings->site_default_movie_thumb)) {
                         $thumb = $settings->site_default_movie_thumb;
                         if (filter_var($thumb, FILTER_VALIDATE_URL)) {
                             $thumb = parse_url($thumb, PHP_URL_PATH);
                         }
                         $thumb = ltrim($thumb, '/');
                         $movie_obj->video_image_thumb = $thumb;
                     } else {
                         $movie_obj->video_image_thumb = 'NA';
                     }

                     $errorMsg = $screenshotResult['error'] ?? 'Unknown error';
                     \Log::error("Screenshot generation failed: " . $errorMsg);
                     Session::flash('flash_message', trans('words.added') . ' but screenshot failed. Error: ' . $errorMsg . ' Used default images.');
                }

                $movie_obj->save();

                if (!Session::has('flash_message')) {
                     Session::flash('flash_message', !empty($inputs['id']) ? trans('words.successfully_updated') : trans('words.added'));
                }

                return redirect()->back();
            }
            // Handle error if screenshot generation fails
            $completed= $movie_obj->save();
            if(!$completed){
                return redirect()->back()->with('error', 'Failed to save movie details');
            }
            // Flash success message and redirect back
            Session::flash('flash_message', !empty($inputs['id']) ? trans('words.successfully_updated') : trans('words.added'));
            return redirect()->back();
        } catch (\Exception $e) {
            \Log::error('Error adding movie: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            // dd($e->getMessage()); // Force display error for debugging
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

    }


    public function editMovie($movie_id)
    {
// dd($movie_id);
        if (Auth::User()->usertype != "Admin" and Auth::User()->usertype != "Sub_Admin" and Auth::User()->usertype != "Moderator") {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
        }

        $page_title = trans('words.edit_movie');

        $language_list = Language::orderBy('language_name')->get();
        $genre_list = Genres::orderBy('genre_name')->get();

        $actor_list = ActorDirector::where('ad_type', 'actor')->orderBy('ad_name')->get();
        $director_list = ActorDirector::where('ad_type', 'director')->orderBy('ad_name')->get();

        $movie = Movies::findOrFail($movie_id);

        return view('admin.pages.movies.addedit', compact('page_title', 'movie', 'language_list', 'genre_list', 'actor_list', 'director_list'));
    }

    public function delete($movie_id)
    {
        if (Auth::User()->usertype == "Admin" or Auth::User()->usertype == "Sub_Admin" or Auth::User()->usertype == "Moderator") {

            $recently = RecentlyWatched::where('video_type', 'Movies')->where('video_id', $movie_id)->delete();

            $movie = Movies::findOrFail($movie_id);
            $movie->delete();

            \Session::flash('flash_message', trans('words.deleted'));

            return redirect()->back();
        } else {
            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }
    }
   public function store_generateScreenshot($fileId)
    {
        $google_drive_api  = $this->getRandomApiKey();
        GoogleDriveApi::where('api_key', $google_drive_api)->increment('calls');
        // Build the video URL using the passed fileId
        $videoUrl = "https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&key={$google_drive_api}";

        // Define paths for the screenshot
        $tempImagePath = storage_path('app/public/screenshots/' . $fileId . '.jpg');
        $publicImagePath = public_path('screenshots/' . $fileId . '.jpg');
        $relativePath = 'screenshots/' . $fileId . '.jpg';

        // Ensure the temp screenshots directory exists
        $tempScreenshotsDir = dirname($tempImagePath);
        if (!file_exists($tempScreenshotsDir)) {
            mkdir($tempScreenshotsDir, 0777, true);
        }

        // Ensure the public screenshots directory exists
        $publicScreenshotsDir = public_path('screenshots');
        if (!file_exists($publicScreenshotsDir)) {
            mkdir($publicScreenshotsDir, 0777, true);
        }

        // Check if the screenshot already exists in the temporary location
        if (file_exists($tempImagePath)) {
            unlink($tempImagePath);  // Delete the existing screenshot
        }

        // FFmpeg executable path
        $operatingSystem = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $bundledPath = $operatingSystem ? storage_path('ffmpeg_win/bin/ffmpeg.exe') : storage_path('ffmpeg_linux/ffmpeg');

        // Try base_path if storage_path fails (sometimes storage_path is symlinked or different)
        if (!file_exists($bundledPath)) {
             $bundledPath = $operatingSystem ? base_path('storage/ffmpeg_win/bin/ffmpeg.exe') : base_path('storage/ffmpeg_linux/ffmpeg');
        }

        $ffmpegPath = 'ffmpeg'; // Default to system path
        $usingBundled = false;
        $bundledFound = false;

        if (file_exists($bundledPath)) {
            $ffmpegPath = $bundledPath;
            $usingBundled = true;
            $bundledFound = true;
            if (!$operatingSystem) {
                chmod($ffmpegPath, 0755); // Ensure executable
            }
        }

        // Generate a random timestamp within the first 15 seconds
        $randomTimestamp = rand(1, 15);

        // FFmpeg command to generate the screenshot
        // Added -y to overwrite output files without asking
        // Use escapeshellarg for safety and proper handling of special characters
        $cmd_ffmpeg = $ffmpegPath; // Don't escape yet, we handle quotes manually or rely on proc_open/shell behavior?
        // Actually, it's safer to use escapeshellarg for paths and URLs.
        // But we need to be careful with Windows vs Linux if we rely on PHP's escapeshellarg.
        // Since we are running on Linux (mostly), escapeshellarg uses single quotes.

        $command = escapeshellarg($ffmpegPath) . " -ss " . $randomTimestamp . " -i " . escapeshellarg($videoUrl) . " -t 00:00:15 -vframes 1 " . escapeshellarg($tempImagePath) . " -y";

        // Execute the command using proc_open
        $descriptors = [
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w']   // stderr
        ];

        $bundledError = '';

        $process = proc_open($command, $descriptors, $pipes);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $errorOutput = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $returnVar = proc_close($process);

            // Fallback: If bundled FFmpeg failed, try system FFmpeg
            if ($returnVar !== 0 && $usingBundled) {
                $bundledError = "Bundled FFmpeg failed at $bundledPath with code $returnVar.";
                if ($returnVar == 139) {
                     $bundledError .= " (Segmentation Fault: Binary incompatible with kernel. Try older FFmpeg v5.x or v4.x)";
                }
                $bundledError .= " Error: $errorOutput. ";

                $ffmpegPath = 'ffmpeg'; // Fallback to system path
                $usingBundled = false;  // We are no longer using bundled

                // Re-build command with system ffmpeg
                $command = "ffmpeg -ss " . $randomTimestamp . " -i " . escapeshellarg($videoUrl) . " -t 00:00:15 -vframes 1 " . escapeshellarg($tempImagePath) . " -y";

                // Re-run process
                $process = proc_open($command, $descriptors, $pipes);
                if (is_resource($process)) {
                    $output = stream_get_contents($pipes[1]);
                    $errorOutput = stream_get_contents($pipes[2]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    $returnVar = proc_close($process);
                }
            }

            // Check if the FFmpeg command was successful
            if ($returnVar === 0) {
                // Move the screenshot to the public directory
                if (file_exists($tempImagePath)) {
                    rename($tempImagePath, $publicImagePath);
                } elseif (!file_exists($publicImagePath)) {
                    // It's possible ffmpeg wrote directly to tempImagePath but it wasn't found?
                    // Or maybe it failed silently but exit code 0?
                     return ['error' => 'FFmpeg exited with 0 but output file not found. Output: ' . $output . ' Error: ' . $errorOutput];
                }

                // Save or update the screenshot in the Thumbnail model
                Thumbnail::updateOrCreate(
                    ['file_id' => $fileId],
                    ['video_image_thumb' => $relativePath]
                );

                // Note: We are NO LONGER modifying session object or saving movie here.
                // The caller (addnew) handles saving the movie.

                return ['success' => 'Screenshot generated successfully', 'path' => $relativePath];
            } else {
                $pathMsg = $usingBundled ? "Bundled path: $bundledPath" : "System path: ffmpeg";
                if (!$usingBundled && !$bundledFound) {
                     $pathMsg .= " (Bundled not found at $bundledPath)";
                }
                return ['error' => $bundledError . 'Error generating screenshot. ' . $pathMsg . '. Exit Code: ' . $returnVar . '. Error Output: ' . $errorOutput . '. Stdout: ' . $output];
            }
        } else {
            return ['error' => 'Failed to start FFmpeg process.'];
        }
    }

    public function getRandomApiKey()
    {
        // Get all available Google Drive API keys
        $google_drive_apis = GoogleDriveApi::all();

        if ($google_drive_apis->isEmpty()) {
            session()->flash('error', 'No API keys available.');

        }

        // Retrieve the last used API key (from session or cache)
        $lastUsedApiKey = session()->get('last_used_api_key', null);

        // Filter out the last used API key from the list
        $availableApiKeys = $google_drive_apis->filter(function ($api) use ($lastUsedApiKey) {
            return $api->api_key !== $lastUsedApiKey;
        });

        // If only one key is available, we can't alternate
        if ($availableApiKeys->isEmpty()) {
            session()->flash('error', 'Only one API key available, cannot alternate.');

        }

        // Randomly select a new API key that hasn't been used last
        $newApiKey = $availableApiKeys->random()->api_key;

        // Store the new API key in session to prevent it from being reused next time
        session()->put('last_used_api_key', $newApiKey);

        return $newApiKey;
    }
    private function getVideoThumbnail($video_url)
{
    $thumbnail_url = '';

    // Check if it's a YouTube URL
    if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
        parse_str(parse_url($video_url, PHP_URL_QUERY), $query);
        if (isset($query['v'])) {
            $video_id = $query['v'];
            $thumbnail_url = 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg';
        }
    }
    // Check if it's a Vimeo URL
    elseif (strpos($video_url, 'vimeo.com') !== false) {
        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/', $video_url, $matches)) {
            $video_id = $matches[1];
            $json = @file_get_contents("https://vimeo.com/api/v2/video/$video_id.json");
            if ($json) {
                $vimeo_data = json_decode($json);
                if (is_array($vimeo_data) && isset($vimeo_data[0])) {
                    $thumbnail_url = $vimeo_data[0]->thumbnail_large ?? '';
                }
            }
        }
    }

    // Save the thumbnail to local storage with a unique name
    if ($thumbnail_url) {
        $thumbnail_dir = public_path('video-thumbnails');
        if (!\File::exists($thumbnail_dir)) {
            \File::makeDirectory($thumbnail_dir, 0755, true);
        }

        // Generate a unique filename (using video ID or random string and timestamp)
        $unique_name = $video_id . '_' . time() . '.jpg';
        $thumbnail_path = $thumbnail_dir . '/' . $unique_name;

        // Download the image and store it in the public path
        $image_content = false;

        // Try with curl first
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $thumbnail_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL errors for simplicity
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
            $image_content = curl_exec($ch);
            curl_close($ch);
        }

        // Fallback to file_get_contents if curl failed or is not available
        if (!$image_content) {
            $image_content = @file_get_contents($thumbnail_url);
        }

        if ($image_content) {
            file_put_contents($thumbnail_path, $image_content);

            // Return the saved path (relative URL)
            return 'video-thumbnails/' . $unique_name;
        } else {
             \Log::error("Failed to download thumbnail from URL: $thumbnail_url");
        }
    }

    return null;
}
private function addActorOrDirector($type, $name)
{
    $adSlug = Str::slug($name, '-', null);

    // Check if the actor or director already exists
    $existing = ActorDirector::where('ad_type', $type)
        ->where('ad_slug', $adSlug)
        ->first();

    if ($existing) {
        return $existing->id;
    }

    // Create a new actor or director
    $ad = new ActorDirector;
    $ad->ad_type = $type;
    $ad->ad_name = addslashes($name);
    $ad->ad_slug = $adSlug;
    $ad->save();

    return $ad->id;
}
public function genSubtitle($id) {
    // 1. Get video URL from database
    $movie = Movies::findOrFail($id);
    $youtubeUrl = $movie->video_url;

    // 2. Extract video ID from YouTube URL
    preg_match('/v=([^&]+)/', $youtubeUrl, $matches);
    if (!isset($matches[1])) {
        return response()->json(['error' => 'Invalid YouTube URL']);
    }
    $videoId = $matches[1];

    // 3. Fetch YouTube video page content using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.youtube.com/watch?v=$videoId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");
    $videoPage = curl_exec($ch);
    curl_close($ch);

    // Debug: Save the YouTube page response for checking
    file_put_contents(storage_path('logs/youtube_page.html'), $videoPage);

    // 4. Find subtitle URL from YouTube page
    preg_match('/"captionTracks":(\[.*?\])/', $videoPage, $match);
    if (!isset($match[1])) {
        return response()->json(['error' => 'No subtitles found for this video']);
    }

    // 5. Extract subtitle URL
    preg_match('/"baseUrl":"(.*?)"/', $match[1], $subMatch);
    if (!isset($subMatch[1])) {
        return response()->json(['error' => 'No subtitles available']);
    }
    $subtitleUrl = str_replace('\u0026', '&', $subMatch[1]);

    // 6. Download subtitles from YouTube using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $subtitleUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $subtitles = curl_exec($ch);
    curl_close($ch);

    // 7. Convert YouTube subtitles (VTT) to SRT format
    $srtContent = $this->convertVttToSrt($subtitles);

    // 8. Move subtitles to Laravel's `public/subtitles/` directory
    $srtFileName = "{$id}.srt";
    $publicPath = public_path("subtitles/{$srtFileName}");

    // Ensure the subtitles directory exists
    if (!file_exists(public_path('subtitles'))) {
        mkdir(public_path('subtitles'), 0777, true);
    }

    // Save SRT file in the public directory
    file_put_contents($publicPath, $srtContent);

    // 9. Generate a **fully accessible URL**
    $srtUrl = asset("subtitles/{$srtFileName}"); // Generates full URL like https://yourdomain.com/subtitles/{id}.srt

    // 10. Store full URL in the database
    $movie->subtitle_on_off = 1;
    $movie->subtitle_language1 = 'English';
    $movie->subtitle_url1 = $srtUrl;
    $movie->save();

    return response()->json([
        'message' => 'Subtitles generated',
        'srt_url' => $srtUrl
    ]);
}

private function convertVttToSrt($vttContent) {
    // Remove WEBVTT header
    $vttContent = preg_replace('/WEBVTT\n\n/', '', $vttContent);

    // Convert timestamps to SRT format
    $vttContent = preg_replace('/(\d+):(\d+)\.(\d+) --> (\d+):(\d+)\.(\d+)/', '$1:$2:$3,000 --> $4:$5:$6,000', $vttContent);

    // Add numbering for SRT format
    $lines = explode("\n", trim($vttContent));
    $srtContent = "";
    $index = 1;

    foreach ($lines as $line) {
        if (preg_match('/-->/',$line)) {
            $srtContent .= "$index\n$line\n";
            $index++;
        } else {
            $srtContent .= "$line\n";
        }
    }

    return $srtContent;
}

    public function upload_srt(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            if (strtolower($extension) !== 'srt') {
                 return response()->json(['error' => 'Only .srt files are allowed.']);
            }

            $filename = 'sub_'.time() . '.' . $extension;
            $destinationPath = public_path('upload/subtitles');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $filename);

            return response()->json(['url' => asset('upload/subtitles/' . $filename)]);
        }
        return response()->json(['error' => 'No file uploaded.']);
    }

    public function generate_srt(Request $request)
    {
        $content = $request->input('content');
        if (empty($content)) {
             return response()->json(['error' => 'Content is empty.']);
        }

        $filename = 'sub_gen_'.time() . '.srt';
        $destinationPath = public_path('upload/subtitles');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        file_put_contents($destinationPath . '/' . $filename, $content);

        return response()->json(['url' => asset('upload/subtitles/' . $filename)]);
    }

}
