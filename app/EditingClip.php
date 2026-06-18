<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * EditingClip Model
 *
 * Represents a single video clip uploaded into an editing project.
 * Stores FFprobe-extracted metadata (duration, resolution, fps) and
 * a JSON array of thumbnail image paths used for the timeline strip.
 */
class EditingClip extends Model
{
    protected $table = 'editing_clips';

    protected $fillable = [
        'project_id',
        'user_id',
        'original_filename',
        'file_path',
        'duration',
        'width',
        'height',
        'fps',
        'thumbnail_strip',
        'file_size',
    ];

    /**
     * Attribute casts — thumbnail_strip is stored as JSON but accessed as a PHP array.
     */
    protected $casts = [
        'thumbnail_strip' => 'array',
    ];

    /**
     * The editing project this clip belongs to.
     */
    public function project()
    {
        return $this->belongsTo(EditingProject::class, 'project_id');
    }

    /**
     * The user who uploaded this clip.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Returns an array of full URLs for each thumbnail in the strip.
     * Thumbnail paths are stored relative to the public directory,
     * e.g. "user_editor/5/12/thumbnails/thumb_001.jpg"
     *
     * @return array
     */
    public function getThumbnailUrls(): array
    {
        $strip = $this->thumbnail_strip;

        if (empty($strip) || !is_array($strip)) {
            return [];
        }

        return array_map(function ($path) {
            return url($path);
        }, $strip);
    }
}
