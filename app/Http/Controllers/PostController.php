<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    // ========== EXISTING METHODS (drafts, trash, archive, etc.) KEPT BELOW ==========
    // ... your existing methods for drafts(), trash(), archive(), show(), edit(), update(), destroy(), restore(), forceDelete() ...
}