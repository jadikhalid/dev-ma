<x-emails.layout>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_welcome.greeting', ['name' => $user->formalDisplayName()]) }}
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;font-weight:700;color:#111827;">
        {{ __('talenma.mail.talent_welcome.welcome') }}
    </p>
    <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_welcome.joined') }}
    </p>
    <p style="margin:0 0 8px;font-size:15px;line-height:1.7;font-weight:700;color:#111827;">
        {{ __('talenma.mail.talent_welcome.action_title') }}
    </p>
    <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_welcome.action') }}
    </p>
    <p style="margin:0 0 28px;">
        <a href="{{ route('profile.edit', ['panel' => 'talent']) }}" style="display:inline-block;padding:12px 24px;background-color:#4f46e5;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;border-radius:12px;">
            {{ __('talenma.mail.talent_welcome.cta') }}
        </a>
    </p>
    <p style="margin:0 0 16px;font-size:15px;line-height:1.7;font-weight:700;color:#111827;">
        {{ __('talenma.mail.talent_welcome.find_title') }}
    </p>
    @foreach (__('talenma.mail.talent_welcome.points') as $point)
        <p style="margin:0 0 14px;font-size:15px;line-height:1.7;color:#374151;">
            <strong style="color:#111827;">{{ $point['title'] }}</strong>
            {{ $point['text'] }}
        </p>
    @endforeach
    <p style="margin:12px 0 0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_welcome.closing') }}
    </p>
    <p style="margin:0;font-size:15px;line-height:1.7;color:#374151;">
        {{ __('talenma.mail.talent_welcome.signature') }}
    </p>
</x-emails.layout>
