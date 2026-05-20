@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('posts.index') }}" class="text-blue-600 hover:underline inline-flex items-center">
            ← Back to My Posts
        </a>
    </div>

    <!-- Post Header (unchanged) -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h1 class="text-2xl font-bold mb-4">{{ $post->user->name }}'s Post</h1>
        <p class="text-gray-800 text-lg mb-4">{{ $post->content }}</p>
        
        @if($post->media_urls)
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($post->media_urls as $url)
                    <img src="{{ $url }}" alt="Post media" class="w-32 h-32 object-cover rounded">
                @endforeach
            </div>
        @endif

        <div class="flex justify-between items-center text-sm text-gray-500 border-t pt-4">
            <div>
                Posted {{ $post->published_at ? $post->published_at->diffForHumans() : 'just now' }}
            </div>
            @can('update', $post)
                <div class="flex space-x-4">
                    <a href="{{ route('posts.edit', $post) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                </div>
            @endcan
        </div>
    </div>

    <!-- Comments Section (unchanged) -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold mb-6">💬 Comments ({{ $post->comments_count ?? 0 }})</h2>

        @auth
            <form action="{{ route('posts.comments.store', $post) }}" method="POST" class="mb-8 pb-8 border-b">
                @csrf
                <textarea 
                    name="content" 
                    rows="3" 
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500 @error('content') border-red-500 @enderror"
                    placeholder="Write a comment..."
                    required>{{ old('content') }}</textarea>
                
                @error('content')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror

                <button type="submit" class="mt-3 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                    📝 Post Comment
                </button>
            </form>
        @else
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <p class="text-blue-800"><a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Sign in</a> to comment</p>
            </div>
        @endauth

        @forelse($post->comments()->whereNull('parent_id')->with('user', 'replies.user', 'reactions')->latest()->get() as $comment)
            <div class="border-b border-gray-200 py-6 last:border-b-0">
                <!-- Comment Header (unchanged) -->
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <strong class="text-gray-900">{{ $comment->user->name }}</strong>
                        <p class="text-sm text-gray-500">
                            {{ $comment->created_at->diffForHumans() }}
                            @if($comment->edited_at && $comment->edited_at->gt($comment->created_at))
                                <span class="text-gray-400">(edited)</span>
                            @endif
                        </p>
                    </div>

                    @can('update', $comment)
                        <div class="flex space-x-2 text-sm">
                            <button 
                                onclick="toggleEditComment({{ $comment->id }})" 
                                class="text-blue-600 hover:text-blue-800">
                                ✏️ Edit
                            </button>
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this comment?')">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>

                <!-- Comment Content & Edit Form (unchanged) -->
                <div id="comment-content-{{ $comment->id }}">
                    <p class="text-gray-800 mb-3">{{ $comment->content }}</p>
                </div>

                <form id="edit-form-{{ $comment->id }}" action="{{ route('comments.update', $comment) }}" method="POST" class="hidden mb-3">
                    @csrf
                    @method('PATCH')
                    <textarea 
                        name="content" 
                        rows="2"
                        class="w-full border border-gray-300 rounded-lg p-2 text-sm"
                        required>{{ $comment->content }}</textarea>
                    <div class="mt-2 flex space-x-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">Save</button>
                        <button type="button" onclick="toggleEditComment({{ $comment->id }})" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-1 rounded text-sm">Cancel</button>
                    </div>
                </form>

                <!-- Reactions Section -->
                <div class="flex flex-wrap gap-2 items-center mb-3 bg-gray-50 p-2 rounded">
                    @php
                        $reactions = $comment->reactions->groupBy('type')->map->count();
                    @endphp

                    @forelse($reactions as $type => $count)
                        <span class="inline-flex items-center gap-1 bg-white border border-gray-200 rounded-full px-2 py-1 text-sm">
                            @php
                                $emojis = [
                                    'like' => '👍',
                                    'love' => '❤️',
                                    'haha' => '😂',
                                    'wow' => '😮',
                                    'sad' => '😢',
                                    'angry' => '😠'
                                ];
                            @endphp
                            {{ $emojis[$type] ?? $type }} <span class="text-gray-600">{{ $count }}</span>
                        </span>
                    @empty
                        <span class="text-gray-400 text-sm">No reactions</span>
                    @endforelse

                    <!-- React Button -->
                    @auth
                        <button 
                            onclick="window.toggleReactionMenu({{ $comment->id }}, 'comment')"
                            class="ml-auto text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full px-2 py-1">
                            👍 React
                        </button>
                    @endauth
                </div>

                <!-- Reaction Menu -->
                <div id="reaction-menu-{{ $comment->id }}-comment" class="hidden bg-gray-100 p-2 rounded mb-3">
                    <div class="flex gap-2 flex-wrap">
                        @foreach(['like' => '👍', 'love' => '❤️', 'haha' => '😂', 'wow' => '😮', 'sad' => '😢', 'angry' => '😠'] as $reaction => $emoji)
                            <button 
                                onclick="window.addReaction('comment', {{ $comment->id }}, '{{ $reaction }}')"
                                class="text-2xl hover:scale-125 transition">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Replies (unchanged) -->
                @if($comment->replies->count() > 0)
                    <div class="mt-4 ml-4 border-l-2 border-gray-200 pl-4">
                        <p class="text-sm font-semibold text-gray-600 mb-2">{{ $comment->replies->count() }} replies</p>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500 text-center py-8">No comments yet. Be the first to comment!</p>
        @endforelse
    </div>
</div>
@endsection

<script>
    // Toggle comment edit form (unchanged)
    function toggleEditComment(id) {
        const content = document.getElementById('comment-content-' + id);
        const form = document.getElementById('edit-form-' + id);
        if (content && form) {
            content.classList.toggle('hidden');
            form.classList.toggle('hidden');
        }
    }
</script>