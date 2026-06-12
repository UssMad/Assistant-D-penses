<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepenseRequest;
use App\Http\Requests\UpdateDepenseRequest;
use App\Models\Depenses;
use App\Models\Recu;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepensesController extends Controller
{
    public function index(): View
    {
        $category = request('categorie');
        $query = Depenses::whereHas('recu', function ($q) {
            $q->where('user_id', auth()->id());
        })->with('recu');

        if ($category && in_array($category, array_column(\App\Enums\CategorieDepense::cases(), 'value'))) {
            $query->where('categorie', $category);
        }

        $depenses = $query->latest()->paginate(15);

        return view('depenses.index', compact('depenses', 'category'));
    }

    public function create(): View
    {
        $recus = auth()->user()->recus()->latest()->get();

        return view('depenses.create', compact('recus'));
    }

    public function store(StoreDepenseRequest $request): RedirectResponse
    {
        $depense = Depenses::create($request->validated());

        return to_route('depenses.index')
            ->with('success', 'Dépense créée avec succès.');
    }

    public function show(Depenses $depense): View
    {
        $depense = Depenses::whereHas('recu', function ($q) {
            $q->where('user_id', auth()->id());
        })->with('recu')->findOrFail($depense->id);

        return view('depenses.show', compact('depense'));
    }

    public function edit(Depenses $depense): View
    {
        $depense = Depenses::whereHas('recu', function ($q) {
            $q->where('user_id', auth()->id());
        })->with('recu')->findOrFail($depense->id);

        $recus = auth()->user()->recus()->latest()->get();

        return view('depenses.edit', compact('depense', 'recus'));
    }

    public function update(UpdateDepenseRequest $request, Depenses $depense): RedirectResponse
    {
        $depense = Depenses::whereHas('recu', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($depense->id);

        $depense->update($request->validated());

        return to_route('depenses.index')
            ->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroy(Depenses $depense): RedirectResponse
    {
        $depense = Depenses::whereHas('recu', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($depense->id);

        $depense->delete();

        return redirect()->back()
            ->with('success', 'Dépense supprimée.');
    }
}
