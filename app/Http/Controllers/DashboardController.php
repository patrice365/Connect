<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'totalPosts'      => Post::count(),
            'publishedPosts'  => Post::where('status', 'publish')->count(),
            'draftPosts'      => Post::where('status', 'draft')->count(),
            'trashedPosts'    => Post::where('status', 'trash')->count(),
            'totalComments'   => Comment::count(),
            'totalReactions'  => Reaction::count(),
            'totalUsers'      => User::count(),
            'latestPosts'     => Post::with('user')->latest()->take(5)->get(),
        ];

        $connectedPlatforms = $user->socialAccounts()->pluck('provider')->toArray();

        $socialStats = [
            'youtube' => array_merge($this->getYoutubeInfo($user), ['connected' => in_array('youtube', $connectedPlatforms)]),
        ];

        return view('dashboard', compact('stats', 'socialStats'));
    }

    // ==================== YouTube ====================
    private function getYoutubeInfo($user)
    {
        $account = $user->socialAccounts()->where('provider', 'youtube')->first();
        if (!$account) {
            return [
                'channel_name' => null,
                'subscribers'  => 0,
                'views'        => 0,
                'videos'       => 0,
                'recent'       => [],
            ];
        }

        try {
            // 1. Get channel info
            $ch = Http::get('https://www.googleapis.com/youtube/v3/channels', [
                'part'         => 'statistics,snippet',
                'mine'         => 'true',
                'access_token' => $account->access_token,
            ])->json();
            $channel = $ch['items'][0] ?? null;
            if (!$channel) {
                return [
                    'channel_name' => null,
                    'subscribers'  => 0,
                    'views'        => 0,
                    'videos'       => 0,
                    'recent'       => [],
                ];
            }

            // 2. Search recent videos
            $searchResponse = Http::get('https://www.googleapis.com/youtube/v3/search', [
                'part'         => 'snippet',
                'channelId'    => $channel['id'],
                'order'        => 'date',
                'maxResults'   => 5,
                'type'         => 'video',
                'access_token' => $account->access_token,
            ])->json();

            $videoIds = [];
            $recent = $searchResponse['items'] ?? [];

            foreach ($recent as $item) {
                if (!empty($item['id']['videoId'])) {
                    $videoIds[] = $item['id']['videoId'];
                }
            }

            // 3. Fetch statistics for all found videos in one call
            $stats = [];
            if (!empty($videoIds)) {
                $statsResponse = Http::get('https://www.googleapis.com/youtube/v3/videos', [
                    'part'         => 'statistics',
                    'id'           => implode(',', $videoIds),
                    'access_token' => $account->access_token,
                ])->json();

                foreach ($statsResponse['items'] ?? [] as $video) {
                    $vid = $video['id'];
                    $stats[$vid] = [
                        'likeCount'    => $video['statistics']['likeCount'] ?? 0,
                        'commentCount' => $video['statistics']['commentCount'] ?? 0,
                    ];
                }
            }

            // Merge statistics into each recent item
            foreach ($recent as &$item) {
                $vid = $item['id']['videoId'] ?? '';
                $item['statistics'] = $stats[$vid] ?? ['likeCount' => 0, 'commentCount' => 0];
            }

            return [
                'channel_name' => $channel['snippet']['title'] ?? null,
                'subscribers'  => $channel['statistics']['subscriberCount'] ?? 0,
                'views'        => $channel['statistics']['viewCount'] ?? 0,
                'videos'       => $channel['statistics']['videoCount'] ?? 0,
                'recent'       => $recent,
            ];
        } catch (\Exception $e) {
            \Log::error('YouTube API error: ' . $e->getMessage());
            return [
                'channel_name' => null,
                'subscribers'  => 0,
                'views'        => 0,
                'videos'       => 0,
                'recent'       => [],
            ];
        }
    }
}