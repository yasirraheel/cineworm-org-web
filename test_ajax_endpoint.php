<?php
// Test the actual AJAX endpoint that's failing

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;
use App\Models\RssFeed;

echo "Testing AJAX Endpoint for 'Read Full Article'\n";
echo "==============================================\n\n";

// Step 1: Check what RSS feeds are active in database
echo "Step 1: Checking active RSS feeds in database...\n";
echo "-------------------------------------------------\n";
$feeds = RssFeed::where('status', 1)->get();

if ($feeds->count() == 0) {
    die("ERROR: No active RSS feeds found in database!\n");
}

foreach ($feeds as $feed) {
    echo "✓ Active Feed: {$feed->name}\n";
    echo "  URL: {$feed->url}\n";
    echo "  Status: " . ($feed->status ? 'Active' : 'Inactive') . "\n\n";
}

// Step 2: Get a sample article URL from Tribune
echo "Step 2: Getting sample article from Tribune RSS...\n";
echo "-------------------------------------------------\n";

$options = [
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
    ]
];
$context = stream_context_create($options);

$rss_content = @file_get_contents('https://tribune.com.pk/feed/home', false, $context);
if (!$rss_content) {
    die("ERROR: Failed to fetch Tribune RSS feed\n");
}

$rss = simplexml_load_string($rss_content);
$articleUrl = (string)$rss->channel->item[0]->link;
$articleTitle = (string)$rss->channel->item[0]->title;

echo "Article Title: $articleTitle\n";
echo "Article URL: $articleUrl\n\n";

// Step 3: Test domain validation
echo "Step 3: Testing domain validation...\n";
echo "-------------------------------------------------\n";

$parsedUrl = parse_url($articleUrl);
$articleHost = $parsedUrl['host'];
echo "Article host: $articleHost\n\n";

$validDomain = false;
foreach ($feeds as $feed) {
    $feedParsedUrl = parse_url($feed->url);
    if (isset($feedParsedUrl['host'])) {
        $feedHost = str_replace('www.', '', $feedParsedUrl['host']);
        $urlHost = str_replace('www.', '', $articleHost);

        echo "Comparing:\n";
        echo "  Feed host: $feedHost\n";
        echo "  Article host: $urlHost\n";

        if (strpos($urlHost, $feedHost) !== false || strpos($feedHost, $urlHost) !== false) {
            $validDomain = true;
            echo "  ✓ MATCH FOUND!\n\n";
            break;
        } else {
            echo "  ✗ No match\n\n";
        }
    }
}

if (!$validDomain) {
    echo "ERROR: Domain validation failed!\n";
    echo "The article domain doesn't match any active RSS feed domains.\n";
    die();
}

echo "✓ Domain validation passed\n\n";

// Step 4: Test content extraction
echo "Step 4: Testing content extraction...\n";
echo "-------------------------------------------------\n";

$html = @file_get_contents($articleUrl, false, $context);
if (!$html) {
    die("ERROR: Failed to fetch article HTML\n");
}

echo "✓ Fetched HTML (" . strlen($html) . " bytes)\n\n";

$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

$queries = [
    '//div[contains(@class, "story-content")]',
    '//div[contains(@class, "story-detail")]',
    '//div[contains(@class, "rich-text")]',
    '//article[contains(@class, "story")]',
    '//article',
];

$content = '';
echo "Testing selectors:\n";
foreach ($queries as $index => $query) {
    $nodes = $xpath->query($query);
    echo ($index + 1) . ". $query: " . $nodes->length . " nodes\n";

    if ($nodes->length > 0) {
        foreach ($nodes->item(0)->childNodes as $child) {
            $content .= $dom->saveHTML($child);
        }
        $textContent = trim(strip_tags($content));
        if (strlen($textContent) > 100) {
            echo "   ✓ Found content: " . strlen($textContent) . " characters\n";
            echo "   Preview: " . substr($textContent, 0, 150) . "...\n";
            break;
        } else {
            $content = '';
        }
    }
}

if (empty($content)) {
    echo "\n✗ FAILED: No content extracted\n";
    echo "Trying paragraph extraction...\n";

    $paragraphs = $xpath->query('//body//p');
    $paragraphCount = 0;
    foreach ($paragraphs as $node) {
        $text = trim($node->textContent);
        if (strlen($text) > 80) {
            $content .= $dom->saveHTML($node);
            $paragraphCount++;
            if ($paragraphCount >= 5) break;
        }
    }

    if (!empty($content)) {
        echo "✓ Extracted $paragraphCount paragraphs\n";
    } else {
        echo "✗ FAILED: No paragraphs found\n";
    }
} else {
    echo "\n✓ SUCCESS: Content extracted\n";
}

echo "\n==============================================\n";
echo "Test Complete\n";
