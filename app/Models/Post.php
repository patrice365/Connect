<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'content',
        'status',
        'published_at',
        'drafted_at',
        'trashed_at',
        'archived_at',
        'platform_status',
        'platform_ids',
        'media_urls',
        'media_type',
        'thumbnail_url',
        'views_count',
        'shares_count',
        'likes_count',
        'comments_count',
        'visibility',
        'allow_comments',
        'allow_sharing',
        'meta_keywords',
        'meta_description',
        'language',
        'parent_post_id',
        'is_pinned',
        'is_featured',
    ];

    protected $casts = [
        'platform_status' => 'array',
        'platform_ids' => 'array',
        'media_urls' => 'array',
        'published_at' => 'datetime',
        'drafted_at' => 'datetime',
        'trashed_at' => 'datetime',
        'archived_at' => 'datetime',
        'deleted_at' => 'datetime',
        'allow_comments' => 'boolean',
        'allow_sharing' => 'boolean',
        'is_pinned' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // ========== RELATIONSHIPS ==========
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function parent()
    {
        return $this->belongsTo(Post::class, 'parent_post_id');
    }

    public function children()
    {
        return $this->hasMany(Post::class, 'parent_post_id');
    }

    // ========== SCOPES ==========
    public function scopePublished($query)
    {
        return $query->where('status', 'publish')->whereNotNull('published_at');
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeTrashed($query)
    {
        return $query->where('status', 'trash');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archive');
    }

    public function scopeVisible($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // ========== STATUS HELPER METHODS ==========
    public function moveToTrash(): void
    {
        $this->update([
            'status' => 'trash',
            'trashed_at' => Carbon::now(),
        ]);
    }

    public function restoreFromTrash(): void
    {
        $this->update([
            'status' => 'publish',
            'trashed_at' => null,
            'published_at' => Carbon::now(),
        ]);
    }

    public function forceDeleteFromTrash(): void
    {
        $this->forceDelete();
    }

    public function archive(): void
    {
        $this->update([
            'status' => 'archive',
            'archived_at' => Carbon::now(),
        ]);
    }

    public function restoreFromArchive(): void
    {
        $this->update([
            'status' => 'publish',
            'archived_at' => null,
            'published_at' => Carbon::now(),
        ]);
    }

    public function publish(): void
    {
        $this->update([
            'status' => 'publish',
            'published_at' => Carbon::now(),
        ]);
    }

    // ========== STATUS CHECK METHODS ==========
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPublished(): bool
    {
        return $this->status === 'publish';
    }

    public function isTrashed(): bool
    {
        return $this->status === 'trash';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archive';
    }
}