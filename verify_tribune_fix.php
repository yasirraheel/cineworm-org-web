<?php
// Verify Tribune article content extraction works

$rss_url = 'https://tribune.com.pk/feed/home';

$options = [
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36\r\n"
    ]
];
$context = stream_context_create($options);

echo "Testing Tribune article extraction...\n";
echo "=====================================\n\n";

$rss_content = @file_get_contents($rss_url, false, $context);
$rss = simplexml_load_string($rss_content);
$articleUrl = (string)$rss->channel->item[0]->link;
$articleTitle = (string)$rss->channel->item[0]->title;

echo "Article: $articleTitle\n";
echo "URL: $articleUrl\n\n";

$html = @file_get_contents($articleUrl, false, $context);
$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

// Test the new selector
$nodes = $xpath->query('//div[contains(@class, "story-content")]');

echo "Testing selector: //div[contains(@class, \"story-content\")]\n";
echo "Nodes found: " . $nodes->length . "\n\n";

if ($nodes->length > 0) {
    $content = '';
    foreach ($nodes->item(0)->childNodes as $child) {
        $content .= $dom->saveHTML($child);
    }

    $textContent = trim(strip_tags($content));
    $textLength = strlen($textContent);

    echo "✓ SUCCESS!\n";
    echo "Content length: $textLength characters\n\n";
    echo "First 300 characters:\n";
    echo "-------------------------------------\n";
    echo substr($textContent, 0, 300) . "...\n";
    echo "-------------------------------------\n\n";
    echo "✓ Tribune article extraction is now WORKING!\n";
} else {
    echo "✗ FAILED - No content found\n";
}
