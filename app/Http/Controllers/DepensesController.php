<?php

namespace App\Http\Controllers;

use App\Enums\CategorieDepense;
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
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        $query = Depenses::whereHas('recu', function ($q) {
            $q->where('user_id', auth()->id());
        })->with('recu');

        if ($category && in_array($category, array_column(\App\Enums\CategorieDepense::cases(), 'value'))) {
            $query->where('categorie', $category);
        }

        $query->when($dateFrom, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($dateTo, fn($q, $v) => $q->whereDate('created_at', '<=', $v));

        $depenses = $query->latest()->paginate(15)
            ->appends(['categorie' => $category, 'date_from' => $dateFrom, 'date_to' => $dateTo]);

        return view('depenses.index', compact('depenses', 'category', 'dateFrom', 'dateTo'));
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

    public function summary(): View
    {
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $query = Depenses::whereHas('recu', fn($q) => $q->where('user_id', auth()->id()));

        $query->when($dateFrom, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($dateTo, fn($q, $v) => $q->whereDate('created_at', '<=', $v));

        $expenses = $query->get();
        $totalGeneral = $expenses->sum(fn($d) => $d->quantite * $d->prix_unitaire);

        $categories = collect(CategorieDepense::cases())->map(function ($cat) use ($expenses, $totalGeneral) {
            $items = $expenses->filter(fn($d) => $d->categorie->value === $cat->value);
            $total = $items->sum(fn($d) => $d->quantite * $d->prix_unitaire);
            $count = $items->count();

            return (object) [
                'label' => $cat->label(),
                'count' => $count,
                'total' => $total,
                'percentage' => $totalGeneral > 0 ? round($total / $totalGeneral * 100, 1) : 0,
            ];
        })->filter(fn($c) => $c->count > 0)->sortByDesc('total');

        return view('depenses.summary', compact('categories', 'totalGeneral', 'dateFrom', 'dateTo'));
    }

    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $category = request('categorie');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $query = Depenses::whereHas('recu', fn($q) => $q->where('user_id', auth()->id()))->with('recu');

        if ($category && in_array($category, array_column(CategorieDepense::cases(), 'value'))) {
            $query->where('categorie', $category);
        }

        $query->when($dateFrom, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($dateTo, fn($q, $v) => $q->whereDate('created_at', '<=', $v));

        $expenses = $query->latest()->get();

        $filename = 'depenses_export_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($expenses) {
            $handle = fopen('php://output', 'w+');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Libellé', 'Quantité', 'Prix unitaire', 'Total', 'Catégorie', 'Date du reçu']);

            foreach ($expenses as $depense) {
                fputcsv($handle, [
                    $depense->libelle,
                    $depense->quantite,
                    number_format($depense->prix_unitaire, 2),
                    number_format($depense->quantite * $depense->prix_unitaire, 2),
                    $depense->categorie->label(),
                    $depense->recu->created_at->format('d/m/Y'),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
