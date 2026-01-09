<?php
// Diagnostic script to check RSS feed issues
// Access at: https://cineworm.org/diagnose.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>RSS Feed Diagnostic</h1>";
echo "<pre>";

// Load Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "==============================================\n";
echo "CHECKING RSS FEEDS SYSTEM\n";
echo "==============================================\n\n";

// Check 1: Database connection
echo "1. CHECKING DATABASE CONNECTION...\n";
echo "-------------------------------------------\n";
try {
    DB::connection()->getPdo();
    echo "✓ Database connected\n\n";
} catch (\Exception $e) {
    die("✗ Database connection failed: " . $e->getMessage() . "\n");
}

// Check 2: Check if rss_feeds table exists
echo "2. CHECKING IF rss_feeds TABLE EXISTS...\n";
echo "-------------------------------------------\n";
try {
    $tableExists = DB::select("SHOW TABLES LIKE 'rss_feeds'");
    if (empty($tableExists)) {
        die("✗ ERROR: rss_feeds table does NOT exist!\n   RUN: php artisan migrate\n");
    }
    echo "✓ rss_feeds table exists\n\n";
} catch (\Exception $e) {
    die("✗ Error checking table: " . $e->getMessage() . "\n");
}

// Check 3: Check active RSS feeds
echo "3. CHECKING ACTIVE RSS FEEDS...\n";
echo "-------------------------------------------\n";
try {
    $feeds = DB::table('rss_feeds')->where('status', 1)->get();

    if ($feeds->count() == 0) {
        die("✗ ERROR: NO ACTIVE RSS FEEDS in database!\n   Go to admin panel and add/activate RSS feeds\n");
    }

    echo "✓ Found " . $feeds->count() . " active feed(s):\n";
    foreach ($feeds as $feed) {
        echo "  - " . $feed->name . ": " . $feed->url . "\n";
    }
    echo "\n";

    // Store first feed for testing
    $testFeed = $feeds[0];

} catch (\Exception $e) {
    die("✗ Error querying feeds: " . $e->getMessage() . "\n");
}

// Check 4: Test fetching RSS feed
echo "4. TESTING RSS FEED FETCH...\n";
echo "-------------------------------------------\n";
$options = [
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
    ]
];
$context = stream_context_create($options);

$rss_content = @file_get_contents($testFeed->url, false, $context);
if (!$rss_content) {
    echo "✗ Failed to fetch RSS from: " . $testFeed->url . "\n\n";
} else {
    echo "✓ RSS fetched successfully (" . strlen($rss_content) . " bytes)\n";

    $rss = @simplexml_load_string($rss_content);
    if ($rss && isset($rss->channel->item[0])) {
        $firstArticle = $rss->channel->item[0];
        $articleUrl = (string)$firstArticle->link;
        $articleTitle = (string)$firstArticle->title;

        echo "✓ RSS parsed successfully\n";
        echo "  First article: " . $articleTitle . "\n";
        echo "  URL: " . $articleUrl . "\n\n";

        // Check 5: Test domain validation
        echo "5. TESTING DOMAIN VALIDATION...\n";
        echo "-------------------------------------------\n";
        $articleParsed = parse_url($articleUrl);
        $feedParsed = parse_url($testFeed->url);

        $articleHost = str_replace('www.', '', $articleParsed['host']);
        $feedHost = str_replace('www.', '', $feedParsed['host']);

        echo "Article host: " . $articleHost . "\n";
        echo "Feed host: " . $feedHost . "\n";

        $matches = (strpos($articleHost, $feedHost) !== false || strpos($feedHost, $articleHost) !== false);

        if ($matches) {
            echo "✓ Domain validation: PASS\n\n";
        } else {
            echo "✗ Domain validation: FAIL\n";
            echo "  This is why 'Read Full Article' is failing!\n\n";
        }

        // Check 6: Test article content extraction
        echo "6. TESTING ARTICLE CONTENT EXTRACTION...\n";
        echo "-------------------------------------------\n";
        $html = @file_get_contents($articleUrl, false, $context);

        if (!$html) {
            echo "✗ Failed to fetch article HTML\n\n";
        } else {
            echo "✓ Article HTML fetched (" . strlen($html) . " bytes)\n";

            $dom = new DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new DOMXPath($dom);

            // Try story-content selector
            $nodes = $xpath->query('//div[contains(@class, "story-content")]');
            if ($nodes->length > 0) {
                echo "✓ Content found with story-content selector\n";
                $content = '';
                foreach ($nodes->item(0)->childNodes as $child) {
                    $content .= $dom->saveHTML($child);
                }
                $textContent = trim(strip_tags($content));
                echo "  Content length: " . strlen($textContent) . " chars\n\n";
            } else {
                echo "✗ No content found with story-content selector\n";
                echo "  Trying paragraph extraction...\n";
                $paragraphs = $xpath->query('//body//p');
                echo "  Found " . $paragraphs->length . " paragraphs\n\n";
            }
        }

        // Check 7: Test the actual AJAX endpoint
        echo "7. TESTING AJAX ENDPOINT...\n";
        echo "-------------------------------------------\n";
        $ajaxUrl = url('ajax/get_news_content') . '?url=' . urlencode($articleUrl);
        echo "Calling: " . $ajaxUrl . "\n";

        $response = @file_get_contents($ajaxUrl, false, $context);
        if ($response === false) {
            echo "✗ AJAX endpoint not reachable\n";
            echo "  Check routes and controller\n\n";
        } else {
            $json = json_decode($response, true);
            if (isset($json['error'])) {
                echo "✗ AJAX returned error: " . $json['error'] . "\n";
                echo "  THIS IS THE PROBLEM!\n\n";
            } else if (isset($json['content'])) {
                echo "✓ AJAX endpoint working!\n";
                echo "  Content returned: " . strlen($json['content']) . " chars\n\n";
            } else {
                echo "✗ Unexpected response: " . substr($response, 0, 200) . "\n\n";
            }
        }

    } else {
        echo "✗ Failed to parse RSS XML\n\n";
    }
}

echo "==============================================\n";
echo "DIAGNOSIS COMPLETE\n";
echo "==============================================\n";
echo "\nDelete this file after checking!\n";

echo "</pre>";
