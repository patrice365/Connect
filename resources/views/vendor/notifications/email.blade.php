<x-mail::message>
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
# Welcome to Connect! 🚀
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}
@endforeach

{{-- If no custom intro lines are provided, show a default message --}}
@if (empty($introLines))
Thank you for creating an account with **Connect**. We're excited to have you on board!
Please verify your email address to get started.
@endif

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}
@endforeach

@if (empty($outroLines))
Once verified, you'll have full access to your dashboard and all the tools you need to manage your social presence.
@endif

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
Cheers,<br>
**The Connect Team**
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below into your web browser:",
    ['actionText' => $actionText]
)
<span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>