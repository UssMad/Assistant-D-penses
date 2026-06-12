<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détail de la Dépense') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Libellé</dt>
                            <dd class="mt-1 text-lg">{{ $depense->libelle }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Quantité</dt>
                            <dd class="mt-1 text-lg">{{ $depense->quantite }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Prix unitaire</dt>
                            <dd class="mt-1 text-lg">{{ number_format($depense->prix_unitaire, 2) }} €</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total</dt>
                            <dd class="mt-1 text-lg font-semibold">{{ number_format($depense->quantite * $depense->prix_unitaire, 2) }} €</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 rounded text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $depense->categorie->label() }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reçu associé</dt>
                            <dd class="mt-1">
                                <a href="{{ route('recus.show', $depense->recu) }}"
                                   class="text-blue-600 hover:underline">
                                    Voir le reçu
                                </a>
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-6">
                        <a href="{{ route('depenses.index') }}"
                           class="text-sm text-gray-600 hover:underline">&larr; Retour à la liste</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
