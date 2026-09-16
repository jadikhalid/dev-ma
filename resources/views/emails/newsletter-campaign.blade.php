<x-emails.layout :show-footer="false" :brand-label="__('talenma.newsletter.email_brand')">
    {!! $bodyHtml !!}
    <x-slot:footer>
        @include('emails.partials.newsletter-footer')
    </x-slot:footer>
</x-emails.layout>
