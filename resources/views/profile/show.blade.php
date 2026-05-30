@extends('layouts.app')

@section('content')
<div class="profile-page" style="max-width: 1000px; margin: 0 auto; padding: 2rem;">
    <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start;">
        <div style="flex: 0 0 220px; text-align: center;">
            <img src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : 'https://ui-avatars.com/api/?background=1f1f1f&color=3b82f6&size=180&name=' . urlencode($user->name) }}"
                 alt="{{ $user->name }}"
                 style="width: 180px; height: 180px; border-radius: 50%; object-fit: cover; border: 4px solid #3b82f6;">
            <h1 style="margin-top: 1rem; font-size: 1.8rem; color: #ffffff;">{{ $user->name }}</h1>
            <p style="color: #94a3b8; margin-top: 0.5rem;">{{ $user->username ?? '@' . Str::slug($user->name, '_') }}</p>
            <a href="{{ route('profile.edit') }}" style="display: inline-block; margin-top: 1rem; background: #3b82f6; color: #fff; padding: 0.9rem 1.5rem; border-radius: 999px; text-decoration: none;">Edit Profile</a>
        </div>

        <div style="flex: 1 1 1px; background: #111827; border: 1px solid #1f2937; border-radius: 24px; padding: 2rem; color: #e5e7eb;">
            <h2 style="font-size: 1.4rem; margin-bottom: 1rem; color: #ffffff;">Profile Overview</h2>
            <div style="display: grid; gap: 1rem;">
                <div style="display: flex; justify-content: space-between; gap: 1rem; padding: 1rem; background: #0f172a; border-radius: 18px;">
                    <span style="color: #9ca3af;">Email</span>
                    <strong>{{ $user->email }}</strong>
                </div>
                @if($user->bio)
                <div style="padding: 1rem; background: #0f172a; border-radius: 18px;">
                    <span style="color: #9ca3af;">About</span>
                    <p style="margin-top: 0.5rem; line-height: 1.7;">{{ $user->bio }}</p>
                </div>
                @endif
                @if($user->location)
                <div style="display: flex; justify-content: space-between; gap: 1rem; padding: 1rem; background: #0f172a; border-radius: 18px;">
                    <span style="color: #9ca3af;">Location</span>
                    <strong>{{ $user->location }}</strong>
                </div>
                @endif
                @if($user->website)
                <div style="display: flex; justify-content: space-between; gap: 1rem; padding: 1rem; background: #0f172a; border-radius: 18px;">
                    <span style="color: #9ca3af;">Website</span>
                    <a href="{{ $user->website }}" target="_blank" rel="noopener noreferrer" style="color: #60a5fa; text-decoration: none;">{{ $user->website }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
