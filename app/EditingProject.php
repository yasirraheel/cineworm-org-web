<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * EditingProject Model
 *
 * Represents a user's video editing project in the Cineworm Vintage Film Editor.
 * The timeline_data column stores the complete edit decision list as JSON,
 * including clip order, in/out points, transitions, audio tracks, and colour grading.
 */
class EditingProject extends Model
{
    protected $table = 'editing_projects';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'thumbnail',
        'timeline_data',
        'status',
        'exported_file',
        'total_duration',
    ];

    /**
     * Attribute casts — timeline_data is stored as JSON but accessed as a PHP array.
     */
    protected $casts = [
        'timeline_data' => 'array',
    ];

    /**
     * The user who owns this editing project.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * All video clips uploaded to this project.
     */
    public function clips()
    {
        return $this->hasMany(EditingClip::class, 'project_id');
    }
}
