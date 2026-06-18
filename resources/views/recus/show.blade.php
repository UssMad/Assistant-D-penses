<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détail du Reçu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-gray-500">Créé le {{ $recu->created_at->format('d/m/Y H:i') }}</span>
                        <a href="{{ route('recus.edit', $recu) }}"
                           class="text-blue-600 hover:underline text-sm">Modifier le texte</a>
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
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg">Dépenses</h3>
                            <button onclick="document.getElementById('inline-expense-form').classList.toggle('hidden')"
                                    class="inline-flex items-center px-3 py-1 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                + Ajouter
                            </button>
                        </div>

                        <div id="inline-expense-form" class="hidden">
                            @include('depenses._form_inline', ['recu' => $recu])
                        </div>

                        <table class="w-full text-sm text-left">
                            <thead class="border-b">
                                <tr>
                                    <th class="px-4 py-2">Libellé</th>
                                    <th class="px-4 py-2">Qté</th>
                                    <th class="px-4 py-2">Prix unitaire</th>
                                    <th class="px-4 py-2">Sous-total</th>
                                    <th class="px-4 py-2">Catégorie</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recu->depenses as $depense)
                                    <tr class="border-b">
                                        <td class="px-4 py-2">{{ $depense->libelle }}</td>
                                        <td class="px-4 py-2">{{ $depense->quantite }}</td>
                                        <td class="px-4 py-2">{{ number_format($depense->prix_unitaire, 2) }} €</td>
                                        <td class="px-4 py-2">{{ number_format($depense->quantite * $depense->prix_unitaire, 2) }} €</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $depense->categorie->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 space-x-2">
                                            <a href="{{ route('depenses.edit', $depense) }}"
                                               class="text-blue-600 hover:underline">Modifier</a>
                                            <form action="{{ route('depenses.destroy', $depense) }}"
                                                  method="POST" class="inline"
                                                  onsubmit="return confirm('Supprimer cette dépense ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                            Aucune dépense trouvée.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if ($recu->depenses->isNotEmpty())
                                <tfoot>
                                    <tr class="font-semibold border-t-2">
                                        <td colspan="4" class="px-4 py-2 text-right">Total</td>
                                        <td class="px-4 py-2">{{ number_format($total, 2) }} €</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                        @if ($recu->depenses->isEmpty() && $recu->statut->value === 'traite')
                            <p class="text-sm text-gray-500 mt-2">—</p>
                        @endif
                    @elseif ($recu->statut->value === 'echoue')
                        <div class="p-4 bg-red-50 text-red-700 rounded">
                            <strong>Erreur :</strong> {{ $recu->message_erreur ?? 'Erreur inconnue lors du traitement.' }}
                        </div>
                        <div class="mt-4">
                            <form action="{{ route('recus.retry', $recu) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                    Réessayer l'extraction
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-4 bg-yellow-50 text-yellow-700 rounded">
                            <p>En attente de traitement...</p>
                            <a href="{{ url()->current() }}"
                               class="mt-2 inline-flex items-center px-3 py-1 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500">
                                Actualiser
                            </a>
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
