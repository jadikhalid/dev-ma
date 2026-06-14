<x-guest-layout>
    <x-slot name="title">Vérification de l'e-mail</x-slot>
    <x-slot name="description">Confirmez votre adresse e-mail pour accéder à la plateforme</x-slot>

    <p class="mb-4 text-sm text-gray-600">
        Merci pour votre inscription ! Avant de commencer, veuillez vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer. Si vous n'avez pas reçu l'e-mail, nous pouvons vous en renvoyer un.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-sm font-medium text-green-700">
            Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
        </div>
    @endif

    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>Renvoyer l'e-mail de vérification</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                Déconnexion
            </button>
        </form>
    </div>
</x-guest-layout>
