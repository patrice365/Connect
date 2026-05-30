<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('video_path')->nullable()->after('content');      // local file path (for drafts)
            $table->string('thumbnail_path')->nullable()->after('video_path');
            $table->string('youtube_video_id')->nullable()->after('thumbnail_path');
            $table->unsignedBigInteger('file_size')->nullable()->after('youtube_video_id');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['video_path', 'thumbnail_path', 'youtube_video_id', 'file_size']);
        });
    }
};