<?php

namespace App\Http\Controllers;

use App\Models\Chantier;
use App\Models\Client;
use App\Models\Prestataire;
use App\Services\NumeroService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ChantierController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Chantier::with('client')->withCount('prestataires');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                    ->orWhere('numero', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($c) => $c->where('nom_complet', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        return Inertia::render('Chantiers/Index', [
            'chantiers' => $query->latest()->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'statut']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Chantiers/Form', [
            'clients' => Client::orderBy('nom_complet')->get(['id', 'nom_complet']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'required|in:en_attente,en_cours,termine,suspendu,annule',
            'notes' => 'nullable|string',
        ]);

        $validated['numero'] = NumeroService::genererChantier();

        $chantier = Chantier::create($validated);

        return redirect()->route('chantiers.show', $chantier)->with('success', 'Chantier créé avec succès.');
    }

    public function show(Chantier $chantier): Response
    {
        $chantier->load([
            'client',
            'prestataires',
            'devis' => fn ($q) => $q->latest(),
            'contrat',
            'paiements' => fn ($q) => $q->with('client', 'prestataire')->latest(),
            'depenses' => fn ($q) => $q->latest(),
        ]);

        return Inertia::render('Chantiers/Show', [
            'chantier' => $chantier,
            'prestatairesDisponibles' => Prestataire::orderBy('nom_complet')->get(['id', 'nom_complet', 'specialite']),
            'margeEstimee' => $chantier->margeEstimee(),
            'totalRecettes' => $chantier->paiements()->where('type', 'recu_client')->sum('montant'),
            'totalDepenses' => $chantier->depenses()->sum('montant'),
            'totalPrestataires' => $chantier->prestataires()->sum('chantier_prestataire.montant_paye'),
        ]);
    }

    public function edit(Chantier $chantier): Response
    {
        return Inertia::render('Chantiers/Form', [
            'chantier' => $chantier,
            'clients' => Client::orderBy('nom_complet')->get(['id', 'nom_complet']),
        ]);
    }

    public function update(Request $request, Chantier $chantier): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'localisation' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'required|in:en_attente,en_cours,termine,suspendu,annule',
            'notes' => 'nullable|string',
        ]);

        $chantier->update($validated);

        return redirect()->route('chantiers.show', $chantier)->with('success', 'Chantier modifié.');
    }

    public function destroy(Chantier $chantier): RedirectResponse
    {
        $chantier->delete();

        return redirect()->route('chantiers.index')->with('success', 'Chantier supprimé.');
    }

    /**
     * Affecter un prestataire à un chantier
     */
    public function affecterPrestataire(Request $request, Chantier $chantier): RedirectResponse
    {
        $validated = $request->validate([
            'prestataire_id' => 'required|exists:prestataires,id',
            'role' => 'nullable|string|max:255',
            'montant_convenu' => 'required|numeric|min:0',
            'montant_paye' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $chantier->prestataires()->attach($validated['prestataire_id'], [
            'role' => $validated['role'],
            'montant_convenu' => $validated['montant_convenu'],
            'montant_paye' => $validated['montant_paye'],
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Prestataire affecté au chantier.');
    }

    /**
     * Mettre à jour l'affectation d'un prestataire
     */
    public function updatePrestataire(Request $request, Chantier $chantier, int $pivotId): RedirectResponse
    {
        $validated = $request->validate([
            'role' => 'nullable|string|max:255',
            'montant_convenu' => 'required|numeric|min:0',
            'montant_paye' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        \DB::table('chantier_prestataire')
            ->where('id', $pivotId)
            ->where('chantier_id', $chantier->id)
            ->update($validated);

        return back()->with('success', 'Affectation mise à jour.');
    }

    /**
     * Retirer un prestataire d'un chantier
     */
    public function retirerPrestataire(Request $request, Chantier $chantier): RedirectResponse
    {
        $chantier->prestataires()->detach($request->prestataire_id);

        return back()->with('success', 'Prestataire retiré du chantier.');
    }
}
