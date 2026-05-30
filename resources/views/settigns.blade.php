@extends('layouts.app')

@section('content')
<div class="content-body" style="display:block; padding:25px;">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2 style="color:white; margin-bottom:20px;">Settings</h2>

        @if (session('status'))
            <div style="color:#34d399; margin-bottom:15px;">{{ session('status') }}</div>
        @endif

        <form action="{{ route('settings.save') }}" method="POST">
            @csrf

            <!-- Color Theme -->
            <div class="form-group" style="margin-bottom:20px;">
                <label for="theme" style="color:#cbd5e1;">Interface Color</label>
                <select name="theme" id="theme"
                        style="width:100%; background:#1a1a1a; border:1px solid #2d2d2d; padding:12px; border-radius:10px; color:white;">
                    <option value="default" {{ session('theme', 'default') == 'default' ? 'selected' : '' }}>Default Dark</option>
                    <option value="blue" {{ session('theme') == 'blue' ? 'selected' : '' }}>Blue Accent</option>
                    <option value="green" {{ session('theme') == 'green' ? 'selected' : '' }}>Green Accent</option>
                    <option value="purple" {{ session('theme') == 'purple' ? 'selected' : '' }}>Purple Accent</option>
                </select>
            </div>

            <button type="submit" class="btn-new" style="background:#0ea5e9;">Save Settings</button>
        </form>
    </div>
</div>
@endsection