<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can view any comments.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a specific comment.
     */
    public function view(User $user, Comment $comment): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create a comment.
     * Only verified users can comment.
     */
    public function create(User $user): bool
    {
        return $user->email_verified_at !== null;
    }

    /**
     * Determine whether the user can update a comment.
     * Only the OWNER OF THE POST (not comment author) can update any comment on their post.
     */
    public function update(User $user, Comment $comment): Response
    {
        return $user->id === $comment->post->user_id
            ? Response::allow()
            : Response::deny('Only the post owner can edit comments on this post.');
    }

    /**
     * Determine whether the user can delete a comment.
     * Only the OWNER OF THE POST can delete any comment on their post.
     */
    public function delete(User $user, Comment $comment): Response
    {
        return $user->id === $comment->post->user_id
            ? Response::allow()
            : Response::deny('Only the post owner can delete comments on this post.');
    }

    /**
     * Determine whether the user can restore a soft-deleted comment.
     * Only the post owner.
     */
    public function restore(User $user, Comment $comment): Response
    {
        return $user->id === $comment->post->user_id
            ? Response::allow()
            : Response::deny('Only the post owner can restore comments.');
    }

    /**
     * Determine whether the user can permanently delete a comment.
     * Only the post owner.
     */
    public function forceDelete(User $user, Comment $comment): Response
    {
        return $user->id === $comment->post->user_id
            ? Response::allow()
            : Response::deny('Only the post owner can permanently delete comments.');
    }
}