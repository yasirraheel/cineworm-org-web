<?php

namespace App\Http\Controllers;

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

        return view('pages.user.films.index', compact('films'));
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

        return view('pages.user.films.create', compact('genre_list', 'language_list', 'actor_list', 'director_list'));
    }

    // ── Save new film ──────────────────────────────────────────────────────
    public function store(Request $request)
    {
        if ($redirect = $this->ensureFilmUploadAccess()) {
            return $redirect;
        }

        $validator = Validator::make($request->all(), [
            'video_title' => 'required|max:255',
            'genres'      => 'required|array|min:1',
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
        elseif ($video_type === 'HLS')   $video_url = $request->video_url_hls;
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
        } elseif (stripos($video_url, 'drive.google.com') !== false) {
            $video_type = 'GoogleDrive';
            preg_match('/\/d\/(.*?)\//', $video_url, $m);
            if (!empty($m[1])) $fileId = $m[1];
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
        $movie->is_owner          = $request->is_owner ?? 0;
        $movie->upcoming          = $request->upcoming ?? 0;
        $movie->funding_url       = $request->funding_url ?? '';
        $movie->webpage_url       = $request->webpage_url ?? '';
        $movie->imdb_rating       = $request->imdb_rating ?? '';
        $movie->content_rating    = $request->content_rating ?? '';
        $movie->file_id           = $fileId;
        $movie->status            = 0; // Pending admin approval

        // Multi-quality URLs
        $movie->video_url_480  = $request->video_url_480  ?? '';
        $movie->video_url_720  = $request->video_url_720  ?? '';
        $movie->video_url_1080 = $request->video_url_1080 ?? '';

        // Actors / Directors
        $movie->actor_id    = !empty($actorIds)    ? implode(',', $actorIds)    : null;
        $movie->director_id = !empty($directorIds) ? implode(',', $directorIds) : null;

        // Poster
        if ($request->filled('poster_link')) {
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
