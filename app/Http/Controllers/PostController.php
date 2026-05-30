<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;

class PostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Post::class, 'post');
    }

    /**
     * Show the create post form with platform selection.
     */
    public function create()
    {
        $connected = [
            'youtube' => Auth::user()->socialAccounts()->where('provider', 'youtube')->exists(),
            'github'  => Auth::user()->socialAccounts()->where('provider', 'github')->exists(),
        ];

        return view('posts.create', compact('connected'));
    }

    /**
     * Store a new post (draft or publish) for YouTube or GitHub.
     */
    public function store(Request $request)
    {
        $request->validate([
            'platform'   => 'required|in:youtube,github',
            'title'      => 'required|string|max:100',
            'content'    => 'nullable|string|max:5000',
        ]);

        if ($request->platform === 'youtube') {
            return $this->storeYoutube($request);
        } elseif ($request->platform === 'github') {
            return $this->storeGithub($request);
        }

        return back()->with('error', 'Invalid platform.');
    }

    /**
     * Handle YouTube video publishing.
     */
    private function storeYoutube(Request $request)
    {
        $request->validate([
            'video_file' => 'required|file|mimes:mp4,avi,mov,wmv,flv,mkv|max:512000',
            'thumbnail'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();
        $youtubeAccount = $user->socialAccounts()->where('provider', 'youtube')->first();

        if (!$youtubeAccount) {
            return back()->with('error', 'You must connect a YouTube account to publish videos.');
        }

        // ----- Save as draft -----
        if ($request->action === 'draft') {
            $videoPath = $request->file('video_file')->store('draft_videos', 'public');
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('draft_thumbnails', 'public');
            }

            Post::create([
                'user_id'        => $user->id,
                'title'          => $request->title,
                'content'        => $request->content,
                'status'         => 'draft',
                'platform'       => 'youtube',
                'video_path'     => $videoPath,
                'thumbnail_path' => $thumbnailPath,
                'file_size'      => $request->file('video_file')->getSize(),
            ]);

            return redirect()->route('posts.drafts')->with('success', 'Draft saved.');
        }

        // ----- Publish to YouTube -----
        $client = new Google_Client();
        $client->setClientId(env('YOUTUBE_CLIENT_ID'));
        $client->setClientSecret(env('YOUTUBE_CLIENT_SECRET'));
        $client->setAccessToken($youtubeAccount->access_token);
        $client->setScopes(['https://www.googleapis.com/auth/youtube.upload']);

        if ($client->isAccessTokenExpired() && $youtubeAccount->refresh_token) {
            $client->fetchAccessTokenWithRefreshToken($youtubeAccount->refresh_token);
            $youtubeAccount->update([
                'access_token'  => $client->getAccessToken()['access_token'] ?? $youtubeAccount->access_token,
                'refresh_token' => $client->getAccessToken()['refresh_token'] ?? $youtubeAccount->refresh_token,
            ]);
        }

        $youtube = new Google_Service_YouTube($client);
        $video = new Google_Service_YouTube_Video();
        $snippet = new Google_Service_YouTube_VideoSnippet();
        $snippet->setTitle($request->title);
        $snippet->setDescription($request->content);
        $video->setSnippet($snippet);
        $status = new Google_Service_YouTube_VideoStatus();
        $status->setPrivacyStatus('public');
        $video->setStatus($status);

        try {
            $chunkSizeBytes = 5 * 1024 * 1024;
            $client->setDefer(true);
            $insertRequest = $youtube->videos->insert('snippet,status', $video);
            $media = new \Google_Http_MediaFileUpload(
                $client,
                $insertRequest,
                'video/*',
                null,
                true,
                $chunkSizeBytes
            );
            $media->setFileSize(filesize($request->file('video_file')->getPathname()));

            $uploadStatus = false;
            $handle = fopen($request->file('video_file')->getPathname(), 'rb');
            while (!$uploadStatus && !feof($handle)) {
                $chunk = fread($handle, $chunkSizeBytes);
                $uploadStatus = $media->nextChunk($chunk);
            }
            fclose($handle);
            $client->setDefer(false);

            $youtubeVideoId = $uploadStatus['id'];

            if ($request->hasFile('thumbnail')) {
                $thumbPath = $request->file('thumbnail')->getPathname();
                $youtube->thumbnails->set($youtubeVideoId, [
                    'data'       => file_get_contents($thumbPath),
                    'mimeType'   => $request->file('thumbnail')->getMimeType(),
                    'uploadType' => 'media',
                ]);
            }

            Post::create([
                'user_id'          => $user->id,
                'title'            => $request->title,
                'content'          => $request->content,
                'status'           => 'publish',
                'platform'         => 'youtube',
                'youtube_video_id' => $youtubeVideoId,
                'file_size'        => $request->file('video_file')->getSize(),
            ]);

            return redirect()->route('dashboard')->with('success', 'Video published to YouTube!');
        } catch (\Exception $e) {
            \Log::error('YouTube upload failed: ' . $e->getMessage());
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle GitHub repository creation.
     */
    private function storeGithub(Request $request)
    {
        $request->validate([
            'repo_name'       => 'required|string|max:100',
            'repo_visibility' => 'required|in:public,private',
            'init_readme'     => 'nullable|boolean',
            'files.*'         => 'nullable|file|max:10240',
        ]);

        $user = Auth::user();
        $githubAccount = $user->socialAccounts()->where('provider', 'github')->first();

        if (!$githubAccount) {
            return back()->with('error', 'You must connect a GitHub account to publish repositories.');
        }

        try {
            $client = new \Github\Client();
            $client->authenticate($githubAccount->access_token, null, \Github\Client::AUTH_ACCESS_TOKEN);

            $repo = $client->api('repo')->create(
                $request->repo_name,
                [
                    'description' => $request->content,
                    'private'     => $request->repo_visibility === 'private',
                    'auto_init'   => $request->has('init_readme'),
                ]
            );

            // Upload additional files if provided
            if ($request->hasFile('files')) {
                $username = $client->api('current_user')->show()['login'];
                $committer = ['name' => $user->name, 'email' => $user->email];

                foreach ($request->file('files') as $file) {
                    $path = $file->getClientOriginalName();
                    $content = base64_encode(file_get_contents($file->getPathname()));
                    $client->api('repo')->contents()->create(
                        $username,
                        $request->repo_name,
                        $path,
                        $content,
                        'Add ' . $path,
                        null,
                        'main',
                        $committer
                    );
                }
            }

            Post::create([
                'user_id'        => $user->id,
                'title'          => $request->repo_name,
                'content'        => $request->content,
                'status'         => 'publish',
                'platform'       => 'github',
                'github_repo_id' => $repo['id'],
            ]);

            return redirect()->route('dashboard')->with('success', 'Repository created on GitHub!');
        } catch (\Exception $e) {
            \Log::error('GitHub repo creation failed: ' . $e->getMessage());
            return back()->with('error', 'Repository creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Post a comment to a YouTube video using the user's connected YouTube account.
     */
    public function postYouTubeComment(Request $request, $videoId)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $youtubeAccount = $user->socialAccounts()->where('provider', 'youtube')->first();
        if (!$youtubeAccount) {
            return response()->json(['error' => 'YouTube not connected'], 403);
        }

        $client = new Google_Client();
        $client->setClientId(env('YOUTUBE_CLIENT_ID'));
        $client->setClientSecret(env('YOUTUBE_CLIENT_SECRET'));
        $client->setAccessToken($youtubeAccount->access_token);
        $client->setScopes(['https://www.googleapis.com/auth/youtube.force-ssl']);

        if ($client->isAccessTokenExpired() && $youtubeAccount->refresh_token) {
            $client->fetchAccessTokenWithRefreshToken($youtubeAccount->refresh_token);
            $youtubeAccount->update([
                'access_token'  => $client->getAccessToken()['access_token'] ?? $youtubeAccount->access_token,
                'refresh_token' => $client->getAccessToken()['refresh_token'] ?? $youtubeAccount->refresh_token,
            ]);
        }

        $accessToken = $client->getAccessToken()['access_token'] ?? $youtubeAccount->access_token;

        try {
            $resp = Http::withToken($accessToken)->post('https://www.googleapis.com/youtube/v3/commentThreads?part=snippet', [
                'snippet' => [
                    'videoId' => $videoId,
                    'topLevelComment' => [
                        'snippet' => [
                            'textOriginal' => $request->input('comment'),
                        ],
                    ],
                ],
            ]);

            if ($resp->successful()) {
                return response()->json(['success' => true, 'data' => $resp->json()]);
            }

            // If Google returns 401, prompt the frontend to re-connect the account
            if ($resp->status() === 401) {
                return response()->json([
                    'error' => 'unauthorized',
                    'message' => 'YouTube authorization required. Please reconnect your account.',
                    'reconnect_url' => route('social.redirect', ['provider' => 'youtube']),
                ], 401);
            }

            return response()->json(['error' => $resp->body()], $resp->status() ?: 500);
        } catch (\Exception $e) {
            \Log::error('YouTube comment failed: ' . $e->getMessage());
            return response()->json(['error' => 'Comment failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Create a GitHub gist using the connected account.
     * Expects `description`, `public` (bool), and `files` as an associative array: files["name.txt"] = content
     */
    public function createGithubGist(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string|max:255',
            'public' => 'nullable|boolean',
            'files' => 'required|array',
        ]);

        $user = Auth::user();
        $githubAccount = $user->socialAccounts()->where('provider', 'github')->first();
        if (!$githubAccount) {
            return back()->with('error', 'GitHub not connected');
        }

        $payload = [
            'description' => $request->input('description', ''),
            'public' => (bool) $request->input('public', true),
            'files' => [],
        ];

        foreach ($request->input('files', []) as $name => $content) {
            $payload['files'][$name] = ['content' => $content];
        }

        try {
            $resp = Http::withToken($githubAccount->access_token)
                ->post('https://api.github.com/gists', $payload);

            if ($resp->successful()) {
                return redirect()->back()->with('success', 'Gist created!');
            }
            \Log::error('GitHub gist failed: ' . $resp->body());
            return back()->with('error', 'Failed to create gist');
        } catch (\Exception $e) {
            \Log::error('GitHub gist exception: ' . $e->getMessage());
            return back()->with('error', 'Failed to create gist: ' . $e->getMessage());
        }
    }

    /**
     * Push (create/update) a file to a GitHub repo using the connected account.
     * Expects: owner (optional), repo, path, content, message (optional), branch (optional)
     */
    public function pushToGithub(Request $request)
    {
        $request->validate([
            'repo' => 'required|string',
            'path' => 'required|string',
            'content' => 'required|string',
            'owner' => 'nullable|string',
            'message' => 'nullable|string|max:255',
            'branch' => 'nullable|string',
        ]);

        $user = Auth::user();
        $githubAccount = $user->socialAccounts()->where('provider', 'github')->first();
        if (!$githubAccount) {
            return back()->with('error', 'GitHub not connected');
        }

        try {
            // Determine owner (username) if not provided
            $owner = $request->input('owner');
            if (!$owner) {
                $me = Http::withToken($githubAccount->access_token)->get('https://api.github.com/user')->json();
                $owner = $me['login'] ?? null;
            }
            if (!$owner) {
                return back()->with('error', 'Unable to determine GitHub owner');
            }

            $repo = $request->input('repo');
            $path = ltrim($request->input('path'), '/');
            $branch = $request->input('branch');
            $message = $request->input('message', 'Update ' . $path);

            // Check if file exists to get SHA
            $url = "https://api.github.com/repos/{$owner}/{$repo}/contents/{$path}";
            $query = [];
            if ($branch) $query['ref'] = $branch;

            $get = Http::withToken($githubAccount->access_token)->get($url, $query);
            $body = [
                'message' => $message,
                'content' => base64_encode($request->input('content')),
            ];
            if ($branch) $body['branch'] = $branch;
            $committer = ['name' => $user->name, 'email' => $user->email];
            $body['committer'] = $committer;

            if ($get->successful()) {
                $sha = $get->json()['sha'] ?? null;
                if ($sha) $body['sha'] = $sha;
            }

            $resp = Http::withToken($githubAccount->access_token)->put($url, $body);

            if ($resp->successful()) {
                return redirect()->back()->with('success', 'File pushed to GitHub');
            }
            \Log::error('GitHub push failed: ' . $resp->body());
            return back()->with('error', 'Failed to push file to GitHub');
        } catch (\Exception $e) {
            \Log::error('GitHub push exception: ' . $e->getMessage());
            return back()->with('error', 'Failed to push file: ' . $e->getMessage());
        }
    }

    // ========== EXISTING METHODS (drafts, trash, archive, etc.) KEPT BELOW ==========
    // ... your existing methods for drafts(), trash(), archive(), show(), edit(), update(), destroy(), restore(), forceDelete() ...
}