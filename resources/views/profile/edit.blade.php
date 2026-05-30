@extends('layouts.app')

@section('content')
<div style="width: 100%; display: flex; justify-content: center; background: #0a0a0a; min-height: calc(100vh - 80px); align-items: center; padding: 2rem;">
    <div style="width: 100%; max-width: 1000px;">

        <div style="text-align: center; margin-bottom: 2.5rem;">
            <h1 style="font-size: 2.2rem; font-weight: 700; background: linear-gradient(135deg, #ffffff 0%, #3b82f6 100%); -webkit-background-clip: text; background-clip: text; color: transparent; margin: 0 0 0.5rem 0;">Edit Profile</h1>
            <p style="color: #9ca3af; font-size: 1rem;">Update your avatar and personal details</p>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div style="display: flex; flex-direction: column; align-items: center; gap: 2rem; margin-bottom: 2.5rem;">
                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2.5rem; width: 100%;">
                    <div style="position: relative;">
                        <img id="avatarPreview" 
                             src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : 'https://ui-avatars.com/api/?background=1f1f1f&color=3b82f6&size=120&name=' . urlencode($user->name) }}" 
                             style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 3px solid #3b82f6; box-shadow: 0 0 15px rgba(59,130,246,0.4); background: #1f1f1f;">
                        <div style="position: absolute; bottom: 6px; right: 6px; background: #3b82f6; border-radius: 50%; padding: 8px; border: 2px solid #0a0a0a;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        </div>
                    </div>
                    <div style="text-align: center;">
                        <label style="display: inline-flex; align-items: center; gap: 0.6rem; background: #1f1f1f; padding: 0.7rem 1.5rem; border-radius: 40px; font-size: 0.9rem; font-weight: 500; color: #3b82f6; cursor: pointer; border: 1px solid #3b82f6;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-8-4-4m0 0L8 8m4-4v12"/></svg>
                            Choose File
                            <input type="file" name="profile_picture" id="profilePic" accept="image/jpeg,image/png,image/gif" style="display: none;">
                        </label>
                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 10px;">JPG, PNG or GIF · Max 2 MB</div>
                        @error('profile_picture')<div style="color: #ef4444; font-size: 0.8rem;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.8rem; margin-bottom: 2.5rem;">
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: #3b82f6;">Full name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" style="background: #141414; border: 1px solid #2c2c2c; border-radius: 20px; padding: 1rem 1.2rem; font-size: 1rem; color: #f0f0f0; width: 100%;">
                    @error('name')<div style="color: #ef4444;">{{ $message }}</div>@enderror
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                    <label style="font-weight: 600; font-size: 0.9rem; color: #3b82f6;">Email address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" style="background: #141414; border: 1px solid #2c2c2c; border-radius: 20px; padding: 1rem 1.2rem; font-size: 1rem; color: #f0f0f0; width: 100%;">
                    @error('email')<div style="color: #ef4444;">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 0.9rem 2.5rem; border-radius: 50px; font-weight: 600; font-size: 1rem; cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-right: 8px;"><path d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    const fileInput = document.getElementById('profilePic');
    const avatarPreview = document.getElementById('avatarPreview');
    if (fileInput && avatarPreview) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.size <= 2 * 1024 * 1024) {
                const reader = new FileReader();
                reader.onload = function(ev) { avatarPreview.src = ev.target.result; };
                reader.readAsDataURL(file);
            } else if (file) { alert('File too large. Max size is 2 MB.'); fileInput.value = ''; }
        });
    }
</script>
@endsection