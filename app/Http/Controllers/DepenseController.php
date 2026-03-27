<?php

namespace App\Http\Controllers;

use App\Models\Chantier;
use App\Models\Depense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DepenseController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Depense::with('chantier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('titre', 'like', "%{$search}%");
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('chantier_id')) {
            $query->where('chantier_id', $request->chantier_id);
        }

        return Inertia::render('Depenses/Index', [
            'depenses' => $query->latest('date')->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'categorie', 'chantier_id']),
            'totalDepenses' => Depense::sum('montant'),
            'chantiers' => Chantier::orderBy('titre')->get(['id', 'titre', 'numero']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Depenses/Form', [
            'chantiers' => Chantier::orderBy('titre')->get(['id', 'titre', 'numero']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant' => 'required|numeric|min:0',
            'date' => 'required|date',
            'categorie' => 'required|in:materiaux,transport,main_oeuvre,imprevus,location_outils,autres',
            'chantier_id' => 'nullable|exists:chantiers,id',
        ]);

        Depense::create($validated);

        return redirect()->route('depenses.index')->with('success', 'Dépense enregistrée.');
    }

    public function edit(Depense $depense): Response
    {
        return Inertia::render('Depenses/Form', [
            'depense' => $depense,
            'chantiers' => Chantier::orderBy('titre')->get(['id', 'titre', 'numero']),
        ]);
    }

    public function update(Request $request, Depense $depense): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'montant' => 'required|numeric|min:0',
            'date' => 'required|date',
            'categorie' => 'required|in:materiaux,transport,main_oeuvre,imprevus,location_outils,autres',
            'chantier_id' => 'nullable|exists:chantiers,id',
        ]);

        $depense->update($validated);

        return redirect()->route('depenses.index')->with('success', 'Dépense modifiée.');
    }

    public function destroy(Depense $depense): RedirectResponse
    {
        $depense->delete();

        return redirect()->route('depenses.index')->with('success', 'Dépense supprimée.');
    }
}
