@extends('layouts.app')

@section('content')
<div class="profile-container" style="max-width:1200px; margin:0 auto;">
    <h1 class="page-title">Profile Settings</h1>

    <div class="card">
        <div class="profile-avatar-section">
            <div class="large-avatar"></div>
            <div class="avatar-info">
                <h2>{{ Auth::user()->name ?? 'User Name' }}</h2>
                <p>Manage your personal profile and account settings.</p>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', Auth::user()->first_name ?? '') }}">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', Auth::user()->last_name ?? '') }}">
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', Auth::user()->email ?? '') }}">
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label>Biography</label>
                <input type="text" name="bio" class="form-control" placeholder="Tell us about yourself..." value="{{ old('bio', Auth::user()->bio ?? '') }}">
            </div>

            <div style="overflow: hidden;">
                <button type="submit" class="btn-update">Update Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection