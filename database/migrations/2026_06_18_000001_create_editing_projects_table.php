<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the editing_projects table for the Cineworm Vintage Film Editor.
     * Each project belongs to a user and stores the timeline edit decisions as JSON.
     */
    public function up(): void
    {
        Schema::create('editing_projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();

            // Stores the full timeline as JSON (clip order, in/out points, transitions, etc.)
            $table->longText('timeline_data')->nullable();

            // Project lifecycle: draft → exporting → completed / failed
            $table->enum('status', ['draft', 'exporting', 'completed', 'failed'])->default('draft');

            // Path to the final rendered MP4
            $table->string('exported_file')->nullable();

            // Computed total duration of the timeline in seconds
            $table->float('total_duration')->nullable()->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editing_projects');
    }
};
