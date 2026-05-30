<?php $__env->startSection('content'); ?>
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

        
        <?php if(session('success')): ?>
            <div style="background: rgba(16,185,129,0.2); border:1px solid #34d399; color:#34d399; padding:12px; border-radius:10px; margin-bottom:20px;">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div style="background: rgba(239,68,68,0.2); border:1px solid #ef4444; color:#fca5a5; padding:12px; border-radius:10px; margin-bottom:20px;">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <h2 style="margin-bottom: 20px; font-size: 22px; font-weight: 700; color: #cbd5e1;">Social Media Overview</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            
            <div class="card" style="border-left: 4px solid #FF0000;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <i class="fab fa-youtube" style="color: #FF0000; font-size: 24px;"></i>
                    <h3 style="color: white; font-size: 16px; font-weight: 600;">YouTube</h3>
                </div>
                <?php if($socialStats['youtube']['connected'] ?? false): ?>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <?php if($socialStats['youtube']['channel_thumbnail'] ?? null): ?>
                            <img src="<?php echo e($socialStats['youtube']['channel_thumbnail']); ?>" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                        <?php endif; ?>
                        <span style="color: #cbd5e1; font-size: 16px;">
                            <?php echo e($socialStats['youtube']['channel_name'] ?? 'YouTube Channel'); ?>

                        </span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; color: #cbd5e1; margin-bottom: 20px;">
                        <div>
                            <p style="color: #64748b;">Subscribers</p>
                            <p style="font-size: 18px; font-weight: 700;">
                                <?php echo e(isset($socialStats['youtube']['subscribers']) ? number_format($socialStats['youtube']['subscribers']) : '—'); ?>

                            </p>
                        </div>
                        <div>
                            <p style="color: #64748b;">Views</p>
                            <p style="font-size: 18px; font-weight: 700;">
                                <?php echo e(isset($socialStats['youtube']['views']) ? number_format($socialStats['youtube']['views']) : '—'); ?>

                            </p>
                        </div>
                        <div>
                            <p style="color: #64748b;">Videos</p>
                            <p style="font-size: 18px; font-weight: 700;">
                                <?php echo e(isset($socialStats['youtube']['videos']) ? number_format($socialStats['youtube']['videos']) : '—'); ?>

                            </p>
                        </div>
                    </div>
                    <form method="POST" action="<?php echo e(route('social.disconnect', 'youtube')); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn-new" style="background:#2d2d2d; color:#f87171; padding: 12px 24px; border-radius: 12px;">Disconnect YouTube</button>
                    </form>
                <?php else: ?>
                    <p style="color: #94a3b8; margin-bottom: 10px;">Not connected</p>
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Connect to see your channel stats and recent videos.</p>
                    <a href="<?php echo e(route('social.redirect', 'youtube')); ?>" class="btn-new" style="background:#FF0000; padding: 12px 24px; border-radius: 12px; display: inline-block;">Connect YouTube</a>
                <?php endif; ?>
            </div>

            
            <div class="card" style="border-left: 4px solid #6e5494;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                    <i class="fab fa-github" style="color: #6e5494; font-size: 24px;"></i>
                    <h3 style="color: white; font-size: 16px; font-weight: 600;">GitHub</h3>
                </div>
                <?php if($socialStats['github']['connected'] ?? false): ?>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                        <?php if($socialStats['github']['avatar'] ?? null): ?>
                            <img src="<?php echo e($socialStats['github']['avatar']); ?>" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover;">
                        <?php endif; ?>
                        <span style="color: #cbd5e1; font-size: 16px;"><?php echo e($socialStats['github']['username'] ?? 'GitHub User'); ?></span>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <p style="color: #94a3b8;">Repos: <strong style="color: white;"><?php echo e(number_format($socialStats['github']['repos'] ?? 0)); ?></strong></p>
                        <p style="color: #94a3b8;">Followers: <strong style="color: white;"><?php echo e(number_format($socialStats['github']['followers'] ?? 0)); ?></strong></p>
                        <?php if($socialStats['github']['bio'] ?? null): ?>
                            <p style="color: #64748b; font-size: 13px;"><?php echo e($socialStats['github']['bio']); ?></p>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="<?php echo e(route('social.disconnect', 'github')); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn-new" style="background:#2d2d2d; color:#f87171; padding: 12px 24px; border-radius: 12px;">Disconnect GitHub</button>
                    </form>
                <?php else: ?>
                    <p style="color: #94a3b8; margin-bottom: 10px;">Not connected</p>
                    <a href="<?php echo e(route('social.redirect', 'github')); ?>" class="btn-new" style="background:#6e5494; padding: 12px 24px; border-radius: 12px; display: inline-block;">Connect GitHub</a>
                <?php endif; ?>
            </div>
        </div>

        
        <?php if(($socialStats['youtube']['connected'] ?? false) && !empty($socialStats['youtube']['recent'])): ?>
            <h2 style="margin-bottom: 20px; font-size: 22px; font-weight: 700; color: #cbd5e1;">Recent Videos</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <?php $__currentLoopData = $socialStats['youtube']['recent']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card" style="padding:15px; background:#121212; border:1px solid #1f1f1f; border-radius:12px;">
                        <?php
                            $thumb = $video['snippet']['thumbnails']['medium']['url'] ?? $video['snippet']['thumbnails']['default']['url'] ?? '';
                            $videoId = $video['id']['videoId'] ?? '';
                            $likes = $video['statistics']['likeCount'] ?? 0;
                            $comments = $video['statistics']['commentCount'] ?? 0;
                        ?>
                        <?php if($thumb): ?>
                            <a href="https://www.youtube.com/watch?v=<?php echo e($videoId); ?>" target="_blank">
                                <img src="<?php echo e($thumb); ?>" style="width:100%; border-radius:8px; margin-bottom:10px;">
                            </a>
                        <?php endif; ?>
                        <p style="color:#cbd5e1; font-size:14px; line-height:1.4; margin-bottom:8px;">
                            <?php echo e(\Illuminate\Support\Str::limit($video['snippet']['title'] ?? 'Untitled', 60)); ?>

                        </p>
                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:11px; color:#64748b; margin-bottom:5px;">
                            <span><?php echo e(\Carbon\Carbon::parse($video['snippet']['publishedAt'])->diffForHumans()); ?></span>
                            <a href="https://www.youtube.com/watch?v=<?php echo e($videoId); ?>" target="_blank" style="color:#FF0000; text-decoration:none;">Watch →</a>
                        </div>
                        <div style="display:flex; gap:20px; font-size:13px; color:#94a3b8; border-top:1px solid #2d2d2d; padding-top:8px;">
                            <span>❤️ <?php echo e(number_format($likes)); ?></span>
                            <span class="comment-trigger" data-video-id="<?php echo e($videoId); ?>" style="cursor:pointer;">💬 <?php echo e(number_format($comments)); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php elseif($socialStats['youtube']['connected'] ?? false): ?>
            <div style="background:#121212; padding:30px; border-radius:20px; border:1px solid #1f1f1f; text-align:center; color:#64748b; margin-bottom: 30px;">
                <p>No recent videos found. Make sure you have uploaded videos to this YouTube channel.</p>
            </div>
        <?php endif; ?>

        
        <?php if(($socialStats['github']['connected'] ?? false) && !empty($socialStats['github']['repo_list'])): ?>
            <h2 style="margin-bottom: 20px; font-size: 22px; font-weight: 700; color: #cbd5e1;">Recent Repositories</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <?php $__currentLoopData = $socialStats['github']['repo_list']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $repo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card" style="padding:15px; background:#121212; border:1px solid #1f1f1f; border-radius:12px;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px;">
                            <a href="<?php echo e($repo['url']); ?>" target="_blank" style="color: #0ea5e9; font-weight: 600; text-decoration: none; font-size: 15px;">
                                <?php echo e($repo['name']); ?>

                            </a>
                            <?php if($repo['language']): ?>
                                <span style="font-size: 11px; color: #64748b; background: #1a1a1a; padding: 2px 8px; border-radius: 4px;">
                                    <?php echo e($repo['language']); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if($repo['description']): ?>
                            <p style="color: #94a3b8; font-size: 13px; line-height: 1.4; margin-bottom: 8px;">
                                <?php echo e($repo['description']); ?>

                            </p>
                        <?php endif; ?>
                        <div style="display: flex; align-items: center; gap: 10px; font-size: 12px; color: #64748b;">
                            <span>⭐ <?php echo e($repo['stars']); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php elseif($socialStats['github']['connected'] ?? false): ?>
            <div style="background:#121212; padding:20px; border-radius:20px; border:1px solid #1f1f1f; text-align:center; color:#64748b; margin-bottom: 30px;">
                <p>No recent repositories found.</p>
            </div>
        <?php endif; ?>
    </section>
</div>


<div id="commentModal" class="modal" style="display:none; position:fixed; z-index:10000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.8);">
    <div class="modal-content" style="background:#121212; margin:5% auto; padding:20px; border:1px solid #2d2d2d; border-radius:15px; width:90%; max-width:600px; max-height:80vh; overflow-y:auto; color:white;">
        <span id="closeModal" style="float:right; font-size:24px; cursor:pointer;">&times;</span>
        <h3 style="margin-bottom:15px;">Comments</h3>
        <div id="commentList" style="margin-top:10px;">Loading...</div>
        <div style="margin-top:16px; border-top:1px solid #2d2d2d; padding-top:12px;">
            <label for="newComment" style="color:#cbd5e1; font-weight:600;">Add a comment</label>
            <textarea id="newComment" rows="3" style="width:100%; margin-top:8px; background:#0b1220; border:1px solid #1f1f1f; color:#e6eef8; padding:8px; border-radius:6px;" placeholder="Write a comment..."></textarea>
            <div style="display:flex; gap:8px; margin-top:8px;">
                <button id="postCommentBtn" class="btn-new" style="background:#0ea5e9; color:#06121a; padding:8px 12px; border-radius:6px;">Post Comment</button>
                <button id="cancelCommentBtn" class="btn-secondary" style="background:#1f2937; color:#cbd5e1; padding:8px 12px; border-radius:6px;">Cancel</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('commentModal');
        const closeBtn = document.getElementById('closeModal');
        const commentList = document.getElementById('commentList');
        const newCommentEl = document.getElementById('newComment');
        const postCommentBtn = document.getElementById('postCommentBtn');
        const cancelCommentBtn = document.getElementById('cancelCommentBtn');
        const commentPostBase = "<?php echo e(url('/youtube/comment')); ?>";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

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

                postCommentBtn.onclick = function () {
                    const text = newCommentEl.value.trim();
                    if (!text) return alert('Comment cannot be empty');
                    postCommentBtn.disabled = true;
                    fetch(`${commentPostBase}/${videoId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ comment: text })
                    })
                    .then(r => r.json())
                    .then(res => {
                        postCommentBtn.disabled = false;
                        if (res.error) {
                            if (res.error === 'unauthorized' || res.reconnect_url) {
                                const msg = res.message || 'YouTube authorization expired. Reconnect to continue.';
                                if (confirm(msg + '\n\nReconnect now?')) {
                                    window.location.href = res.reconnect_url || "<?php echo e(route('social.redirect', 'youtube')); ?>";
                                }
                                return;
                            }
                            alert('Failed to post comment: ' + (res.message ?? res.error?.message ?? res.error));
                            return;
                        }
                        const snippet = res.data?.snippet?.topLevelComment?.snippet ?? null;
                        const author = snippet?.authorDisplayName ?? 'You';
                        const avatar = snippet?.authorProfileImageUrl ?? '';
                        const published = 'just now';
                        const textHtml = newCommentEl.value;
                        const newHtml = `
                            <div style="border-bottom:1px solid #2d2d2d; padding:8px 0;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:5px;">
                                    ${avatar ? `<img src="${avatar}" style="width:32px; height:32px; border-radius:50%;">` : ''}
                                    <strong style="color:#cbd5e1;">${author}</strong>
                                    <span style="color:#64748b; font-size:12px;">${published}</span>
                                </div>
                                <p style="color:#94a3b8; margin:0;">${textHtml}</p>
                            </div>
                        `;
                        commentList.innerHTML = newHtml + commentList.innerHTML;
                        newCommentEl.value = '';
                    })
                    .catch(err => {
                        postCommentBtn.disabled = false;
                        alert('Failed to post comment');
                    });
                };
                cancelCommentBtn.onclick = () => newCommentEl.value = '';
            });
        });

        closeBtn.onclick = () => modal.style.display = 'none';
        window.onclick = (e) => { if (e.target == modal) modal.style.display = 'none'; };
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Connect\resources\views/dashboard.blade.php ENDPATH**/ ?>