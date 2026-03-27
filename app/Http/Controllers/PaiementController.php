<?php

namespace App\Http\Controllers;

use App\Models\Chantier;
use App\Models\Client;
use App\Models\Contrat;
use App\Models\Devis;
use App\Models\Paiement;
use App\Models\Prestataire;
use App\Services\NumeroService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaiementController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Paiement::with('client', 'prestataire', 'chantier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('chantier_id')) {
            $query->where('chantier_id', $request->chantier_id);
        }

        return Inertia::render('Paiements/Index', [
            'paiements' => $query->latest('date')->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'type', 'chantier_id']),
            'totalRecus' => Paiement::where('type', 'recu_client')->sum('montant'),
            'totalVerses' => Paiement::where('type', 'verse_prestataire')->sum('montant'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Paiements/Form', [
            'clients' => Client::orderBy('nom_complet')->get(['id', 'nom_complet']),
            'prestataires' => Prestataire::orderBy('nom_complet')->get(['id', 'nom_complet']),
            'chantiers' => Chantier::orderBy('titre')->get(['id', 'titre', 'numero']),
            'nextNumero' => NumeroService::genererPaiement(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:recu_client,verse_prestataire',
            'montant' => 'required|numeric|min:0',
            'date' => 'required|date',
            'mode_paiement' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'prestataire_id' => 'nullable|exists:prestataires,id',
            'chantier_id' => 'nullable|exists:chantiers,id',
            'devis_id' => 'nullable|exists:devis,id',
            'contrat_id' => 'nullable|exists:contrats,id',
        ]);

        $validated['numero'] = NumeroService::genererPaiement();

        Paiement::create($validated);

        return redirect()->route('paiements.index')->with('success', 'Paiement enregistré.');
    }

    public function show(Paiement $paiement): Response
    {
        $paiement->load(['client', 'prestataire', 'chantier', 'devis', 'contrat']);

        return Inertia::render('Paiements/Show', [
            'paiement' => $paiement,
        ]);
    }

    public function edit(Paiement $paiement): Response
    {
        return Inertia::render('Paiements/Form', [
            'paiement' => $paiement,
            'clients' => Client::orderBy('nom_complet')->get(['id', 'nom_complet']),
            'prestataires' => Prestataire::orderBy('nom_complet')->get(['id', 'nom_complet']),
            'chantiers' => Chantier::orderBy('titre')->get(['id', 'titre', 'numero']),
            'nextNumero' => $paiement->numero,
        ]);
    }

    public function update(Request $request, Paiement $paiement): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:recu_client,verse_prestataire',
            'montant' => 'required|numeric|min:0',
            'date' => 'required|date',
            'mode_paiement' => 'nullable|string|max:100',
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'prestataire_id' => 'nullable|exists:prestataires,id',
            'chantier_id' => 'nullable|exists:chantiers,id',
            'devis_id' => 'nullable|exists:devis,id',
            'contrat_id' => 'nullable|exists:contrats,id',
        ]);

        $paiement->update($validated);

        return redirect()->route('paiements.show', $paiement)->with('success', 'Paiement modifié.');
    }

    public function destroy(Paiement $paiement): RedirectResponse
    {
        $paiement->delete();

        return redirect()->route('paiements.index')->with('success', 'Paiement supprimé.');
    }
}
