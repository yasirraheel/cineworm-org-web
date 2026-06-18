<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the editing_clips table for individual video clips within a project.
     * Each clip has FFprobe-extracted metadata and a strip of thumbnail images.
     */
    public function up(): void
    {
        Schema::create('editing_clips', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Foreign key to the parent project (cascade delete cleans up clips automatically)
            $table->unsignedBigInteger('project_id')->index();
            $table->foreign('project_id')
                  ->references('id')
                  ->on('editing_projects')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->index();
            $table->string('original_filename', 255);
            $table->string('file_path', 500);

            // FFprobe-extracted metadata
            $table->float('duration')->default(0);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->float('fps')->nullable();

            // JSON array of thumbnail image paths for the timeline strip
            $table->text('thumbnail_strip')->nullable();

            // File size in bytes
            $table->bigInteger('file_size')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editing_clips');
    }
};
