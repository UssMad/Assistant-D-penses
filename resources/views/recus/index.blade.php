<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Reçus') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('recus.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    + Nouveau Reçu
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="w-full text-sm text-left">
                        <thead class="border-b">
                            <tr>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Statut</th>
                                <th class="px-4 py-2">Dépenses</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recus as $recu)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $recu->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-2">
                                        @php
                                            $badge = match ($recu->statut->value) {
                                                'en_attente' => 'bg-yellow-100 text-yellow-800',
                                                'traite' => 'bg-green-100 text-green-800',
                                                'echoue' => 'bg-red-100 text-red-800',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 rounded text-xs font-medium {{ $badge }}">
                                            {{ $recu->statut->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">{{ $recu->depenses_count }}</td>
                                    <td class="px-4 py-2 space-x-2">
                                        <a href="{{ route('recus.show', $recu) }}"
                                           class="text-blue-600 hover:underline">Voir</a>
                                        <form action="{{ route('recus.destroy', $recu) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Supprimer ce reçu ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Aucun reçu pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $recus->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
