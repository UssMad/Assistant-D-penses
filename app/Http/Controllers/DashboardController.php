<?php

namespace App\Http\Controllers;

use App\Models\Depenses;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $userId = auth()->id();

        $monthlyTotal = Depenses::whereHas('recu', fn($q) => $q->where('user_id', $userId))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get()
            ->sum(fn($d) => $d->quantite * $d->prix_unitaire);

        $categoryBreakdown = Depenses::whereHas('recu', fn($q) => $q->where('user_id', $userId))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get()
            ->groupBy(fn($d) => $d->categorie->label())
            ->map(fn($items) => $items->sum(fn($d) => $d->quantite * $d->prix_unitaire))
            ->sortDesc();

        $recentExpenses = Depenses::whereHas('recu', fn($q) => $q->where('user_id', $userId))
            ->with('recu')
            ->latest()
            ->take(5)
            ->get();

        $pendingCount = auth()->user()->recus()
            ->where('statut', 'en_attente')
            ->count();

        return view('dashboard', compact('monthlyTotal', 'categoryBreakdown', 'recentExpenses', 'pendingCount'));
    }
}
