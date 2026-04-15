<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Comment::class, 'comment');
    }

    public function store(Request $request, Post $post)
    {
        $request->validate(['content' => 'required|string|max:1000']);

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'parent_id' => $request->input('parent_id'),
        ]);

        // Increment comment counter on post
        $post->increment('comments_count');

        return back()->with('success', 'Comment added.');
    }

    public function update(Request $request, Comment $comment)
    {
        $request->validate(['content' => 'required|string|max:1000']);
        $comment->update([
            'content' => $request->input('content'),
            'edited_at' => now()
        ]);
        return back()->with('success', 'Comment updated.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        $comment->post->decrement('comments_count');
        return back()->with('success', 'Comment deleted.');
    }
}