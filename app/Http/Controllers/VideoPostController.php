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

class VideoPostController extends Controller
{
    /**
     * Show the create video post form.
     */
    public function create()
    {
        $connected = Auth::user()->socialAccounts()->where('provider', 'youtube')->exists();
        return view('posts.create', compact('connected'));
    }

    /**
     * Store a new video post (draft or publish).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:100',
            'content'    => 'nullable|string|max:5000',
            'video_file' => 'required|file|mimes:mp4,avi,mov,wmv,flv,mkv|max:512000', // 500 MB
            'thumbnail'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::user();
        $youtubeAccount = $user->socialAccounts()->where('provider', 'youtube')->first();

        // ----- SAVE AS DRAFT -----
        if ($request->action === 'draft') {
            $videoPath = $request->file('video_file')->store('draft_videos', 'public');
            $thumbnailPath = null;
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('draft_thumbnails', 'public');
            }

            $post = Post::create([
                'user_id'         => $user->id,
                'title'           => $request->title,           // we added title to fillable? let's add it
                'content'         => $request->content,
                'status'          => 'draft',
                'video_path'      => $videoPath,
                'thumbnail_path'  => $thumbnailPath,
                'file_size'       => $request->file('video_file')->getSize(),
            ]);

            return redirect()->route('posts.drafts')->with('success', 'Draft saved.');
        }

        // ----- PUBLISH TO YOUTUBE -----
        if (!$youtubeAccount) {
            return back()->with('error', 'You must connect a YouTube account to publish videos.');
        }

        // Prepare Google Client
        $client = new Google_Client();
        $client->setClientId(env('YOUTUBE_CLIENT_ID'));
        $client->setClientSecret(env('YOUTUBE_CLIENT_SECRET'));
        $client->setAccessToken($youtubeAccount->access_token);
        $client->setScopes(['https://www.googleapis.com/auth/youtube.upload']);

        // If token expired, refresh it (basic refresh – may not work if we don't have refresh_token)
        if ($client->isAccessTokenExpired() && $youtubeAccount->refresh_token) {
            $client->fetchAccessTokenWithRefreshToken($youtubeAccount->refresh_token);
            // Update stored token
            $youtubeAccount->update([
                'access_token'  => $client->getAccessToken()['access_token'] ?? $youtubeAccount->access_token,
                'refresh_token' => $client->getAccessToken()['refresh_token'] ?? $youtubeAccount->refresh_token,
            ]);
        }

        $youtube = new Google_Service_YouTube($client);

        // Create the video resource
        $video = new Google_Service_YouTube_Video();
        $snippet = new Google_Service_YouTube_VideoSnippet();
        $snippet->setTitle($request->title);
        $snippet->setDescription($request->content);
        $video->setSnippet($snippet);

        $status = new Google_Service_YouTube_VideoStatus();
        $status->setPrivacyStatus('public'); // can be 'private' or 'unlisted'
        $video->setStatus($status);

        try {
            // Upload video (chunked, resumable)
            $chunkSizeBytes = 5 * 1024 * 1024; // 5 MB chunks
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

            // Read and upload in chunks
            $status = false;
            $handle = fopen($request->file('video_file')->getPathname(), 'rb');
            while (!$status && !feof($handle)) {
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }
            fclose($handle);
            $client->setDefer(false);

            $youtubeVideoId = $status['id'];

            // Upload thumbnail if provided
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->getPathname();
                $youtube->thumbnails->set($youtubeVideoId, [
                    'data'       => file_get_contents($thumbnailPath),
                    'mimeType'   => $request->file('thumbnail')->getMimeType(),
                    'uploadType' => 'media',
                ]);
            }

            // Save to database
            Post::create([
                'user_id'          => $user->id,
                'title'            => $request->title,
                'content'          => $request->content,
                'status'           => 'publish',
                'youtube_video_id' => $youtubeVideoId,
                'video_path'       => null,  // not needed for published
                'thumbnail_path'   => null,
                'file_size'        => $request->file('video_file')->getSize(),
            ]);

            return redirect()->route('dashboard')->with('success', 'Video published to YouTube!');
        } catch (\Exception $e) {
            \Log::error('YouTube upload failed: ' . $e->getMessage());
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }
}