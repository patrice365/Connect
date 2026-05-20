@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">Edit Post</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Post Status -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-blue-900 mb-2">📊 Post Status</h3>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <span class="text-sm text-gray-600">Status:</span>
                <p class="font-semibold">
                    @if($post->isDraft())
                        <span class="text-yellow-600">📝 Draft</span>
                    @elseif($post->isPublished())
                        <span class="text-green-600">✓ Published</span>
                    @elseif($post->isTrashed())
                        <span class="text-red-600">🗑️ Trashed</span>
                    @elseif($post->isArchived())
                        <span class="text-gray-600">📦 Archived</span>
                    @endif
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-600">Comments:</span>
                <p class="font-semibold">{{ $post->comments_count ?? 0 }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-600">Views:</span>
                <p class="font-semibold">{{ $post->views_count ?? 0 }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('posts.update', $post) }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf
        @method('PATCH')

        <!-- Content Textarea -->
        <div class="mb-6">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Post Content</label>
            <textarea 
                id="content" 
                name="content" 
                rows="8" 
                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500 @error('content') border-red-500 @enderror"
                placeholder="What's on your mind? (Max 5000 characters)"
                required>{{ old('content', $post->content) }}</textarea>
            @error('content')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
            <p class="text-gray-500 text-sm mt-2">
                <span id="char-count">{{ strlen($post->content ?? '') }}</span> / 5000 characters
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex space-x-4 mb-6">
            <!-- Update -->
            <button 
                type="submit"
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition">
                💾 Save Changes
            </button>

            <!-- Publish (for drafts) -->
            @if($post->isDraft())
                <button 
                    type="submit" 
                    name="publish" 
                    value="1"
                    class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition">
                    ✓ Publish Now
                </button>
            @endif

            <!-- Cancel -->
            <a 
                href="{{ route('posts.show', $post) }}" 
                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg text-center transition">
                Cancel
            </a>
        </div>

        <!-- Danger Zone -->
        <div class="border-t border-gray-200 pt-6">
            <h3 class="font-semibold text-red-600 mb-4">⚠️ Danger Zone</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Delete Button -->
                <form action="{{ route('posts.destroy', $post) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit" 
                        onclick="return confirm('Are you sure you want to delete this post?')"
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition">
                        🗑️ Delete
                    </button>
                </form>

                <!-- View Post Button -->
                <a 
                    href="{{ route('posts.show', $post) }}" 
                    class="w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded-lg text-center transition">
                    👁️ View Post
                </a>
            </div>
        </div>
    </form>

    <!-- Edit History -->
    @if($post->edited_at)
        <div class="mt-6 text-sm text-gray-500">
            Last edited {{ $post->updated_at->diffForHumans() }}
        </div>
    @endif
</div>

<script>
    // Character counter
    const textarea = document.getElementById('content');
    const charCount = document.getElementById('char-count');
    
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
</script>
@endsection
