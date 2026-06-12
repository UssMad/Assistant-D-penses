<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détail du Reçu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <span class="text-sm text-gray-500">Créé le {{ $recu->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="mb-4">
                        @php
                            $badge = match ($recu->statut->value) {
                                'en_attente' => 'bg-yellow-100 text-yellow-800',
                                'traite' => 'bg-green-100 text-green-800',
                                'echoue' => 'bg-red-100 text-red-800',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded text-sm font-medium {{ $badge }}">
                            {{ $recu->statut->label() }}
                        </span>
                    </div>

                    <div class="mb-6 p-4 bg-gray-50 rounded">
                        <pre class="whitespace-pre-wrap text-sm">{{ $recu->texte_brut }}</pre>
                    </div>

                    @if ($recu->statut->value === 'traite')
                        <h3 class="font-semibold text-lg mb-4">Dépenses extraites</h3>
                        <table class="w-full text-sm text-left">
                            <thead class="border-b">
                                <tr>
                                    <th class="px-4 py-2">Libellé</th>
                                    <th class="px-4 py-2">Quantité</th>
                                    <th class="px-4 py-2">Prix unitaire</th>
                                    <th class="px-4 py-2">Catégorie</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recu->depenses as $depense)
                                    <tr class="border-b">
                                        <td class="px-4 py-2">{{ $depense->libelle }}</td>
                                        <td class="px-4 py-2">{{ $depense->quantite }}</td>
                                        <td class="px-4 py-2">{{ number_format($depense->prix_unitaire, 2) }} €</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $depense->categorie->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                            Aucune dépense trouvée.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @elseif ($recu->statut->value === 'echoue')
                        <div class="p-4 bg-red-50 text-red-700 rounded">
                            <strong>Erreur :</strong> {{ $recu->message_erreur ?? 'Erreur inconnue lors du traitement.' }}
                        </div>
                    @else
                        <div class="p-4 bg-yellow-50 text-yellow-700 rounded">
                            En attente de traitement...
                        </div>
                    @endif

                    <div class="mt-6">
                        <a href="{{ route('recus.index') }}"
                           class="text-sm text-gray-600 hover:underline">&larr; Retour à la liste</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
