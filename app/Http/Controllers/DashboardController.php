<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
            'youtube' => $this->getYoutubeInfo($user, in_array('youtube', $connectedPlatforms)),
            'github'  => $this->getGithubInfo($user, in_array('github', $connectedPlatforms)),
        ];

        return view('dashboard', compact('stats', 'socialStats'));
    }

    // ==================== YouTube ====================
    private function getYoutubeInfo($user, $isConnected)
    {
        $default = [
            'connected'         => $isConnected,
            'channel_name'      => null,
            'channel_thumbnail' => null,
            'subscribers'       => 0,
            'views'             => 0,
            'videos'            => 0,
            'recent'            => [],
        ];

        if (!$isConnected) {
            return $default;
        }

        $account = $user->socialAccounts()->where('provider', 'youtube')->first();
        if (!$account || !$account->access_token) {
            Log::warning('YouTube account found but no access token', ['user_id' => $user->id]);
            return $default;
        }

        try {
            // Fetch channel data
            $channelResponse = Http::withToken($account->access_token)
                ->get('https://www.googleapis.com/youtube/v3/channels', [
                    'part' => 'snippet,statistics',
                    'mine' => 'true',
                ]);

            if (!$channelResponse->successful()) {
                Log::error('YouTube channel API failed', [
                    'status' => $channelResponse->status(),
                    'body' => $channelResponse->body(),
                ]);
                return $default;
            }

            $channelData = $channelResponse->json();
            if (empty($channelData['items'])) {
                Log::error('YouTube channel API returned no items', ['response' => $channelData]);
                return $default;
            }

            $channel = $channelData['items'][0];
            $channelId = $channel['id'];

            // Fetch recent videos
            $searchResponse = Http::withToken($account->access_token)
                ->get('https://www.googleapis.com/youtube/v3/search', [
                    'part'       => 'snippet',
                    'channelId'  => $channelId,
                    'maxResults' => 6,
                    'order'      => 'date',
                    'type'       => 'video',
                ]);

            $recentVideos = [];
            if ($searchResponse->successful() && !empty($searchResponse->json()['items'])) {
                $videoIds = [];
                foreach ($searchResponse->json()['items'] as $item) {
                    if (isset($item['id']['videoId'])) {
                        $videoIds[] = $item['id']['videoId'];
                        $recentVideos[$item['id']['videoId']] = $item;
                    }
                }

                // Fetch statistics for those videos
                if (!empty($videoIds)) {
                    $statsResponse = Http::withToken($account->access_token)
                        ->get('https://www.googleapis.com/youtube/v3/videos', [
                            'part' => 'statistics',
                            'id'   => implode(',', $videoIds),
                        ]);

                    if ($statsResponse->successful()) {
                        foreach ($statsResponse->json()['items'] ?? [] as $videoStat) {
                            $vid = $videoStat['id'];
                            if (isset($recentVideos[$vid])) {
                                $recentVideos[$vid]['statistics'] = $videoStat['statistics'];
                            }
                        }
                    }
                }

                // Re-index to plain array
                $recentVideos = array_values($recentVideos);
            }

            return [
                'connected'         => true,
                'channel_name'      => $channel['snippet']['title'] ?? null,
                'channel_thumbnail' => $channel['snippet']['thumbnails']['default']['url'] ?? null,
                'subscribers'       => (int)($channel['statistics']['subscriberCount'] ?? 0),
                'views'             => (int)($channel['statistics']['viewCount'] ?? 0),
                'videos'            => (int)($channel['statistics']['videoCount'] ?? 0),
                'recent'            => $recentVideos,
            ];
        } catch (\Exception $e) {
            Log::error('YouTube API exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return $default;
        }
    }

    // ==================== GitHub ====================
    private function getGithubInfo($user, $isConnected)
    {
        $default = [
            'connected' => $isConnected,
            'username'  => null,
            'repos'     => 0,
            'followers' => 0,
            'avatar'    => null,
            'bio'       => null,
            'repo_list' => [],
        ];

        if (!$isConnected) {
            return $default;
        }

        $account = $user->socialAccounts()->where('provider', 'github')->first();
        if (!$account || !$account->access_token) {
            Log::warning('GitHub account found but no access token', ['user_id' => $user->id]);
            return $default;
        }

        try {
            // User profile
            $userResponse = Http::withToken($account->access_token)
                ->get('https://api.github.com/user');

            if (!$userResponse->successful()) {
                Log::error('GitHub user API failed', ['status' => $userResponse->status()]);
                return $default;
            }

            $userData = $userResponse->json();

            // Repositories
            $reposResponse = Http::withToken($account->access_token)
                ->get('https://api.github.com/user/repos', [
                    'sort'      => 'updated',
                    'direction' => 'desc',
                    'per_page'  => 6,
                ]);

            $repoList = [];
            if ($reposResponse->successful()) {
                foreach ($reposResponse->json() as $repo) {
                    $repoList[] = [
                        'name'        => $repo['name'],
                        'description' => $repo['description'],
                        'language'    => $repo['language'],
                        'stars'       => $repo['stargazers_count'],
                        'url'         => $repo['html_url'],
                    ];
                }
            }

            return [
                'connected' => true,
                'username'  => $userData['login'] ?? null,
                'repos'     => $userData['public_repos'] ?? 0,
                'followers' => $userData['followers'] ?? 0,
                'avatar'    => $userData['avatar_url'] ?? null,
                'bio'       => $userData['bio'] ?? null,
                'repo_list' => $repoList,
            ];
        } catch (\Exception $e) {
            Log::error('GitHub API exception: ' . $e->getMessage());
            return $default;
        }
    }
}