<?php

namespace App\Http\Controllers;

use App\Enums\StatutRecu;
use App\Http\Requests\StoreRecuRequest;
use App\Http\Requests\UpdateRecuRequest;
use App\Jobs\ExtraireDepensesDuRecu;
use App\Models\Recu;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RecuController extends Controller
{
    public function index(): View
    {
        $recus = auth()->user()->recus()
            ->withCount('depenses')
            ->when(request('search'), fn($q, $v) => $q->where('texte_brut', 'like', "%{$v}%"))
            ->when(request('status'), fn($q, $v) => $q->where('statut', $v))
            ->latest()
            ->paginate(15)
            ->appends(['search' => request('search'), 'status' => request('status')]);

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

        ExtraireDepensesDuRecu::dispatch($recu->id);

        return to_route('recus.index')
            ->with('success', 'Reçu créé avec succès. L\'extraction est en cours.');
    }

    public function show(Recu $recu): View
    {
        $recu = auth()->user()->recus()
            ->with('depenses')
            ->findOrFail($recu->id);

        $total = $recu->depenses->sum(fn($d) => $d->quantite * $d->prix_unitaire);

        return view('recus.show', compact('recu', 'total'));
    }

    public function edit(Recu $recu): View
    {
        $recu = auth()->user()->recus()->findOrFail($recu->id);

        return view('recus.edit', compact('recu'));
    }

    public function update(UpdateRecuRequest $request, Recu $recu): RedirectResponse
    {
        $recu = auth()->user()->recus()->findOrFail($recu->id);

        $recu->update([
            'texte_brut' => $request->validated()['texte_brut'],
            'statut' => StatutRecu::en_attente,
            'payload_ia' => null,
            'message_erreur' => null,
        ]);

        ExtraireDepensesDuRecu::dispatch($recu->id);

        return to_route('recus.show', $recu)
            ->with('success', 'Reçu mis à jour. L\'extraction va être relancée.');
    }

    public function destroy(Recu $recu): RedirectResponse
    {
        $recu = auth()->user()->recus()->findOrFail($recu->id);

        $recu->delete();

        return to_route('recus.index')
            ->with('success', 'Reçu supprimé.');
    }

    public function retry(Recu $recu): RedirectResponse
    {
        $recu = auth()->user()->recus()->findOrFail($recu->id);

        if ($recu->statut !== StatutRecu::echoue) {
            return back()->with('error', 'Ce reçu n\'est pas en échec.');
        }

        $recu->depenses()->delete();
        $recu->update([
            'statut' => StatutRecu::en_attente,
            'payload_ia' => null,
            'message_erreur' => null,
        ]);

        ExtraireDepensesDuRecu::dispatch($recu->id);

        return to_route('recus.show', $recu)
            ->with('success', 'L\'extraction va être relancée.');
    }
}
