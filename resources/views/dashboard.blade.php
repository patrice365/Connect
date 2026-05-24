@extends('layouts.app')

@section('content')
<div class="content-body" style="display:block; padding:25px;">
    <section class="feed" style="max-width:100%;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
            <h1 class="page-title-main" style="margin-bottom:0;">Dashboard</h1>
            <div style="display:flex; gap:10px; align-items:center;">
                <label for="date-filter" style="color:#94a3b8; font-size:14px;">Date Filter</label>
                <select id="date-filter" class="date-select" style="width:auto; padding:8px 15px; background:#1a1a1a; border:1px solid #2d2d2d; border-radius:8px; color:white;">
                    <option>24 hours</option>
                    <option>7 days</option>
                    <option>30 days</option>
                </select>
            </div>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div style="background: rgba(16,185,129,0.2); border:1px solid #34d399; color:#34d399; padding:12px; border-radius:10px; margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div style="background: rgba(239,68,68,0.2); border:1px solid #ef4444; color:#fca5a5; padding:12px; border-radius:10px; margin-bottom:20px;">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div style="background: rgba(239,68,68,0.2); border:1px solid #ef4444; color:#fca5a5; padding:12px; border-radius:10px; margin-bottom:20px;">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Social Media Overview -->
        <h2 style="margin-bottom: 20px; font-size: 22px; font-weight: 700; color: #cbd5e1;">Social Media Overview</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <!-- YouTube Card (extra‑large buttons) -->
            <div class="card" id="card-youtube" style="border-left: 4px solid #FF0000;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <i class="fab fa-youtube" style="color: #FF0000; font-size: 24px;"></i>
                    <h3 style="color: white; font-size: 16px; font-weight: 600;">YouTube</h3>
                </div>
                @if($socialStats['youtube']['connected'])
                    @if($socialStats['youtube']['channel_name'])
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                            @if($socialStats['youtube']['channel_thumbnail'])
                                <img src="{{ $socialStats['youtube']['channel_thumbnail'] }}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                            @endif
                            <span style="color: #cbd5e1; font-size: 16px;">{{ $socialStats['youtube']['channel_name'] }}</span>
                        </div>
                    @endif
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; color: #cbd5e1; margin-bottom: 20px;">
                        <div>
                            <p style="color: #64748b;">Subscribers</p>
                            <p style="font-size: 18px; font-weight: 700;">{{ number_format($socialStats['youtube']['subscribers']) }}</p>
                        </div>
                        <div>
                            <p style="color: #64748b;">Views</p>
                            <p style="font-size: 18px; font-weight: 700;">{{ number_format($socialStats['youtube']['views']) }}</p>
                        </div>
                        <div>
                            <p style="color: #64748b;">Videos</p>
                            <p style="font-size: 18px; font-weight: 700;">{{ number_format($socialStats['youtube']['videos']) }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('social.disconnect', 'youtube') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-new"
                                style="background:#2d2d2d; color:#f87171; padding: 24px 40px; font-size: 18px; border-radius: 12px; margin-top: 10px; display: inline-block;">
                            Disconnect YouTube
                        </button>
                    </form>
                @else
                    <p style="color: #94a3b8; margin-bottom: 10px;">Not connected</p>
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">
                        Connect to see your channel stats and recent videos.
                    </p>
                    <a href="{{ route('social.redirect', 'youtube') }}" class="btn-new"
                       style="background:#FF0000; padding: 24px 40px; font-size: 18px; border-radius: 12px; display: inline-block;">
                        Connect YouTube
                    </a>
                @endif
            </div>

            <!-- GitHub Card (extra‑large buttons) -->
            <div class="card" id="card-github" style="border-left: 4px solid #6e5494;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <i class="fab fa-github" style="color: #6e5494; font-size: 24px;"></i>
                    <h3 style="color: white; font-size: 16px; font-weight: 600;">GitHub</h3>
                </div>
                @if($socialStats['github']['connected'])
                    @if($socialStats['github']['username'])
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                            @if($socialStats['github']['avatar'])
                                <img src="{{ $socialStats['github']['avatar'] }}" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                            @endif
                            <span style="color: #cbd5e1; font-size: 16px;">{{ $socialStats['github']['username'] }}</span>
                        </div>
                    @endif
                    <div style="margin-bottom: 20px;">
                        <p style="color: #94a3b8;">Repos: <strong style="color: white;">{{ number_format($socialStats['github']['repos']) }}</strong></p>
                        <p style="color: #94a3b8;">Followers: <strong style="color: white;">{{ number_format($socialStats['github']['followers']) }}</strong></p>
                        @if($socialStats['github']['bio'])
                            <p style="color: #64748b; font-size: 13px;">{{ $socialStats['github']['bio'] }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('social.disconnect', 'github') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-new"
                                style="background:#2d2d2d; color:#f87171; padding: 24px 40px; font-size: 18px; border-radius: 12px; margin-top: 10px; display: inline-block;">
                            Disconnect GitHub
                        </button>
                    </form>
                @else
                    <p style="color: #94a3b8; margin-bottom: 10px;">Not connected</p>
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">
                        Connect to see your GitHub profile and stats.
                    </p>
                    <a href="{{ route('social.redirect', 'github') }}" class="btn-new"
                       style="background:#6e5494; padding: 24px 40px; font-size: 18px; border-radius: 12px; display: inline-block;">
                        Connect GitHub
                    </a>
                @endif
            </div>
        </div>

        <!-- Recent Posts (YouTube videos) -->
        <h2 style="margin-bottom: 20px; font-size: 22px; font-weight: 700; color: #cbd5e1;">Recent Posts</h2>
        @if($socialStats['youtube']['connected'] && !empty($socialStats['youtube']['recent']))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach($socialStats['youtube']['recent'] as $video)
                    <div class="card" style="padding:15px; background:#121212; border:1px solid #1f1f1f; border-radius:12px;">
                        @php
                            $thumb = $video['snippet']['thumbnails']['medium']['url'] ?? $video['snippet']['thumbnails']['default']['url'] ?? '';
                            $videoId = $video['id']['videoId'] ?? '';
                            $likes = $video['statistics']['likeCount'] ?? 0;
                            $comments = $video['statistics']['commentCount'] ?? 0;
                        @endphp
                        @if($thumb)
                            <a href="https://www.youtube.com/watch?v={{ $videoId }}" target="_blank">
                                <img src="{{ $thumb }}" style="width:100%; border-radius:8px; margin-bottom:10px;">
                            </a>
                        @endif
                        <p style="color:#cbd5e1; font-size:14px; line-height:1.4; margin-bottom:8px;">
                            {{ \Illuminate\Support\Str::limit($video['snippet']['title'] ?? 'Untitled', 60) }}
                        </p>
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:11px; color:#64748b; margin-bottom:5px;">
                            <span>{{ \Carbon\Carbon::parse($video['snippet']['publishedAt'])->diffForHumans() }}</span>
                            <a href="https://www.youtube.com/watch?v={{ $videoId }}" target="_blank" style="color:#FF0000; text-decoration:none;">Watch →</a>
                        </div>
                        <div style="display:flex; gap:20px; font-size:13px; color:#94a3b8; border-top:1px solid #2d2d2d; padding-top:8px;">
                            <span><i class="far fa-heart" style="color:#f59e0b;"></i> {{ number_format($likes) }}</span>
                            <span class="comment-trigger" data-video-id="{{ $videoId }}" style="cursor:pointer;">
                                <i class="far fa-comment" style="color:#0ea5e9;"></i> {{ number_format($comments) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="background:#121212; padding:30px; border-radius:20px; border:1px solid #1f1f1f; text-align:center; color:#64748b;">
                <i class="fas fa-chart-line" style="font-size:40px; margin-bottom:15px;"></i>
                <p>No recent videos found. Connect YouTube and make sure you have uploaded videos.</p>
            </div>
        @endif
    </section>
</div>

<!-- Comment Modal -->
<div id="commentModal" class="modal" style="display:none; position:fixed; z-index:10000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.8);">
    <div class="modal-content" style="background:#121212; margin:5% auto; padding:20px; border:1px solid #2d2d2d; border-radius:15px; width:90%; max-width:600px; max-height:80vh; overflow-y:auto; color:white;">
        <span id="closeModal" style="float:right; font-size:24px; cursor:pointer;">&times;</span>
        <h3 style="margin-bottom:15px;">Comments</h3>
        <div id="commentList" style="margin-top:10px;">Loading...</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('commentModal');
    const closeBtn = document.getElementById('closeModal');
    const commentList = document.getElementById('commentList');

    // Open modal when clicking comment count
    document.querySelectorAll('.comment-trigger').forEach(el => {
        el.addEventListener('click', function() {
            const videoId = this.dataset.videoId;
            commentList.innerHTML = 'Loading comments...';
            modal.style.display = 'block';

            fetch(`/youtube/comments/${videoId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        commentList.innerHTML = `<p style="color:#f87171;">${data.error}</p>`;
                        return;
                    }
                    let html = '';
                    data.comments.forEach(comment => {
                        html += `
                            <div style="border-bottom:1px solid #2d2d2d; padding:8px 0;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:5px;">
                                    <img src="${comment.authorProfileImageUrl}" style="width:32px; height:32px; border-radius:50%;">
                                    <strong style="color:#cbd5e1;">${comment.authorDisplayName}</strong>
                                    <span style="color:#64748b; font-size:12px;">${comment.publishedAt}</span>
                                </div>
                                <p style="color:#94a3b8; margin:0;">${comment.textDisplay}</p>
                            </div>
                        `;
                    });
                    commentList.innerHTML = html || '<p>No comments yet.</p>';
                })
                .catch(err => {
                    commentList.innerHTML = '<p style="color:#f87171;">Failed to load comments.</p>';
                });
        });
    });

    closeBtn.onclick = () => modal.style.display = 'none';
    window.onclick = (e) => { if (e.target == modal) modal.style.display = 'none'; };
});
</script>
@endpush