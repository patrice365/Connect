<?php $__env->startSection('content'); ?>
<div class="content-body" style="justify-content: center;">
    <section class="feed" style="max-width: 800px;">
        <h1 class="page-title-main">Create New Post</h1>

        <div class="card">
            <form action="<?php echo e(route('posts.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label for="content" style="display: block; color: #cbd5e1; font-size: 16px; font-weight: 600; margin-bottom: 12px;">
                        What would you like to share?
                    </label>
                    <textarea
                        name="content"
                        id="content"
                        rows="10"
                        maxlength="5000"
                        placeholder="What's on your mind? (Max 5000 characters)"
                        style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1);
                               padding: 18px; border-radius: 15px; color: white; outline: none; resize: vertical;
                               transition: 0.3s; font-size: 15px; line-height: 1.6;"
                        required
                    ><?php echo e(old('content')); ?></textarea>
                    <div style="text-align: right; color: #64748b; font-size: 12px; margin-top: 8px;">
                        <span id="character-count">0</span> / 5000 characters
                    </div>
                    <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p style="color: #fca5a5; font-size: 13px; margin-top: 5px;"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                    <button type="submit" name="publish" value="1"
                            style="flex: 1; background: linear-gradient(135deg, #0ea5e9, #2563eb); color: white;
                                   padding: 22px; border: none; border-radius: 15px; font-weight: 800; font-size: 18px;
                                   cursor: pointer; transition: 0.3s; box-shadow: 0 10px 25px rgba(14,165,233,0.4);
                                   text-transform: uppercase; letter-spacing: 1px;">
                        Publish Post
                    </button>
                    <button type="submit"
                            style="flex: 1; background: #2d2d2d; color: white;
                                   padding: 22px; border: none; border-radius: 15px; font-weight: 800; font-size: 18px;
                                   cursor: pointer; transition: 0.3s; box-shadow: 0 10px 25px rgba(0,0,0,0.4);
                                   text-transform: uppercase; letter-spacing: 1px;">
                        Save as Draft
                    </button>
                </div>
            </form>

            <div style="background: #1a1a1a; border: 1px solid #2d2d2d; border-radius: 12px; padding: 20px; margin-top: 20px;">
                <h4 style="color: #94a3b8; margin-bottom: 12px; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">Tips</h4>
                <ul style="color: #64748b; font-size: 14px; list-style: disc; padding-left: 20px;">
                    <li>Save as Draft to write and edit later</li>
                    <li>Publish Now to share with your audience</li>
                    <li>You can always edit or delete your posts</li>
                    <li>Published posts can be moved to trash after 30 days</li>
                </ul>
            </div>
        </div>
    </section>

    
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const textarea = document.getElementById('content');
    const counter = document.getElementById('character-count');
    textarea.addEventListener('input', () => {
        counter.textContent = textarea.value.length;
    });
    // Initialize count if old content exists
    counter.textContent = textarea.value.length;
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Connect\resources\views/posts/create.blade.php ENDPATH**/ ?>