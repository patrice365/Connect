<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Soft delete column
            if (!Schema::hasColumn('posts', 'deleted_at')) {
                $table->softDeletes();
            }

            // Status and timestamps
            if (!Schema::hasColumn('posts', 'status')) {
                $table->enum('status', ['draft', 'publish', 'trash', 'archive'])->default('draft');
            }
            if (!Schema::hasColumn('posts', 'published_at')) {
                $table->timestamp('published_at')->nullable();
            }
            if (!Schema::hasColumn('posts', 'drafted_at')) {
                $table->timestamp('drafted_at')->nullable();
            }
            if (!Schema::hasColumn('posts', 'trashed_at')) {
                $table->timestamp('trashed_at')->nullable();
            }
            if (!Schema::hasColumn('posts', 'archived_at')) {
                $table->timestamp('archived_at')->nullable();
            }

            // Cross-platform sharing
            if (!Schema::hasColumn('posts', 'platform_status')) {
                $table->json('platform_status')->nullable();
            }
            if (!Schema::hasColumn('posts', 'platform_ids')) {
                $table->json('platform_ids')->nullable();
            }

            // Media
            if (!Schema::hasColumn('posts', 'media_urls')) {
                $table->json('media_urls')->nullable();
            }
            if (!Schema::hasColumn('posts', 'media_type')) {
                $table->string('media_type')->nullable();
            }
            if (!Schema::hasColumn('posts', 'thumbnail_url')) {
                $table->string('thumbnail_url')->nullable();
            }

            // Counters
            if (!Schema::hasColumn('posts', 'views_count')) {
                $table->unsignedBigInteger('views_count')->default(0);
            }
            if (!Schema::hasColumn('posts', 'shares_count')) {
                $table->unsignedBigInteger('shares_count')->default(0);
            }
            if (!Schema::hasColumn('posts', 'likes_count')) {
                $table->unsignedBigInteger('likes_count')->default(0);
            }
            if (!Schema::hasColumn('posts', 'comments_count')) {
                $table->unsignedBigInteger('comments_count')->default(0);
            }

            // Privacy & interaction
            if (!Schema::hasColumn('posts', 'visibility')) {
                $table->string('visibility')->default('public');
            }
            if (!Schema::hasColumn('posts', 'allow_comments')) {
                $table->boolean('allow_comments')->default(true);
            }
            if (!Schema::hasColumn('posts', 'allow_sharing')) {
                $table->boolean('allow_sharing')->default(true);
            }

            // SEO / meta
            if (!Schema::hasColumn('posts', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable();
            }
            if (!Schema::hasColumn('posts', 'meta_description')) {
                $table->text('meta_description')->nullable();
            }
            if (!Schema::hasColumn('posts', 'language')) {
                $table->string('language', 5)->default('en');
            }

            // Parent post (for replies / quotes)
            if (!Schema::hasColumn('posts', 'parent_post_id')) {
                $table->foreignId('parent_post_id')->nullable()->constrained('posts')->onDelete('cascade');
            }

            // Pinned & featured
            if (!Schema::hasColumn('posts', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false);
            }
            if (!Schema::hasColumn('posts', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }

            // Indexes for performance – with conditional checks to avoid duplicates
            $indexes = Schema::getIndexes('posts');

            $hasUserStatusIndex = collect($indexes)->contains(fn($idx) => $idx['name'] === 'posts_user_id_status_index');
            if (!$hasUserStatusIndex) {
                $table->index(['user_id', 'status']);
            }

            $hasPublishedAtIndex = collect($indexes)->contains(fn($idx) => $idx['name'] === 'posts_published_at_index');
            if (!$hasPublishedAtIndex) {
                $table->index('published_at');
            }

            $hasTrashedAtIndex = collect($indexes)->contains(fn($idx) => $idx['name'] === 'posts_trashed_at_index');
            if (!$hasTrashedAtIndex) {
                $table->index('trashed_at');
            }
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            // Remove all added columns (for rollback)
            $table->dropSoftDeletes();
            $table->dropColumn([
                'status', 'published_at', 'drafted_at', 'trashed_at', 'archived_at',
                'platform_status', 'platform_ids', 'media_urls', 'media_type', 'thumbnail_url',
                'views_count', 'shares_count', 'likes_count', 'comments_count',
                'visibility', 'allow_comments', 'allow_sharing',
                'meta_keywords', 'meta_description', 'language',
                'is_pinned', 'is_featured'
            ]);
            $table->dropForeign(['parent_post_id']);
            $table->dropColumn('parent_post_id');
        });
    }
};