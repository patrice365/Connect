@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">My Drafts</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @forelse($posts as $post)
        <div class="bg-white shadow rounded-lg p-6 mb-4 border border-gray-200">
            <p class="text-gray-800 mb-2">{{ $post->content }}</p>
            <div class="text-sm text-gray-500 mb-4">
                Last saved {{ $post->drafted_at ? $post->drafted_at->diffForHumans() : 'just now' }}
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('posts.show', $post) }}" class="text-blue-600 hover:underline">View Comments</a>
                <a href="{{ route('posts.edit', $post) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Permanently delete this draft?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <p class="text-gray-500">You have no drafts. <a href="{{ route('posts.create') }}" class="text-blue-500">Create one</a>.</p>
    @endforelse

    {{ $posts->links() }}
</div>
@endsection