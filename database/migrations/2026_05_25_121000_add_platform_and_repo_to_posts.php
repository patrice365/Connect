<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'github_repo_id')) {
                $table->string('github_repo_id')->nullable()->after('youtube_video_id');
            }
            if (!Schema::hasColumn('posts', 'platform')) {
                $table->string('platform')->nullable()->after('github_repo_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'platform')) {
                $table->dropColumn('platform');
            }
            if (Schema::hasColumn('posts', 'github_repo_id')) {
                $table->dropColumn('github_repo_id');
            }
        });
    }
};
