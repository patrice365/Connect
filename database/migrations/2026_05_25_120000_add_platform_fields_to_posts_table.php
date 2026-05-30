<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing columns only if they don't already exist (safe for existing DBs)
        if (!Schema::hasTable('posts')) {
            return;
        }

        if (!Schema::hasColumn('posts', 'title')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('title')->nullable()->after('user_id');
            });
        }

        if (!Schema::hasColumn('posts', 'video_path')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('video_path')->nullable()->after('media_urls');
            });
        }

        if (!Schema::hasColumn('posts', 'thumbnail_path')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('thumbnail_path')->nullable()->after('video_path');
            });
        }

        if (!Schema::hasColumn('posts', 'youtube_video_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('youtube_video_id')->nullable()->after('thumbnail_path');
            });
        }

        if (!Schema::hasColumn('posts', 'file_size')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedBigInteger('file_size')->nullable()->after('youtube_video_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('posts')) {
            return;
        }

        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'file_size')) {
                $table->dropColumn('file_size');
            }
            if (Schema::hasColumn('posts', 'youtube_video_id')) {
                $table->dropColumn('youtube_video_id');
            }
            if (Schema::hasColumn('posts', 'thumbnail_path')) {
                $table->dropColumn('thumbnail_path');
            }
            if (Schema::hasColumn('posts', 'video_path')) {
                $table->dropColumn('video_path');
            }
            if (Schema::hasColumn('posts', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
