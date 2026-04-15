@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Archived Posts</h1>

    @forelse($posts as $post)
        <div class="bg-white shadow rounded-lg p-6 mb-4 border border-gray-200">
            <p class="text-gray-800 mb-2">{{ $post->content }}</p>
            <div class="text-sm text-gray-500 mb-4">
                Archived {{ $post->archived_at->diffForHumans() }}
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:underline">View Comments</a>
                <a href="{{ route('posts.edit', $post) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                <form action="{{ route('posts.restore', $post->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-green-600 hover:text-green-800">Restore to Published</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-gray-500">No archived posts.</p>
    @endforelse

    {{ $posts->links() }}
</div>
@endsection