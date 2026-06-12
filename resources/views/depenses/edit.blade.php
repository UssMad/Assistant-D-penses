<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier la Dépense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('depenses.update', $depense) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Reçu</label>
                            <p class="mt-1 text-sm text-gray-500">
                                Reçu du {{ $depense->recu->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <label for="libelle" class="block text-sm font-medium text-gray-700">Libellé</label>
                            <input type="text" name="libelle" id="libelle" value="{{ old('libelle', $depense->libelle) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('libelle')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="quantite" class="block text-sm font-medium text-gray-700">Quantité</label>
                                <input type="number" name="quantite" id="quantite" value="{{ old('quantite', $depense->quantite) }}" min="1"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('quantite')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="prix_unitaire" class="block text-sm font-medium text-gray-700">Prix unitaire (€)</label>
                                <input type="number" step="0.01" name="prix_unitaire" id="prix_unitaire" value="{{ old('prix_unitaire', $depense->prix_unitaire) }}" min="0"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('prix_unitaire')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                            <select name="categorie" id="categorie"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach (\App\Enums\CategorieDepense::cases() as $cat)
                                    <option value="{{ $cat->value }}" {{ old('categorie', $depense->categorie->value) === $cat->value ? 'selected' : '' }}>
                                        {{ $cat->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categorie')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Mettre à jour
                            </button>
                            <a href="{{ route('depenses.index') }}"
                               class="text-sm text-gray-600 hover:underline">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
