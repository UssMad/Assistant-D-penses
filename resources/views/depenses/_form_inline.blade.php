<form method="POST" action="{{ route('recus.depenses.store', $recu) }}"
      class="mb-4 p-4 bg-gray-50 rounded-lg border">
    @csrf

    <h4 class="font-semibold text-sm mb-3">Ajouter une dépense</h4>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <input type="text" name="libelle" placeholder="Libellé"
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                   value="{{ old('libelle') }}">
        </div>
        <div>
            <input type="number" name="quantite" placeholder="Qté" min="1" value="1"
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>
        <div>
            <input type="number" step="0.01" name="prix_unitaire" placeholder="Prix unitaire" min="0"
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>
        <div>
            <select name="categorie"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @foreach (\App\Enums\CategorieDepense::cases() as $cat)
                    <option value="{{ $cat->value }}">{{ $cat->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit"
                    class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Ajouter
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="mt-2 text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <input type="hidden" name="recu_id" value="{{ $recu->id }}">
</form>
