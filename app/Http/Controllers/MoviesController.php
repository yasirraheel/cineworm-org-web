<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Movies;
use App\Genres;
use App\Language;
use App\HomeSection;
use App\RecentlyWatched;
use App\Slider;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

use Session;

class MoviesController extends Controller
{

    public function movies()
    {
        if (Auth::check()) {
            if (Auth::user()->usertype != "Admin" and Auth::user()->usertype != "Sub_Admin") {
                if (user_device_limit_reached(Auth::user()->id, Auth::user()->plan_id)) {
                    return redirect('dashboard');
                }
            }
        }

        $slider = Slider::where('status', 1)->whereRaw("find_in_set('Movies',slider_display_on)")->orderby('id', 'DESC')->get();

        $pagination_limit = 18;

        if (isset($_GET['lang_id'])) {
            $movie_lang_id = $_GET['lang_id'];

            $movies_list = Movies::where('status', 1)->where('upcoming', 0)->where('movie_lang_id', $movie_lang_id)->orderBy('id', 'DESC')->paginate($pagination_limit);
            $movies_list->appends(\Request::only('lang_id'))->links();
        } else if (isset($_GET['genre_id'])) {
            $movie_genre_id = $_GET['genre_id'];

            $movies_list = Movies::where('status', 1)->where('upcoming', 0)->whereRaw("find_in_set('$movie_genre_id',movie_genre_id)")->orderBy('id', 'DESC')->paginate($pagination_limit);
            $movies_list->appends(\Request::only('genre_id'))->links();
        } else if (isset($_GET['filter'])) {
            $keyword = $_GET['filter'];

            if ($keyword == 'old') {
                $movies_list = Movies::where('status', 1)->where('upcoming', 0)->orderBy('id', 'ASC')->paginate($pagination_limit);
                $movies_list->appends(\Request::only('filter'))->links();
            } else if ($keyword == 'alpha') {
                $movies_list = Movies::where('status', 1)->where('upcoming', 0)->orderBy('video_title', 'ASC')->paginate($pagination_limit);
                $movies_list->appends(\Request::only('filter'))->links();
            } else if ($keyword == 'rand') {
                $movies_list = Movies::where('status', 1)->where('upcoming', 0)->inRandomOrder()->paginate($pagination_limit);
                $movies_list->appends(\Request::only('filter'))->links();
            } else {
                $movies_list = Movies::where('status', 1)->where('upcoming', 0)->orderBy('id', 'DESC')->paginate($pagination_limit);
                $movies_list->appends(\Request::only('filter'))->links();
            }
        } else {
            $movies_list = Movies::where('status', 1)->where('upcoming', 0)->orderBy('id', 'DESC')->paginate($pagination_limit);
        }

        return view('pages.movies.list', compact('slider', 'movies_list'));
    }

    public function movies_details($slug, $id)
    {
        if (Auth::check()) {
            if (Auth::user()->usertype != "Admin" and Auth::user()->usertype != "Sub_Admin") {
                if (user_device_limit_reached(Auth::user()->id, Auth::user()->plan_id)) {
                    return redirect('dashboard');
                }
            }
        }

        $movies_info = Movies::where('status', 1)->where('id', $id)->first();
        //get a random video on each page load

        $random_movie = Movies::where('status', 1)->where('id', '!=', $id)->inRandomOrder()->first();


        //    $random_movie = Movies::where('status',1)->inRandomOrder()->first();


        if ($movies_info == '') {
            abort(404, 'Unauthorized action.');
        }

        $related_movies_list = Movies::where('status', 1)->where('id', '!=', $id)->where('movie_lang_id', $movies_info->movie_lang_id)->orderBy('id', 'DESC')->take(10)->get();

        return view('pages.movies.details', compact('movies_info', 'related_movies_list', 'random_movie'));
    }

    public function movies_watch($slug, $id)
    {
        if (Auth::check()) {
            if (Auth::user()->usertype != "Admin" and Auth::user()->usertype != "Sub_Admin") {
                if (user_device_limit_reached(Auth::user()->id, Auth::user()->plan_id)) {
                    return redirect('dashboard');
                }
            }
        }

        $movies_info = Movies::where('status', 1)->where('id', $id)->first();

        if ($movies_info == '') {
            abort(404, 'Unauthorized action.');
        }

        //Check user plan
        // if ($movies_info->video_access == "Paid") {
        //     if (Auth::check()) {
        //         if (Auth::User()->usertype == "User") {
        //             $user_id = Auth::User()->id;

        //             $user_info = User::findOrFail($user_id);
        //             $user_plan_id = $user_info->plan_id;
        //             $user_plan_exp_date = $user_info->exp_date;

        //             if ($user_plan_id == 0 or strtotime(date('m/d/Y')) > $user_plan_exp_date) {
        //                 return redirect('membership_plan');
        //             }
        //         }
        //     } else {
        //         \Session::flash('error_flash_message', 'Access denied!');

        //         return redirect('login');
        //     }
        // }

        $related_movies_list = Movies::where('status', 1)->where('id', '!=', $id)->where('movie_lang_id', $movies_info->movie_lang_id)->orderBy('id', 'DESC')->take(10)->get();

        //Recently Watched
        if (Auth::check()) {
            $current_user_id = Auth::User()->id;
            $video_id = $movies_info->id;

            $recently_video_count = RecentlyWatched::where('video_type', 'Movies')->where('user_id', $current_user_id)->where('video_id', $video_id)->count();

            if ($recently_video_count <= 0) {
                //Current user recently count
                $current_user_video_count = RecentlyWatched::where('user_id', $current_user_id)->count();

                if ($current_user_video_count == 10) {
                    DB::table("recently_watched")
                        ->where("user_id", "=", $current_user_id)
                        ->orderBy("id", "ASC")
                        ->take(1)
                        ->delete();

                    $video_recent_obj = new RecentlyWatched;
                    $video_recent_obj->video_type = 'Movies';
                    $video_recent_obj->user_id = $current_user_id;
                    $video_recent_obj->video_id = $video_id;
                    $video_recent_obj->save();
                } else {
                    $video_recent_obj = new RecentlyWatched;
                    $video_recent_obj->video_type = 'Movies';
                    $video_recent_obj->user_id = $current_user_id;
                    $video_recent_obj->video_id = $video_id;
                    $video_recent_obj->save();
                }
            }
        }
        $random_movie = Movies::where('status', 1)->where('id', '!=', $id)->inRandomOrder()->first();
        //View Update
        $v_id = $movies_info->id;
        $video_obj = Movies::findOrFail($v_id);
        $video_obj->increment('views');
        $video_obj->save();
        if (Auth::check()) {
            $user_has_liked = Like::where('movie_video_id', $movies_info->id)
                ->where('user_id', Auth::id())
                ->first();
        } else {
            $user_has_liked = null;
        }

        // Fetch Good News Network RSS News
        $rss_news = [];
        try {
            $rss_content = @file_get_contents('https://www.goodnewsnetwork.org/category/news/feed/');
            if ($rss_content) {
                $rss = simplexml_load_string($rss_content);
                if ($rss) {
                    $count = 0;
                    foreach ($rss->channel->item as $item) {
                        if($count >= 20) break;

                        $image = '';
                        if (isset($item->enclosure) && isset($item->enclosure['url'])) {
                            $image = (string)$item->enclosure['url'];
                        }

                        $rss_news[] = [
                            'headline' => (string)$item->title,
                            'details' => (string)$item->description,
                            'created_at' => (string)$item->pubDate,
                            'link' => (string)$item->link,
                            'image' => $image
                        ];
                        $count++;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error("RSS Fetch Error: " . $e->getMessage());
        }

        if ($movies_info->upcoming == 1) {
            return view('pages.movies.upcoming_watch', compact('movies_info', 'related_movies_list', 'rss_news'));
        } else {
            return view('pages.movies.watch', compact('movies_info', 'related_movies_list', 'random_movie', 'user_has_liked', 'rss_news'));
        }
    }
    public function like(Request $request, $video_id)
    {
        $user = Auth::user();

        // Check if the user has already liked the video
        $existingLike = Like::where('user_id', $user->id)
                            ->where('movie_video_id', $video_id)
                            ->first();

        if ($existingLike) {
            // User has already liked the video, so we'll return a message
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'You have already liked this video.'], 400);
            }
            return redirect()->back()->with('error', 'You have already liked this video.');
        }

        // Create a new like
        Like::create([
            'user_id' => $user->id,
            'movie_video_id' => $video_id,
        ]);

        // Increment the likes count on the video
        $movie = Movies::find($video_id);
        if ($movie) {
            $movie->increment('likes');
        }

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'liked' => true,
                'likes' => $movie->likes,
                'message' => 'Video liked successfully'
            ]);
        }

        return redirect()->back()->with('success','Video liked successfully');
    }


    public function unlike(Request $request, $video_id)
    {
        // dd($video_id);
        $user = Auth::user();
        $video = Movies::findOrFail($video_id);

        // Check if user has liked the video
        $like = Like::where('user_id', $user->id)
                    ->where('movie_video_id', $video_id)
                    ->first();
        if (!$like) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'You have not liked this video'], 400);
            }
            return redirect()->back()->with('error', 'You have not liked this video');
        }

        // Delete the like
        $like->delete();

        // Decrement the likes count on the video
        $video->decrement('likes');

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'liked' => false,
                'likes' => $video->likes,
                'message' => 'Video unliked successfully'
            ]);
        }

        return redirect()->back()->with('success','Video unliked successfully');
    }

    public function getNewsContent(Request $request)
    {
        $url = $request->query('url');
        
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Invalid URL'], 400);
        }

        // Basic security check: ensure it is a Good News Network domain
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['host']) || strpos($parsedUrl['host'], 'goodnewsnetwork.org') === false) {
            return response()->json(['error' => 'Invalid domain'], 400);
        }

        try {
            // Set a user agent to avoid being blocked
            $options = [
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
                ]
            ];
            $context = stream_context_create($options);
            $html = @file_get_contents($url, false, $context);

            if (!$html) {
                return response()->json(['error' => 'Failed to fetch content'], 500);
            }

            $dom = new \DOMDocument();
            @$dom->loadHTML($html); // Suppress warnings for malformed HTML
            $xpath = new \DOMXPath($dom);

            // DW articles usually have content in div.rich-text
            $nodes = $xpath->query('//div[contains(@class, "rich-text")]');
            
            $content = '';
            
            if ($nodes->length > 0) {
                // If found, get the inner HTML
                // We need to iterate over child nodes and export them
                foreach ($nodes->item(0)->childNodes as $child) {
                    $content .= $dom->saveHTML($child);
                }
            } else {
                // Fallback: try finding <article> tag
                $nodes = $xpath->query('//article');
                if ($nodes->length > 0) {
                    foreach ($nodes->item(0)->childNodes as $child) {
                        $content .= $dom->saveHTML($child);
                    }
                } else {
                    // Another fallback: try finding header and rich-text separately if structure is different
                    // Sometimes title is separate. For now return not found if rich-text is missing.
                     return response()->json(['error' => 'Content not found'], 404);
                }
            }
            
            return response()->json(['content' => $content]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
