<section class="space-y-6">
    <header>
        <h2 class="text-lg font-semibold text-red-700">Supprimer le compte</h2>
        <p class="mt-1 text-sm text-gray-600">
            Une fois votre compte supprimé, toutes vos données seront définitivement effacées. Cette action est irréversible.
        </p>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Supprimer mon compte
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-gray-900">
                Confirmer la suppression du compte
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                Entrez votre mot de passe pour confirmer la suppression définitive de votre compte et de toutes vos données.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Mot de passe" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" placeholder="Mot de passe" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                <x-danger-button>Supprimer définitivement</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
