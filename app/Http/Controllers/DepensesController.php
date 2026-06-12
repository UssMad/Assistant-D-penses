<?php

namespace App\Http\Controllers;

use App\Models\Depenses;
use Illuminate\View\View;

class DepensesController extends Controller
{
    public function index(): View
    {
        $depenses = Depenses::whereHas('recu', function ($q) {
            $q->where('user_id', auth()->id());
        })->with('recu')->latest()->paginate(15);

        return view('depenses.index', compact('depenses'));
    }

    public function show(Depenses $depense): View
    {
        $depense = Depenses::whereHas('recu', function ($q) {
            $q->where('user_id', auth()->id());
        })->with('recu')->findOrFail($depense->id);

        return view('depenses.show', compact('depense'));
    }
}
