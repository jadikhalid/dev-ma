@php
    $type = (string) ($type ?? '');
    $url = (string) ($url ?? '');
    $href = \App\Support\TalentCv\TalentCvLinkHelper::href($url);
    $text = \App\Support\TalentCv\TalentCvLinkHelper::display($type, $url);
@endphp
<a href="{{ $href }}" class="social-link" title="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $text }}</a>
