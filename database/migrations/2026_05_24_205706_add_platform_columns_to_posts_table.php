<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('github_repo_id')->nullable()->after('youtube_video_id');
            $table->string('platform')->nullable()->after('github_repo_id'); // 'youtube' or 'github'
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['github_repo_id', 'platform']);
        });
    }
};