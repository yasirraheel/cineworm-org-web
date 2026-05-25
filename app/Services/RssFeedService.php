<?php

namespace App\Services;

use App\Models\RssFeed;

class RssFeedService
{
    public function fetchItems(int $maxItems = 20, int $maxItemsPerFeed = 10): array
    {
        $rssItems = [];
        $rssFeeds = RssFeed::where('status', 1)->get();
        $context = $this->buildContext();

        foreach ($rssFeeds as $feed) {
            $rss = $this->loadFeed($feed->url, $context);

            if (!$rss || !isset($rss->channel->item)) {
                continue;
            }

            $feedCount = 0;

            foreach ($rss->channel->item as $item) {
                if ($feedCount >= $maxItemsPerFeed || count($rssItems) >= $maxItems) {
                    break;
                }

                $pubDate = (string) $item->pubDate;
                $rssItems[] = [
                    'headline' => (string) $item->title,
                    'details' => (string) $item->description,
                    'created_at' => $pubDate,
                    'timestamp' => strtotime($pubDate) ?: 0,
                    'link' => (string) $item->link,
                    'image' => $this->extractImage($item),
                    'feed_name' => $feed->name,
                ];

                $feedCount++;
            }

            if (count($rssItems) >= $maxItems) {
                break;
            }
        }

        usort($rssItems, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return array_slice($rssItems, 0, $maxItems);
    }

    protected function buildContext()
    {
        return stream_context_create([
            'http' => [
                'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36\r\n",
                'timeout' => 12,
            ],
        ]);
    }

    protected function loadFeed(string $url, $context)
    {
        $rssContent = @file_get_contents($url, false, $context);

        if ($rssContent === false || $rssContent === '') {
            return null;
        }

        $rssContent = $this->sanitizeXml($rssContent);

        libxml_use_internal_errors(true);
        $rss = simplexml_load_string($rssContent);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        if ($rss === false) {
            $errorMessage = !empty($errors) ? trim($errors[0]->message) : 'Unknown XML parse error';
            \Log::warning('RSS feed parse skipped: ' . $errorMessage, ['url' => $url]);
            return null;
        }

        return $rss;
    }

    protected function sanitizeXml(string $rssContent): string
    {
        $rssContent = preg_replace('/^\xEF\xBB\xBF/', '', $rssContent);
        return ltrim($rssContent);
    }

    protected function extractImage($item): string
    {
        if (isset($item->enclosure) && isset($item->enclosure['url'])) {
            return (string) $item->enclosure['url'];
        }

        return '';
    }
}
