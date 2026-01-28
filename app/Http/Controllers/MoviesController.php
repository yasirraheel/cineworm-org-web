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
use App\Models\RssFeed;
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

        // Fetch RSS News from database
        $rss_news = [];
        try {
            // Fetch active RSS feeds from database
            $rss_feeds = RssFeed::where('status', 1)->get();

            // Set context options with User-Agent header (required by some RSS feeds)
            $options = [
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
                ]
            ];
            $context = stream_context_create($options);

            foreach ($rss_feeds as $feed) {
                $rss_content = @file_get_contents($feed->url, false, $context);
                if ($rss_content) {
                    $rss = simplexml_load_string($rss_content);
                    if ($rss) {
                        $count = 0;
                        foreach ($rss->channel->item as $item) {
                            if(count($rss_news) >= 20) break; // Limit total items to 20

                            $image = '';
                            if (isset($item->enclosure) && isset($item->enclosure['url'])) {
                                $image = (string)$item->enclosure['url'];
                            }

                            $rss_news[] = [
                                'headline' => (string)$item->title,
                                'details' => (string)$item->description,
                                'created_at' => (string)$item->pubDate,
                                'link' => (string)$item->link,
                                'image' => $image,
                                'feed_name' => $feed->name
                            ];
                            $count++;
                        }
                    }
                }

                // Break if we've reached the limit
                if(count($rss_news) >= 20) break;
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

            // Check for awards if the movie has an owner
            if ($movie->is_owner && $movie->added_by) {
                $updatedMovie = $movie->fresh(); // Get updated likes count
                $likes = $updatedMovie->likes;

                if ($likes >= 100) {
                    $this->checkAndGiveAward($updatedMovie->added_by, $updatedMovie->id, '100_likes');
                }
                if ($likes >= 1000) {
                    $this->checkAndGiveAward($updatedMovie->added_by, $updatedMovie->id, '1000_likes');
                }
                if ($likes >= 10000) {
                    $this->checkAndGiveAward($updatedMovie->added_by, $updatedMovie->id, '10000_likes');
                }
            }
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
        \Log::info("getNewsContent called with URL: " . $request->query('url'));

        $url = $request->query('url');

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            \Log::error("getNewsContent: Invalid URL provided: " . $url);
            return response()->json(['error' => 'Invalid URL provided'], 400);
        }

        // Basic security check: ensure URL belongs to one of our RSS feed domains
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['host'])) {
            \Log::error("getNewsContent: Could not parse host from URL: " . $url);
            return response()->json(['error' => 'Invalid URL format'], 400);
        }

        // Check if the domain is from one of our active RSS feeds
        $rss_feeds = RssFeed::where('status', 1)->get();
        \Log::info("getNewsContent: Found " . $rss_feeds->count() . " active RSS feeds");

        if ($rss_feeds->count() == 0) {
            \Log::error("getNewsContent: No active RSS feeds found in database");
            return response()->json(['error' => 'No active RSS feeds configured'], 500);
        }

        $validDomain = false;
        foreach ($rss_feeds as $feed) {
            $feedParsedUrl = parse_url($feed->url);
            if (isset($feedParsedUrl['host'])) {
                // Extract base domain (e.g., tribune.com.pk from www.tribune.com.pk)
                $feedHost = str_replace('www.', '', $feedParsedUrl['host']);
                $urlHost = str_replace('www.', '', $parsedUrl['host']);

                \Log::info("getNewsContent: Comparing - Feed: $feedHost, Article: $urlHost");

                if (strpos($urlHost, $feedHost) !== false || strpos($feedHost, $urlHost) !== false) {
                    $validDomain = true;
                    \Log::info("getNewsContent: Domain validation passed for feed: " . $feed->name);
                    break;
                }
            }
        }

        if (!$validDomain) {
            \Log::error("getNewsContent: Domain validation failed for: " . $parsedUrl['host']);
            return response()->json(['error' => 'Article domain does not match any active RSS feed'], 403);
        }

        try {
            // Use cURL with comprehensive headers for better compatibility
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Accept-Encoding: gzip, deflate, br',
                    'Connection: keep-alive',
                    'Upgrade-Insecure-Requests: 1',
                    'Sec-Fetch-Dest: document',
                    'Sec-Fetch-Mode: navigate',
                    'Sec-Fetch-Site: none',
                    'Cache-Control: max-age=0'
                ],
                CURLOPT_ENCODING => '', // Handle all encodings
            ]);

            $html = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($html === false || $httpCode >= 400) {
                \Log::error("Failed to fetch content from URL: $url (HTTP: $httpCode, cURL Error: $curlError)");
                return response()->json(['error' => 'Content not available or blocked by source'], 403);
            }

            if (empty($html)) {
                \Log::error("Empty content from URL: " . $url);
                return response()->json(['error' => 'No content received'], 500);
            }

            $dom = new \DOMDocument();
            @$dom->loadHTML($html); // Suppress warnings for malformed HTML
            $xpath = new \DOMXPath($dom);

            $content = '';

            // Strategy 1: Try comprehensive list of content selectors for different news sites
            $queries = [
                // Tribune Pakistan
                '//div[contains(@class, "story-content")]',
                '//div[contains(@class, "story-detail")]',

                // Good News Network
                '//div[contains(@class, "rich-text")]',

                // BBC, CNN, Reuters
                '//article[contains(@class, "story")]',
                '//article[contains(@class, "article")]',
                '//div[contains(@class, "story-body")]',
                '//div[contains(@class, "article-body")]',
                '//div[contains(@class, "article__body")]',

                // WordPress sites
                '//div[contains(@class, "entry-content")]',
                '//div[contains(@class, "post-content")]',
                '//div[contains(@class, "content-area")]',

                // Generic article tags
                '//article',
                '//main[contains(@class, "content")]',
                '//div[contains(@class, "article-content")]',
                '//div[contains(@class, "main-content")]',

                // Dawn, Geo News, other Pakistani sites
                '//div[contains(@class, "detail")]',
                '//div[contains(@class, "story")]',

                // Last resort: main tag with paragraphs
                '//main',
            ];

            foreach ($queries as $query) {
                $nodes = $xpath->query($query);
                if ($nodes->length > 0) {
                    foreach ($nodes->item(0)->childNodes as $child) {
                        $content .= $dom->saveHTML($child);
                    }
                    $textContent = trim(strip_tags($content));
                    // Only accept if we got meaningful content (at least 100 chars)
                    if (strlen($textContent) > 100) {
                        break;
                    } else {
                        $content = ''; // Reset and try next selector
                    }
                }
            }

            // Strategy 2: If still no content, extract all meaningful paragraphs from body
            if (trim(strip_tags($content)) === '') {
                $paragraphs = $xpath->query('//body//p');
                $paragraphCount = 0;
                $foundContent = false;

                foreach ($paragraphs as $node) {
                    $text = trim($node->textContent);
                    // Only include paragraphs with substantial content
                    if (strlen($text) > 80) {
                        $content .= $dom->saveHTML($node);
                        $paragraphCount++;
                        $foundContent = true;
                        if ($paragraphCount >= 15) break; // Get more paragraphs for better content
                    }
                }
            }

            if (trim(strip_tags($content)) === '') {
                \Log::error("No content found for URL: " . $url);
                return response()->json(['error' => 'Content not found'], 404);
            }

            return response()->json(['content' => $content]);

        } catch (\Exception $e) {
            \Log::error("Exception in getNewsContent: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function checkAndGiveAward($user_id, $movie_id, $award_type)
    {
        // Check if award already exists
        $exists = Award::where('user_id', $user_id)
            ->where('movie_id', $movie_id)
            ->where('award_type', $award_type)
            ->exists();

        if (!$exists) {
            Award::create([
                'user_id' => $user_id,
                'movie_id' => $movie_id,
                'award_type' => $award_type
            ]);
            // Log or notify if needed
            \Log::info("Award given: User $user_id, Movie $movie_id, Type $award_type");
        }
    }
}
