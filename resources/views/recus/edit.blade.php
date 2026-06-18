<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le Reçu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('recus.update', $recu) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="texte_brut" class="block text-sm font-medium text-gray-700">
                                Texte du reçu
                            </label>
                            <textarea name="texte_brut" id="texte_brut" rows="8"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Collez le texte du reçu ici...">{{ old('texte_brut', $recu->texte_brut) }}</textarea>
                            @error('texte_brut')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Enregistrer
                            </button>
                            <a href="{{ route('recus.show', $recu) }}"
                               class="text-sm text-gray-600 hover:underline">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
