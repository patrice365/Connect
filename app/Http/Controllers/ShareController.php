<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareController extends Controller
{
    /**
     * Share a post to external platforms
     */
    public function share(Request $request, Post $post)
    {
        // Authorize the action (user must own the post)
        $this->authorize('view', $post);

        $request->validate([
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'required|string|in:twitter,facebook,linkedin',
        ]);

        // Check if post is published
        if (!$post->isPublished()) {
            return back()->with('error', 'Only published posts can be shared.');
        }

        // Get user's connected social accounts
        $platforms = $request->input('platforms');
        $userAccounts = Auth::user()->socialAccounts()->whereIn('provider', $platforms)->get();

        if ($userAccounts->isEmpty()) {
            return back()->with('error', 'You have not connected any social accounts.');
        }

        // Share post to each platform
        $shared = [];
        foreach ($userAccounts as $account) {
            try {
                // In production, use SDK for each platform
                $this->shareToProvider($account->provider, $post, $account);
                $shared[] = ucfirst($account->provider);
            } catch (\Exception $e) {
                \Log::error("Failed to share to {$account->provider}: " . $e->getMessage());
            }
        }

        if (empty($shared)) {
            return back()->with('error', 'Failed to share post to any platform.');
        }

        $post->increment('shares_count');

        return back()->with('success', 'Post shared to: ' . implode(', ', $shared));
    }

    /**
     * Share post to a specific provider
     */
    private function shareToProvider($provider, Post $post, $account)
    {
        $url = route('posts.show', $post->id);
        $message = $post->content . "\n\n" . $url;

        switch ($provider) {
            case 'twitter':
                // Implement Twitter API
                break;
            case 'facebook':
                // Implement Facebook API
                break;
            case 'linkedin':
                // Implement LinkedIn API
                break;
        }
    }
}
