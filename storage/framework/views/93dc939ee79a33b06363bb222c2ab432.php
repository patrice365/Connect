<?php $__env->startSection('content'); ?>
<div class="content-body" style="justify-content: center;">
    <section class="feed" style="max-width: 800px;">
        <h1 class="page-title-main">Create New Post</h1>

        <div class="card">
            
            <div style="display: flex; gap: 0; margin-bottom: 30px; border-bottom: 1px solid #2d2d2d;">
                <button id="tab-youtube" class="tab-btn" style="flex:1; padding:12px; background:none; border:none; color:white; font-weight:600; border-bottom:2px solid #FF0000; cursor:pointer;">
                    <i class="fab fa-youtube"></i> YouTube
                </button>
                <button id="tab-github" class="tab-btn" style="flex:1; padding:12px; background:none; border:none; color:#94a3b8; font-weight:600; border-bottom:2px solid transparent; cursor:pointer;">
                    <i class="fab fa-github"></i> GitHub
                </button>
            </div>

            <?php if($errors->any()): ?>
                <div style="background: rgba(239,68,68,0.2); border:1px solid #ef4444; color:#fca5a5; padding:12px; border-radius:10px; margin-bottom:20px;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p><?php echo e($error); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            
            <form id="youtube-form" action="<?php echo e(route('posts.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="platform" value="youtube">

                <?php if(!$connected['youtube']): ?>
                    <div style="background: rgba(239,68,68,0.2); border:1px solid #ef4444; padding:12px; border-radius:10px; margin-bottom:20px;">
                        You must <a href="<?php echo e(route('social.redirect', 'youtube')); ?>" style="color:#0ea5e9;">connect your YouTube account</a> to publish videos.
                    </div>
                <?php endif; ?>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="youtube-title" style="color:#cbd5e1; font-weight:600;">Video Title</label>
                    <input type="text" name="title" id="youtube-title" placeholder="Enter video title" required
                           style="width:100%; background:#1a1a1a; border:1px solid #2d2d2d; padding:12px; border-radius:8px; color:white;">
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="youtube-content" style="color:#cbd5e1; font-weight:600;">Description</label>
                    <textarea name="content" id="youtube-content" rows="6" placeholder="Describe your video..."
                              style="width:100%; background:#1a1a1a; border:1px solid #2d2d2d; padding:12px; border-radius:8px; color:white;"></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="color:#cbd5e1; font-weight:600;">Video File (required)</label>
                    <input type="file" name="video_file" accept="video/*" required
                           style="width:100%; background:#1a1a1a; border:1px solid #2d2d2d; padding:12px; border-radius:8px; color:white;">
                    <small style="color:#64748b;">Max 500 MB</small>
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="color:#cbd5e1; font-weight:600;">Custom Thumbnail (optional)</label>
                    <input type="file" name="thumbnail" accept="image/*"
                           style="width:100%; background:#1a1a1a; border:1px solid #2d2d2d; padding:12px; border-radius:8px; color:white;">
                </div>

                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <button type="submit" name="action" value="publish" class="btn-auth" style="flex:1; background: linear-gradient(135deg, #0ea5e9, #2563eb);">
                        Publish to YouTube
                    </button>
                    <button type="submit" name="action" value="draft" class="btn-auth" style="flex:1; background: #2d2d2d;">
                        Save as Draft
                    </button>
                </div>
            </form>

            
            <form id="github-form" action="<?php echo e(route('posts.store')); ?>" method="POST" enctype="multipart/form-data" style="display:none;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="platform" value="github">

                <?php if(!$connected['github']): ?>
                    <div style="background: rgba(239,68,68,0.2); border:1px solid #ef4444; padding:12px; border-radius:10px; margin-bottom:20px;">
                        You must <a href="<?php echo e(route('social.redirect', 'github')); ?>" style="color:#0ea5e9;">connect your GitHub account</a> to publish repositories.
                    </div>
                <?php endif; ?>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="repo-name" style="color:#cbd5e1; font-weight:600;">Repository Name</label>
                    <input type="text" name="repo_name" id="repo-name" placeholder="my-awesome-project" required
                           style="width:100%; background:#1a1a1a; border:1px solid #2d2d2d; padding:12px; border-radius:8px; color:white;">
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="github-content" style="color:#cbd5e1; font-weight:600;">Description</label>
                    <textarea name="content" id="github-content" rows="6" placeholder="A short description of the repository..."
                              style="width:100%; background:#1a1a1a; border:1px solid #2d2d2d; padding:12px; border-radius:8px; color:white;"></textarea>
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="repo-visibility" style="color:#cbd5e1; font-weight:600;">Visibility</label>
                    <select name="repo_visibility" id="repo-visibility"
                            style="width:100%; background:#1a1a1a; border:1px solid #2d2d2d; padding:12px; border-radius:8px; color:white;">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="color:#cbd5e1; font-weight:600;">Initialize with a README</label>
                    <input type="checkbox" name="init_readme" value="1" checked
                           style="accent-color:#0ea5e9; margin-top:8px;"> Add a README file
                </div>
                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="color:#cbd5e1; font-weight:600;">Upload Files (optional)</label>
                    <input type="file" name="files[]" multiple
                           style="width:100%; background:#1a1a1a; border:1px solid #2d2d2d; padding:12px; border-radius:8px; color:white;">
                    <small style="color:#64748b;">Max 10 MB per file</small>
                </div>

                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <button type="submit" class="btn-auth" style="flex:1; background: linear-gradient(135deg, #6e5494, #6e5494);">
                        Create Repository
                    </button>
                </div>
            </form>
        </div>

        <div style="background: #1a1a1a; border: 1px solid #2d2d2d; border-radius: 12px; padding: 20px; margin-top: 20px;">
            <h4 style="color: #94a3b8; margin-bottom: 12px;">Tips</h4>
            <ul style="color: #64748b; font-size: 14px; list-style: disc; padding-left: 20px;">
                <li>For YouTube: upload a high‑quality video and optionally a custom thumbnail.</li>
                <li>For GitHub: you can create a repository with files; a README is auto‑generated.</li>
                <li>Make sure your platform accounts are connected before publishing.</li>
            </ul>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const tabYt = document.getElementById('tab-youtube');
    const tabGh = document.getElementById('tab-github');
    const formYt = document.getElementById('youtube-form');
    const formGh = document.getElementById('github-form');

    tabYt.addEventListener('click', () => {
        tabYt.style.borderBottomColor = '#FF0000';
        tabGh.style.borderBottomColor = 'transparent';
        tabYt.style.color = 'white';
        tabGh.style.color = '#94a3b8';
        formYt.style.display = 'block';
        formGh.style.display = 'none';
    });

    tabGh.addEventListener('click', () => {
        tabGh.style.borderBottomColor = '#6e5494';
        tabYt.style.borderBottomColor = 'transparent';
        tabGh.style.color = 'white';
        tabYt.style.color = '#94a3b8';
        formGh.style.display = 'block';
        formYt.style.display = 'none';
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Connect\resources\views/posts/create.blade.php ENDPATH**/ ?>