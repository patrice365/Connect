<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Content
            $table->text('content');
            
            // Status & timestamps
            $table->enum('status', ['draft', 'publish', 'trash', 'archive'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('drafted_at')->nullable();
            $table->timestamp('trashed_at')->nullable();   // when moved to trash
            $table->timestamp('archived_at')->nullable();
            $table->softDeletes(); // Laravel's 'deleted_at' – we use for permanent deletion after 30 days in trash
            
            // Cross‑platform sharing
            $table->json('platform_status')->nullable();   // e.g. {"twitter":"published","facebook":"pending"}
            $table->json('platform_ids')->nullable();      // external post IDs
            
            // Media
            $table->json('media_urls')->nullable();        // array of file paths
            $table->string('media_type')->nullable();      // 'image', 'video', 'gallery', 'embed'
            $table->string('thumbnail_url')->nullable();    // generated thumbnail
            
            // Analytics counters
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('shares_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->unsignedBigInteger('comments_count')->default(0);
            
            // Privacy & interaction
            $table->string('visibility')->default('public'); // public, private, connections
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_sharing')->default(true);
            
            // SEO / meta
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('language', 5)->default('en');
            
            // Threading / reposting
            $table->foreignId('parent_post_id')->nullable()->constrained('posts')->onDelete('cascade');
            
            // Featured / pinned
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_featured')->default(false);
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index('published_at');
            $table->index('trashed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

