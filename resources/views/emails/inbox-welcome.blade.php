<x-emails.layout :show-brand="false" :show-footer="false">
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.inbox_welcome.greeting', ['name' => $greetingName]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ $intro }}
    </p>
    <p style="margin:0 0 8px;font-size:15px;line-height:1.7;color:#374151;font-weight:600;">
        {{ __('talenma.mail.inbox_welcome.how_title') }}
    </p>
    <ul style="margin:0 0 24px;padding-left:20px;font-size:14px;line-height:1.7;color:#4b5563;">
        @foreach ($tips as $tip)
            <li style="margin:0 0 6px;">{{ $tip }}</li>
        @endforeach
    </ul>
    <p style="margin:0 0 24px;">
        <a href="{{ $inboxUrl }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.inbox_welcome.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ $closing }}
    </p>
</x-emails.layout>
