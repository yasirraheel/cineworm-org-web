<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\ImportImdbController;
use App\Genres;
use App\Language;
use App\Movies;
use App\ActorDirector;
use App\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserFilmController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── Check the user has film_uploads in their plan ──────────────────────
    protected function ensureFilmUploadAccess()
    {
        $user = Auth::user();

        if (in_array($user->usertype, ['Admin', 'Sub_Admin', 'Moderator'], true)) {
            return redirect('admin/dashboard');
        }

        $plan = !empty($user->plan_id) ? SubscriptionPlan::find($user->plan_id) : null;
        $features = $plan ? $plan->getEffectiveFeatureKeys() : [];

        if (
            !in_array('film_uploads', $features, true) &&
            !in_array('extended_media_uploads', $features, true)
        ) {
            \Session::flash('error_flash_message', 'Your subscription plan does not include Film Uploads. Please upgrade your plan.');
            return redirect('dashboard');
        }

        return null;
    }

    // ── My Films list ──────────────────────────────────────────────────────
    public function index()
    {
        if ($redirect = $this->ensureFilmUploadAccess()) {
            return $redirect;
        }

        $films = Movies::where('added_by', Auth::id())
            ->orderBy('id', 'DESC')
            ->paginate(12);

        // There is no value in showing an empty library first. Send new
        // creators directly to the upload form; the library remains useful
        // once they have submitted at least one film.
        if ($films->total() === 0) {
            return redirect('user/films/upload');
        }

        $page_title = 'My Films';

        return view('pages.user.films.index', compact('films', 'page_title'));
    }

    // ── Upload form ────────────────────────────────────────────────────────
    public function create()
    {
        if ($redirect = $this->ensureFilmUploadAccess()) {
            return $redirect;
        }

        $genre_list    = Genres::orderBy('genre_name')->get();
        $language_list = Language::orderBy('language_name')->get();
        $actor_list    = ActorDirector::where('ad_type', 'actor')->orderBy('ad_name')->get();
        $director_list = ActorDirector::where('ad_type', 'director')->orderBy('ad_name')->get();

        $page_title = 'Upload Film';

        return view('pages.user.films.create', compact('genre_list', 'language_list', 'actor_list', 'director_list', 'page_title'));
    }

    public function findImdbMovie(Request $request)
    {
        if ($redirect = $this->ensureFilmUploadAccess()) {
            return $redirect;
        }

        return app(ImportImdbController::class)->find_imdb_movie();
    }

    // ── Save new film ──────────────────────────────────────────────────────
    public function store(Request $request)
    {
        if ($redirect = $this->ensureFilmUploadAccess()) {
            return $redirect;
        }

        $validator = Validator::make($request->all(), [
            'video_title' => 'required|string|max:255',
            'video_description' => 'required|string|max:10000',
            'movie_language' => 'required|integer',
            'genres'      => 'required|array|min:1',
            'video_type' => 'required|in:URL,GoogleDrive,HLS',
            'video_url' => 'required|string|max:2000',
            'poster_link' => 'nullable|url|max:2000',
            'poster_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'creator_price' => 'nullable|numeric|min:0.01',
            'creator_trailer_url' => 'nullable|url|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        // ── Resolve actors ────────────────────────────────────────────────
        $actorIds    = [];
        $directorIds = [];

        if (!empty($request->actors)) {
            foreach (explode(',', $request->actors) as $name) {
                $id = $this->resolveActorDirector('actor', trim($name));
                if ($id) $actorIds[] = $id;
            }
        }
        if (!empty($request->director)) {
            foreach (explode(',', $request->director) as $name) {
                $id = $this->resolveActorDirector('director', trim($name));
                if ($id) $directorIds[] = $id;
            }
        }

        // ── Resolve video URL by type ─────────────────────────────────────
        $video_type = $request->video_type;
        if ($video_type === 'Local')  $video_url = $request->video_url_local;
        elseif ($video_type === 'URL')   $video_url = $request->video_url;
        elseif ($video_type === 'HLS')   $video_url = $request->input('video_url_hls') ?: $request->input('video_url');
        elseif ($video_type === 'DASH')  $video_url = $request->video_url_dash;
        elseif ($video_type === 'Embed') $video_url = $request->video_embed_code;
        else                             $video_url = $request->video_url;

        $video_url   = trim((string) $video_url);
        $video_image = '';
        $fileId      = null;

        // Auto-detect YouTube / Vimeo
        if (stripos($video_url, 'youtube.com') !== false || stripos($video_url, 'youtu.be') !== false) {
            $video_type = 'YouTube';
            parse_str(parse_url($video_url, PHP_URL_QUERY), $q);
            if (!empty($q['v'])) {
                $video_image = 'https://img.youtube.com/vi/' . $q['v'] . '/hqdefault.jpg';
            }
        } elseif (stripos($video_url, 'vimeo.com') !== false) {
            $video_type = 'Vimeo';
        } elseif (stripos($video_url, 'drive.google.com') !== false || stripos($video_url, 'googleapis.com') !== false) {
            $video_type = 'GoogleDrive';
            $fileId = $this->extractGoogleDriveFileId($video_url);
        }

        $isOwner = (int) $request->input('is_owner', 0) === 1;
        $creatorSaleEnabled = $request->boolean('creator_sale_enabled');
        if ($creatorSaleEnabled && !$isOwner) {
            return redirect()->back()
                ->withErrors(['creator_sale_enabled' => 'Only the film owner can sell a film.'])
                ->withInput();
        }

        if ($creatorSaleEnabled && (float) $request->input('creator_price', 0) <= 0) {
            return redirect()->back()
                ->withErrors(['creator_price' => 'Enter a valid price for a paid film.'])
                ->withInput();
        }

        if ($creatorSaleEnabled && $video_type !== 'GoogleDrive') {
            return redirect()->back()
                ->withErrors(['video_url' => 'Paid creator films must use a Google Drive video source.'])
                ->withInput();
        }

        if ($creatorSaleEnabled && !$fileId) {
            return redirect()->back()
                ->withErrors(['video_url' => 'A valid Google Drive file link is required for a paid film.'])
                ->withInput();
        }

        $movie = new Movies();
        $movie->added_by          = Auth::id();
        $movie->video_title       = addslashes(trim($request->video_title));
        $movie->video_slug        = Str::slug($request->video_title, '-');
        $movie->video_description = addslashes(trim($request->video_description ?? ''));
        $movie->movie_genre_id    = implode(',', $request->genres);
        $movie->movie_lang_id     = $request->movie_language ?? 0;
        $movie->video_url         = $video_url;
        $movie->video_type        = $video_type;
        $movie->video_quality     = $request->video_quality ?? 0;
        $movie->is_owner          = $isOwner ? 1 : 0;
        $movie->creator_sale_enabled = $creatorSaleEnabled ? 1 : 0;
        $movie->creator_price     = $creatorSaleEnabled ? (float) $request->input('creator_price') : 0;
        $movie->creator_currency  = strtoupper((string) ($request->input('creator_currency') ?: (getcong('currency_code') ?: 'USD')));
        $movie->creator_trailer_url = $request->input('creator_trailer_url');
        $movie->creator_sale_status = $creatorSaleEnabled ? 'pending' : 'not_for_sale';
        $movie->creator_approved_at = null;
        $movie->upcoming          = $request->upcoming ?? 0;
        $movie->funding_url       = $request->funding_url ?? '';
        $movie->webpage_url       = $request->webpage_url ?? '';
        $movie->imdb_id           = $request->imdb_id ?? '';
        $movie->imdb_rating       = $request->imdb_rating ?? '';
        $movie->imdb_votes        = $request->imdb_votes ?? '';
        $movie->content_rating    = $request->content_rating ?? '';
        $movie->duration          = $request->duration ?? '';
        $movie->release_date      = $request->filled('release_date') ? strtotime($request->release_date) : null;
        $movie->file_id           = $fileId;
        $movie->status            = 0; // Pending admin approval

        // Multi-quality URLs
        $movie->video_url_480  = $request->video_url_480  ?? '';
        $movie->video_url_720  = $request->video_url_720  ?? '';
        $movie->video_url_1080 = $request->video_url_1080 ?? '';

        // Actors / Directors
        $movie->actor_id    = !empty($actorIds)    ? implode(',', $actorIds)    : null;
        $movie->director_id = !empty($directorIds) ? implode(',', $directorIds) : null;

        // Poster: a user-uploaded file takes priority over a remote URL.
        $uploadedPoster = null;
        if ($request->hasFile('poster_file') && $request->file('poster_file')->isValid()) {
            $posterDirectory = public_path('upload/images/creator-posters');
            if (!is_dir($posterDirectory)) {
                mkdir($posterDirectory, 0755, true);
            }

            $posterFile = $request->file('poster_file');
            $posterName = 'creator_poster_' . Str::uuid() . '.' . strtolower($posterFile->getClientOriginalExtension());
            $posterFile->move($posterDirectory, $posterName);
            $uploadedPoster = 'upload/images/creator-posters/' . $posterName;
        }

        if ($uploadedPoster) {
            $movie->video_image       = $uploadedPoster;
            $movie->video_image_thumb = $uploadedPoster;
        } elseif ($request->filled('poster_link')) {
            $movie->video_image       = $request->poster_link;
            $movie->video_image_thumb = $request->poster_link;
        } elseif ($video_image) {
            $movie->video_image       = $video_image;
            $movie->video_image_thumb = $video_image;
        }

        // Subtitles
        $movie->subtitle_on_off    = $request->subtitle_on_off    ?? 0;
        $movie->subtitle_language1 = $request->subtitle_language1 ?? '';
        $movie->subtitle_url1      = $request->subtitle_url1      ?? '';
        $movie->subtitle_language2 = $request->subtitle_language2 ?? '';
        $movie->subtitle_url2      = $request->subtitle_url2      ?? '';
        $movie->subtitle_language3 = $request->subtitle_language3 ?? '';
        $movie->subtitle_url3      = $request->subtitle_url3      ?? '';

        if (!empty($movie->subtitle_url1) || !empty($movie->subtitle_url2) || !empty($movie->subtitle_url3)) {
            $movie->subtitle_on_off = 1;
        }

        $movie->save();

        \Session::flash('flash_message', 'Your film has been submitted for review. It will go live once approved by our team.');
        return redirect('user/films');
    }

    private function extractGoogleDriveFileId(string $url): ?string
    {
        $patterns = [
            '/drive\.google\.com\/file\/d\/([^\/]+)/i',
            '/drive\.google\.com\/open\?id=([^&]+)/i',
            '/drive\.google\.com\/uc\?(?:[^#]*&)?id=([^&]+)/i',
            '/googleapis\.com\/drive\/v3\/files\/([^?]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches) && !empty($matches[1])) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    // ── Helper: find or create actor/director ─────────────────────────────
    private function resolveActorDirector(string $type, string $name): ?int
    {
        if (!$name) return null;
        $slug = Str::slug($name, '-');
        $existing = ActorDirector::where('ad_type', $type)->where('ad_slug', $slug)->first();
        if ($existing) return $existing->id;
        $ad = new ActorDirector();
        $ad->ad_type = $type;
        $ad->ad_name = addslashes($name);
        $ad->ad_slug = $slug;
        $ad->save();
        return $ad->id;
    }

    // ── Delete own film ────────────────────────────────────────────────────
    public function destroy($id)
    {
        if ($redirect = $this->ensureFilmUploadAccess()) {
            return $redirect;
        }

        $movie = Movies::where('id', $id)->where('added_by', Auth::id())->firstOrFail();
        $movie->delete();

        \Session::flash('flash_message', 'Film removed successfully.');

        return redirect('user/films');
    }
}
