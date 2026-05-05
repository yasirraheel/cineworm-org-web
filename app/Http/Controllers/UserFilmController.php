<?php

namespace App\Http\Controllers;

use App\Genres;
use App\Movies;
use App\SubscriptionPlan;
use App\ActorDirector;
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

        $genre_list = Genres::orderBy('genre_name')->get();

        return view('pages.user.films.create', compact('genre_list'));
    }

    // ── Save new film ──────────────────────────────────────────────────────
    public function store(Request $request)
    {
        if ($redirect = $this->ensureFilmUploadAccess()) {
            return $redirect;
        }

        $validator = Validator::make($request->all(), [
            'video_title'       => 'required|max:255',
            'genres'            => 'required|array|min:1',
            'video_url'         => 'required|url',
            'video_description' => 'nullable|string',
            'poster_link'       => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $videoUrl   = trim($request->video_url);
        $video_type = 'URL';
        $video_image = '';

        // Auto-detect YouTube
        if (stripos($videoUrl, 'youtube.com') !== false || stripos($videoUrl, 'youtu.be') !== false) {
            $video_type = 'YouTube';
            parse_str(parse_url($videoUrl, PHP_URL_QUERY), $query);
            if (!empty($query['v'])) {
                $video_image = 'https://img.youtube.com/vi/' . $query['v'] . '/hqdefault.jpg';
            }
        } elseif (stripos($videoUrl, 'vimeo.com') !== false) {
            $video_type = 'Vimeo';
        } elseif (stripos($videoUrl, 'drive.google.com') !== false) {
            $video_type = 'GoogleDrive';
        }

        $movie = new Movies();
        $movie->added_by          = Auth::id();
        $movie->video_title       = addslashes(trim($request->video_title));
        $movie->video_slug        = Str::slug($request->video_title, '-');
        $movie->video_description = addslashes(trim($request->video_description ?? ''));
        $movie->movie_genre_id    = implode(',', $request->genres);
        $movie->movie_lang_id     = 0;
        $movie->video_url         = $videoUrl;
        $movie->video_type        = $video_type;
        $movie->is_owner          = 1;
        $movie->status            = 0; // Pending admin approval
        $movie->funding_url       = $request->funding_url ?? '';
        $movie->webpage_url       = $request->webpage_url ?? '';

        // Poster image
        if ($request->filled('poster_link')) {
            $movie->video_image       = $request->poster_link;
            $movie->video_image_thumb = $request->poster_link;
        } elseif ($video_image) {
            $movie->video_image       = $video_image;
            $movie->video_image_thumb = $video_image;
        }

        $movie->save();

        \Session::flash('flash_message', 'Your film has been submitted for review. It will go live once approved by our team.');

        return redirect('user/films');
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
