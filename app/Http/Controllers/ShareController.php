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
                // In production, use SDK for each platform (Twitter API, Facebook Graph API, etc.)
                $this->shareToProvider($account->provider, $post, $account);
                $shared[] = ucfirst($account->provider);
            } catch (\Exception $e) {
                // Log error but continue with other platforms
                \Log::error("Failed to share to {$account->provider}: " . $e->getMessage());
            }
        }

        if (empty($shared)) {
            return back()->with('error', 'Failed to share post to any platform.');
        }

        // Increment shares count
        $post->increment('shares_count');

        return back()->with('success', 'Post shared to: ' . implode(', ', $shared));
    }

    /**
     * Share post to a specific provider
     * 
     * @param string $provider
     * @param Post $post
     * @param \App\Models\SocialAccount $account
     */
    private function shareToProvider($provider, Post $post, $account)
    {
        // This is a placeholder implementation
        // In production, use the respective SDKs:
        // - Twitter: twitter/twitter-api-php or GuzzleHttp
        // - Facebook: facebook/php-sdk-v4
        // - LinkedIn: linkedin/linkedin-api-sdk

        $url = route('posts.show', $post->id);
        $message = $post->content . "\n\n" . $url;

        switch ($provider) {
            case 'twitter':
                // Use Twitter API to post tweet
                // $this->postToTwitter($account->access_token, $message);
                break;
            case 'facebook':
                // Use Facebook Graph API to post
                // $this->postToFacebook($account->access_token, $message);
                break;
            case 'linkedin':
                // Use LinkedIn API to post
                // $this->postToLinkedIn($account->access_token, $message);
                break;
        }
    }
}
