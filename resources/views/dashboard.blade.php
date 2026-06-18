<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500">Total du mois</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($monthlyTotal, 2) }} €</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500">Dépenses ce mois</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $categoryBreakdown->count() }}</p>
                        <p class="text-xs text-gray-500">catégories</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500">Dernières dépenses</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $recentExpenses->count() }}</p>
                        <p class="text-xs text-gray-500">sur les 5 dernières</p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500">Reçus en attente</h3>
                        <p class="mt-2 text-3xl font-bold {{ $pendingCount > 0 ? 'text-yellow-600' : 'text-gray-900' }}">
                            {{ $pendingCount }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg mb-4">Répartition par catégorie</h3>
                        @if ($categoryBreakdown->isNotEmpty())
                            <div class="space-y-3">
                                @foreach ($categoryBreakdown as $label => $total)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-700">{{ $label }}</span>
                                        <span class="text-sm font-medium">{{ number_format($total, 2) }} €</span>
                                    </div>
                                    @if (!$loop->last)
                                        <hr class="border-gray-100">
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Aucune dépense ce mois-ci.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg mb-4">Dernières dépenses</h3>
                        @if ($recentExpenses->isNotEmpty())
                            <div class="space-y-3">
                                @foreach ($recentExpenses as $depense)
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-700">{{ $depense->libelle }}</p>
                                            <p class="text-xs text-gray-500">{{ $depense->created_at->format('d/m/Y') }}</p>
                                        </div>
                                        <span class="text-sm font-medium">{{ number_format($depense->quantite * $depense->prix_unitaire, 2) }} €</span>
                                    </div>
                                    @if (!$loop->last)
                                        <hr class="border-gray-100">
                                    @endif
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('depenses.index') }}"
                                   class="text-sm text-blue-600 hover:underline">Voir toutes les dépenses &rarr;</a>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Aucune dépense pour le moment.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
