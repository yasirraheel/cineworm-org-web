<div class="embed-responsive embed-responsive-16by9 ratio ratio-16x9">
    @php
        $embed_html = $tv_info->channel_url;
        $embed_html = preg_replace_callback(
            '/(<iframe\b[^>]*\bsrc=["\'])([^"\']+)(["\'])/i',
            static function ($matches) {
                $embed_url = html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8');
                foreach (['autoplay' => '1', 'mute' => '1', 'playsinline' => '1'] as $key => $value) {
                    if (!preg_match('/[?&]' . preg_quote($key, '/') . '=/i', $embed_url)) {
                        $embed_url .= (strpos($embed_url, '?') === false ? '?' : '&') . $key . '=' . $value;
                    }
                }

                return $matches[1] . e($embed_url) . $matches[3];
            },
            $embed_html,
            1,
        );
    @endphp
    {!! $embed_html !!}
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- Custom Script to Auto-click the Unmute Button -->
<script type="text/javascript">
    $(window).on('load', function() {
        setTimeout(function() {
            $("*:contains('Click to Unmute')").each(function() {
                if ($(this).text().trim() === 'Click to Unmute') {
                    $(this).click();
                }
            });
        }, 5000); // Adjust the delay as necessary to ensure the player has fully loaded
    });
</script>
