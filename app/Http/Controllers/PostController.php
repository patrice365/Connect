<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        // The authorizeResource method is provided by the AuthorizesRequests trait
        // which is included in the base Controller class.
        $this->authorizeResource(Post::class, 'post');
    }

    // Display list of published posts
    public function index()
    {
        $posts = Post::latest()->get();
        return view('feed', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $data = [
            'content' => $request->input('content'),
            'status' => 'draft',
            'drafted_at' => now(),
        ];

        if ($request->has('publish')) {
            $data['status'] = 'publish';
            $data['published_at'] = now();
        }

        $post = Auth::user()->posts()->create($data);

        if ($post->status === 'publish') {
            return redirect()->route('posts.index')->with('success', 'Post published!');
        } else {
            return redirect()->route('posts.drafts')->with('success', 'Draft saved.');
        }
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $updateData = ['content' => $request->input('content')];

        if ($post->isDraft() && $request->has('publish')) {
            $updateData['status'] = 'publish';
            $updateData['published_at'] = now();
        }

        $post->update($updateData);

        $redirectRoute = $post->isTrashed() ? 'posts.trash' : 'posts.index';
        return redirect()->route($redirectRoute)->with('success', 'Post updated.');
    }

    // Move to trash (if published) or permanent delete (if draft)
    public function destroy(Post $post)
    {
        if ($post->isDraft()) {
            $post->forceDelete();
            return redirect()->route('posts.drafts')->with('success', 'Draft permanently deleted.');
        }

        if ($post->isPublished()) {
            $post->moveToTrash();
            return redirect()->route('posts.index')->with('warning', 'Post moved to trash. It will be deleted after 30 days.');
        }

        return back()->with('error', 'Invalid action for this post status.');
    }

    // Restore a trashed post back to published
    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $this->authorize('restore', $post);

        if ($post->isTrashed()) {
            $post->restoreFromTrash();
            return redirect()->route('posts.trash')->with('success', 'Post restored and published.');
        }

        return back()->with('error', 'Post cannot be restored.');
    }

    // Permanently delete from trash
    public function forceDelete($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $post);

        if ($post->isTrashed()) {
            $post->forceDeleteFromTrash();
            return redirect()->route('posts.trash')->with('success', 'Post permanently deleted.');
        }

        return back()->with('error', 'Only trashed posts can be permanently deleted.');
    }
}