<?php
// Test fetching news content from Tribune

// Get a Tribune article URL from RSS feed
$rss_url = 'https://tribune.com.pk/feed/home';

echo "Step 1: Fetching RSS feed from Tribune...\n";
echo "========================================\n";

$options = [
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
    ]
];
$context = stream_context_create($options);
$rss_content = @file_get_contents($rss_url, false, $context);

if (!$rss_content) {
    die("Failed to fetch RSS feed\n");
}

$rss = simplexml_load_string($rss_content);
if (!$rss) {
    die("Failed to parse RSS feed\n");
}

// Get first article URL
$firstItem = $rss->channel->item[0];
$articleUrl = (string)$firstItem->link;
$articleTitle = (string)$firstItem->title;

echo "Found article: $articleTitle\n";
echo "URL: $articleUrl\n\n";

echo "Step 2: Fetching article content...\n";
echo "========================================\n";

$html = @file_get_contents($articleUrl, false, $context);

if (!$html) {
    die("Failed to fetch article HTML\n");
}

echo "✓ Successfully fetched HTML (" . strlen($html) . " bytes)\n\n";

echo "Step 3: Testing different selectors...\n";
echo "========================================\n";

$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

$queries = [
    '//div[contains(@class, "story-detail")]//div[contains(@class, "detail")]',
    '//div[contains(@class, "story-detail")]',
    '//div[contains(@class, "content-area")]',
    '//div[contains(@class, "rich-text")]',
    '//article[contains(@class, "story")]',
    '//article',
    '//div[contains(@class, "entry-content")]',
    '//div[contains(@class, "post-content")]',
    '//div[contains(@class, "article-content")]',
    '//main//p'
];

foreach ($queries as $index => $query) {
    $nodes = $xpath->query($query);
    $nodeCount = $nodes->length;
    echo ($index + 1) . ". Query: $query\n";
    echo "   Found: $nodeCount nodes\n";

    if ($nodeCount > 0) {
        $content = '';
        foreach ($nodes->item(0)->childNodes as $child) {
            $content .= $dom->saveHTML($child);
        }
        $textContent = trim(strip_tags($content));
        $textLength = strlen($textContent);
        echo "   Content length: $textLength chars\n";
        if ($textLength > 0) {
            echo "   Preview: " . substr($textContent, 0, 100) . "...\n";
            echo "   ✓ THIS SELECTOR WORKS!\n";
            break;
        }
    }
    echo "\n";
}

echo "\n========================================\n";
echo "Step 4: Checking HTML structure...\n";
echo "========================================\n";

// Find all divs with class attributes
$divs = $xpath->query('//div[@class]');
echo "Found " . $divs->length . " divs with classes\n\n";
echo "Sample classes found:\n";
$classCount = 0;
foreach ($divs as $div) {
    $class = $div->getAttribute('class');
    if (strpos($class, 'story') !== false || strpos($class, 'content') !== false || strpos($class, 'article') !== false) {
        echo "  - $class\n";
        $classCount++;
        if ($classCount >= 10) break;
    }
}

// Find all articles
$articles = $xpath->query('//article');
echo "\nFound " . $articles->length . " <article> tags\n";
if ($articles->length > 0) {
    foreach ($articles as $article) {
        $class = $article->getAttribute('class');
        echo "  Article class: $class\n";
    }
}

echo "\n========================================\n";
echo "DONE\n";
