@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <h1 class="text-3xl font-bold mb-6">Create New Post</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tips Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-blue-900 mb-2">💡 Tips:</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>• Save as Draft to write and edit later</li>
            <li>• Publish Now to share with your audience immediately</li>
            <li>• You can edit or delete your posts anytime</li>
            <li>• Maximum 5000 characters per post</li>
        </ul>
    </div>

    <form action="{{ route('posts.store') }}" method="POST" class="bg-white shadow rounded-lg p-6">
        @csrf

        <!-- Content Textarea -->
        <div class="mb-6">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Post Content</label>
            <textarea 
                id="content" 
                name="content" 
                rows="8" 
                class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500 @error('content') border-red-500 @enderror"
                placeholder="What's on your mind? (Max 5000 characters)"
                required>{{ old('content') }}</textarea>
            @error('content')
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
            @enderror
            <p class="text-gray-500 text-sm mt-2">
                <span id="char-count">0</span> / 5000 characters
            </p>
        </div>

        <!-- Buttons -->
        <div class="flex space-x-4">
            <!-- Save as Draft -->
            <button 
                type="submit" 
                name="action" 
                value="draft"
                class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                📝 Save as Draft
            </button>

            <!-- Publish -->
            <button 
                type="submit" 
                name="publish" 
                value="1"
                class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition">
                ✓ Publish Now
            </button>

            <!-- Cancel -->
            <a 
                href="{{ route('posts.index') }}" 
                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg text-center transition">
                Cancel
            </a>
        </div>
    </form>
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