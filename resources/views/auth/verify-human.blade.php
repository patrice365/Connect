@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-4 text-center">Human Verification</h2>
        <p class="text-gray-700 mb-6 text-center">
            Please confirm you are not a robot to continue.
        </p>

        @if ($errors->any())
            <div class="mb-4 text-red-500 text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('verification.human.verify') }}">
            @csrf
            {{-- This renders the reCAPTCHA widget --}}
            {!! htmlFormSnippet() !!}

            <div class="mt-6">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Verify
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- This loads the reCAPTCHA JavaScript --}}
@push('scripts')
    {!! htmlScriptTagJsApi() !!}
@endpush