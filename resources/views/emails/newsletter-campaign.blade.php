<x-emails.layout :show-footer="false" :brand-label="__('talenma.newsletter.email_brand')" content-max-width="728px">
    {!! $bodyHtml !!}
    <x-slot:footer>
        @include('emails.partials.newsletter-footer')
    </x-slot:footer>
</x-emails.layout>
