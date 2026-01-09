<?php
// Simple test without Laravel to check the actual issue

echo "Testing 'Read Full Article' Functionality\n";
echo "==========================================\n\n";

// Simulate what happens when user clicks "Read full article"
$articleUrl = 'https://tribune.com.pk/story/2586232/sohail-afridi-heads-to-sindh-for-meetings-rally-at-mazar-e-quaid';

echo "Article URL: $articleUrl\n\n";

// Test 1: Check if we can fetch the page
echo "Test 1: Fetching article page...\n";
echo "---------------------------------\n";

$options = [
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n",
        'timeout' => 10
    ]
];
$context = stream_context_create($options);
$html = @file_get_contents($articleUrl, false, $context);

if (!$html) {
    die("✗ FAILED: Could not fetch article page\n");
}

echo "✓ SUCCESS: Fetched " . strlen($html) . " bytes\n\n";

// Test 2: Parse HTML and find content
echo "Test 2: Extracting content...\n";
echo "---------------------------------\n";

$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

// All the selectors from the controller
$queries = [
    '//div[contains(@class, "story-content")]',
    '//div[contains(@class, "story-detail")]',
    '//div[contains(@class, "rich-text")]',
    '//article[contains(@class, "story")]',
    '//article[contains(@class, "article")]',
    '//div[contains(@class, "story-body")]',
    '//div[contains(@class, "article-body")]',
    '//div[contains(@class, "article__body")]',
    '//div[contains(@class, "entry-content")]',
    '//div[contains(@class, "post-content")]',
    '//div[contains(@class, "content-area")]',
    '//article',
    '//main[contains(@class, "content")]',
    '//div[contains(@class, "article-content")]',
    '//div[contains(@class, "main-content")]',
    '//div[contains(@class, "detail")]',
    '//div[contains(@class, "story")]',
    '//main',
];

$content = '';
$successfulQuery = '';

foreach ($queries as $query) {
    $nodes = $xpath->query($query);
    if ($nodes->length > 0) {
        foreach ($nodes->item(0)->childNodes as $child) {
            $content .= $dom->saveHTML($child);
        }
        $textContent = trim(strip_tags($content));

        // Only accept if we got meaningful content (at least 100 chars)
        if (strlen($textContent) > 100) {
            $successfulQuery = $query;
            break;
        } else {
            $content = ''; // Reset and try next selector
        }
    }
}

if (!empty($content)) {
    $textContent = trim(strip_tags($content));
    echo "✓ SUCCESS: Found content using selector:\n";
    echo "  $successfulQuery\n\n";
    echo "Content length: " . strlen($textContent) . " characters\n\n";
    echo "First 200 characters:\n";
    echo str_repeat('-', 50) . "\n";
    echo substr($textContent, 0, 200) . "...\n";
    echo str_repeat('-', 50) . "\n\n";
} else {
    echo "✗ FAILED with primary selectors\n\n";
    echo "Trying fallback: Extracting paragraphs...\n";

    $paragraphs = $xpath->query('//body//p');
    $paragraphCount = 0;
    $content = '';

    foreach ($paragraphs as $node) {
        $text = trim($node->textContent);
        if (strlen($text) > 80) {
            $content .= $dom->saveHTML($node);
            $paragraphCount++;
            if ($paragraphCount >= 15) break;
        }
    }

    if (!empty($content)) {
        $textContent = trim(strip_tags($content));
        echo "✓ SUCCESS: Extracted $paragraphCount paragraphs\n";
        echo "Content length: " . strlen($textContent) . " characters\n\n";
        echo "First 200 characters:\n";
        echo str_repeat('-', 50) . "\n";
        echo substr($textContent, 0, 200) . "...\n";
        echo str_repeat('-', 50) . "\n";
    } else {
        echo "✗ FAILED: No content could be extracted\n";
    }
}

echo "\n==========================================\n";
echo "Now testing on your LIVE server...\n";
echo "==========================================\n\n";

// Test the actual AJAX endpoint
$ajaxUrl = 'https://cineworm.org/movies/get-news-content?url=' . urlencode($articleUrl);

echo "Calling: $ajaxUrl\n\n";

$response = @file_get_contents($ajaxUrl, false, $context);

if ($response === false) {
    echo "✗ FAILED: Could not reach AJAX endpoint\n";
    echo "This might be a server configuration issue.\n";
} else {
    $json = json_decode($response, true);

    if (isset($json['error'])) {
        echo "✗ ERROR from server: " . $json['error'] . "\n\n";
        echo "This is the actual error your users are seeing!\n";

        if ($json['error'] == 'Invalid domain') {
            echo "\nROOT CAUSE: Domain validation is failing!\n";
            echo "The RSS feed domain in database doesn't match article domain.\n";
        }
    } else if (isset($json['content'])) {
        $contentText = trim(strip_tags($json['content']));
        echo "✓ SUCCESS: Server returned content\n";
        echo "Content length: " . strlen($contentText) . " characters\n";
    } else {
        echo "✗ UNEXPECTED RESPONSE:\n";
        echo $response . "\n";
    }
}

echo "\n==========================================\n";
echo "Test Complete\n";
