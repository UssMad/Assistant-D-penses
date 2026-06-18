<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Résumé par catégorie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('depenses.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        &larr; Liste des dépenses
                    </a>
                </div>

                <form method="GET" action="{{ route('depenses.summary') }}" class="flex items-center gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <span class="text-sm text-gray-500">au</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        Filtrer
                    </button>
                    @if (request('date_from') || request('date_to'))
                        <a href="{{ route('depenses.summary') }}" class="text-sm text-gray-600 hover:underline">Effacer</a>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($categories->isNotEmpty())
                        <table class="w-full text-sm text-left">
                            <thead class="border-b">
                                <tr>
                                    <th class="px-4 py-2">Catégorie</th>
                                    <th class="px-4 py-2">Nombre</th>
                                    <th class="px-4 py-2">Total</th>
                                    <th class="px-4 py-2">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $cat)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium">{{ $cat->label }}</td>
                                        <td class="px-4 py-2">{{ $cat->count }}</td>
                                        <td class="px-4 py-2">{{ number_format($cat->total, 2) }} €</td>
                                        <td class="px-4 py-2">{{ $cat->percentage }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-semibold border-t-2">
                                    <td class="px-4 py-2">Total général</td>
                                    <td class="px-4 py-2">{{ $categories->sum('count') }}</td>
                                    <td class="px-4 py-2">{{ number_format($totalGeneral, 2) }} €</td>
                                    <td class="px-4 py-2">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <p class="text-center text-gray-500 py-8">Aucune dépense pour cette période.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
