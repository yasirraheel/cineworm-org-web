<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ButtonsBanners extends Model
{
    use HasFactory;

    public const PLACEMENT_DEFAULT = 'default';
    public const PLACEMENT_BELOW_NEWS_GAMES = 'below_news_games';

    public static function buttonPlacements(): array
    {
        return [
            self::PLACEMENT_DEFAULT => 'Default player/sidebar top',
            self::PLACEMENT_BELOW_NEWS_GAMES => 'Under news feed and games',
        ];
    }

    // Table name
    protected $table = 'buttons_banners';

    // Fillable fields for mass assignment
    protected $fillable = [
        'title',
        'image',
        'type',
        'link',
        'color',
        'placement',
    ];

    // Disable timestamps if not needed
    public $timestamps = true;
}
