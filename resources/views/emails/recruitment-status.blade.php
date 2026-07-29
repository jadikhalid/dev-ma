<x-emails.layout :show-brand="false" :show-footer="false">
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.recruitment_status.greeting', ['name' => $greetingName]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ $body }}
    </p>
    @if (filled($comment))
        <p style="margin:0 0 8px;font-size:13px;font-weight:600;color:#4f46e5;">
            {{ __('talenma.mail.recruitment_status.comment_label') }}
        </p>
        <p style="margin:0 0 24px;font-size:14px;line-height:1.7;color:#6b7280;padding:12px 16px;background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;white-space:pre-line;">
            {{ $comment }}
        </p>
    @endif
    <p style="margin:0 0 24px;">
        <a href="{{ $url }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.recruitment_status.cta') }}
        </a>
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.recruitment_status.closing') }}
    </p>
</x-emails.layout>
