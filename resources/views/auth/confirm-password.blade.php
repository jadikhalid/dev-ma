<x-guest-layout>
    <x-slot name="title">Confirmation requise</x-slot>
    <x-slot name="description">Zone sécurisée — confirmez votre identité</x-slot>

    <p class="mb-4 text-sm text-gray-600">
        Il s'agit d'une zone sécurisée. Veuillez confirmer votre mot de passe avant de continuer.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center">Confirmer</x-primary-button>
        </div>
    </form>
</x-guest-layout>
