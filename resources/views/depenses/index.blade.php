<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dépenses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex items-center gap-4 flex-wrap">
                <div class="flex items-center gap-2">
                    <a href="{{ route('depenses.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        + Nouvelle Dépense
                    </a>
                    <a href="{{ route('depenses.summary') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                        Résumé par catégorie
                    </a>
                </div>

                <form method="GET" action="{{ route('depenses.index') }}" class="flex items-center gap-2 flex-wrap">
                    <select name="categorie"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Toutes les catégories</option>
                        @foreach (\App\Enums\CategorieDepense::cases() as $cat)
                            <option value="{{ $cat->value }}" {{ request('categorie') === $cat->value ? 'selected' : '' }}>
                                {{ $cat->label() }}
                            </option>
                        @endforeach
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <span class="text-sm text-gray-500">au</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Filtrer
                    </button>
                    @if (request('categorie') || request('date_from') || request('date_to'))
                        <a href="{{ route('depenses.index') }}" class="text-sm text-gray-600 hover:underline">Effacer</a>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-sm text-left">
                        <thead class="border-b">
                            <tr>
                                <th class="px-4 py-2">Libellé</th>
                                <th class="px-4 py-2">Qté</th>
                                <th class="px-4 py-2">Prix unitaire</th>
                                <th class="px-4 py-2">Total</th>
                                <th class="px-4 py-2">Catégorie</th>
                                <th class="px-4 py-2">Reçu</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($depenses as $depense)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $depense->libelle }}</td>
                                    <td class="px-4 py-2">{{ $depense->quantite }}</td>
                                    <td class="px-4 py-2">{{ number_format($depense->prix_unitaire, 2) }} €</td>
                                    <td class="px-4 py-2 font-medium">{{ number_format($depense->quantite * $depense->prix_unitaire, 2) }} €</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $catColors = [
                                                'alimentaire' => 'bg-green-100 text-green-800',
                                                'boissons' => 'bg-blue-100 text-blue-800',
                                                'hygiene' => 'bg-purple-100 text-purple-800',
                                                'entretien' => 'bg-orange-100 text-orange-800',
                                                'autre' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $color = $catColors[$depense->categorie->value] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $color }}">
                                            {{ $depense->categorie->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('recus.show', $depense->recu) }}"
                                           class="text-blue-600 hover:underline">
                                            Voir le reçu
                                        </a>
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
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        Aucune dépense pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            {{ $depenses->links() }}
                        </div>
                        @if ($depenses->total() > 0)
                            <a href="{{ route('depenses.export', request()->only(['categorie', 'date_from', 'date_to'])) }}"
                               class="inline-flex items-center px-3 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600">
                                Exporter en CSV
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
