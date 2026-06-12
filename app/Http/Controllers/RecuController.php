<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecuRequest;
use App\Models\Recu;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RecuController extends Controller
{
    public function index(): View
    {
        $recus = auth()->user()->recus()
            ->withCount('depenses')
            ->latest()
            ->paginate(15);

        return view('recus.index', compact('recus'));
    }

    public function create(): View
    {
        return view('recus.create');
    }

    public function store(StoreRecuRequest $request): RedirectResponse
    {
        $recu = auth()->user()->recus()->create([
            'texte_brut' => $request->validated()['texte_brut'],
            'statut' => 'en_attente',
        ]);

        return to_route('recus.index')
            ->with('success', 'Reçu créé avec succès.');
    }

    public function show(Recu $recu): View
    {
        $recu = auth()->user()->recus()
            ->with('depenses')
            ->findOrFail($recu->id);

        return view('recus.show', compact('recu'));
    }

    public function destroy(Recu $recu): RedirectResponse
    {
        $recu = auth()->user()->recus()->findOrFail($recu->id);

        $recu->delete();

        return to_route('recus.index')
            ->with('success', 'Reçu supprimé.');
    }
}
