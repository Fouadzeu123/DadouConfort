<?php

namespace App\Http\Controllers;

use App\Models\Chantier;
use App\Models\Client;
use App\Models\Contrat;
use App\Models\Devis;
use App\Services\ContractGeneratorService;
use App\Services\NumeroService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContratController extends Controller
{
    protected $contractService;

    public function __construct(ContractGeneratorService $contractService)
    {
        $this->contractService = $contractService;
    }

    public function index(Request $request): Response
    {
        $query = Contrat::with('client', 'chantier', 'prestataire');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                    ->orWhere('objet', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($c) => $c->where('nom_complet', 'like', "%{$search}%"))
                    ->orWhereHas('prestataire', fn ($p) => $p->where('nom_complet', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return Inertia::render('Contrats/Index', [
            'contrats' => $query->latest()->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'statut', 'type']),
        ]);
    }

    public function create(Request $request): Response
    {
        $type = $request->type ?? 'client';
        $chantierId = $request->chantier_id;

        return Inertia::render('Contrats/Form', [
            'clients' => Client::orderBy('nom_complet')->get(['id', 'nom_complet']),
            'prestataires' => \App\Models\Prestataire::orderBy('nom_complet')->get(['id', 'nom_complet']),
            'chantiers' => Chantier::with('client')->orderBy('titre')->get(['id', 'titre', 'client_id', 'numero']),
            'devis' => Devis::with('client')->where('statut', 'accepte')->orderBy('numero')->get(['id', 'numero', 'client_id']),
            'nextNumero' => NumeroService::genererContrat($type),
            'initialType' => $type,
            'initialChantierId' => $chantierId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:client,prestataire',
            'client_id' => 'required_if:type,client|nullable|exists:clients,id',
            'prestataire_id' => 'required_if:type,prestataire|nullable|exists:prestataires,id',
            'chantier_id' => 'nullable|exists:chantiers,id',
            'devis_id' => 'nullable|exists:devis,id',
            'objet' => 'required|string|max:255',
            'description_travaux' => 'nullable|string',
            'montant_convenu' => 'required|numeric|min:0',
            'acompte' => 'nullable|numeric|min:0',
            'date_signature' => 'nullable|date',
            'date_debut' => 'nullable|date',
            'date_fin_estimee' => 'nullable|date',
            'conditions_paiement' => 'nullable|string',
            'clauses' => 'nullable|string',
            'statut' => 'required|in:brouillon,actif,termine,resilie',
        ]);

        $validated['numero'] = NumeroService::genererContrat($validated['type']);

        $contrat = Contrat::create($validated);

        return redirect()->route('contrats.show', $contrat)->with('success', 'Contrat créé avec succès.');
    }

    public function show(Contrat $contrat): Response
    {
        $contrat->load(['client', 'prestataire', 'chantier', 'devis', 'paiements' => fn ($q) => $q->latest()]);

        $totalPaye = $contrat->paiements()->where('type', 'recu_client')->sum('montant');
        $solde = $contrat->montant_convenu - $totalPaye;

        return Inertia::render('Contrats/Show', [
            'contrat' => $contrat,
            'totalPaye' => round((float) $totalPaye, 2),
            'solde' => round((float) $solde, 2),
            'generatedContent' => $this->contractService->generate($contrat),
            'contractTitle' => $this->contractService->getTitle($contrat->type),
        ]);
    }

    public function edit(Contrat $contrat): Response
    {
        return Inertia::render('Contrats/Form', [
            'contrat' => $contrat,
            'clients' => Client::orderBy('nom_complet')->get(['id', 'nom_complet']),
            'prestataires' => \App\Models\Prestataire::orderBy('nom_complet')->get(['id', 'nom_complet']),
            'chantiers' => Chantier::with('client')->orderBy('titre')->get(['id', 'titre', 'client_id', 'numero']),
            'devis' => Devis::with('client')->where('statut', 'accepte')->orderBy('numero')->get(['id', 'numero', 'client_id']),
            'nextNumero' => $contrat->numero,
            'initialType' => $contrat->type,
        ]);
    }

    public function update(Request $request, Contrat $contrat): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:client,prestataire',
            'client_id' => 'required_if:type,client|nullable|exists:clients,id',
            'prestataire_id' => 'required_if:type,prestataire|nullable|exists:prestataires,id',
            'chantier_id' => 'nullable|exists:chantiers,id',
            'devis_id' => 'nullable|exists:devis,id',
            'objet' => 'required|string|max:255',
            'description_travaux' => 'nullable|string',
            'montant_convenu' => 'required|numeric|min:0',
            'acompte' => 'nullable|numeric|min:0',
            'date_signature' => 'nullable|date',
            'date_debut' => 'nullable|date',
            'date_fin_estimee' => 'nullable|date',
            'conditions_paiement' => 'nullable|string',
            'clauses' => 'nullable|string',
            'statut' => 'required|in:brouillon,actif,termine,resilie',
        ]);

        $contrat->update($validated);

        return redirect()->route('contrats.show', $contrat)->with('success', 'Contrat modifié.');
    }

    public function destroy(Contrat $contrat): RedirectResponse
    {
        $contrat->delete();

        return redirect()->route('contrats.index')->with('success', 'Contrat supprimé.');
    }

    public function dupliquer(Contrat $contrat): RedirectResponse
    {
        $nouveau = $contrat->replicate();
        $nouveau->numero = NumeroService::genererContrat($contrat->type);
        $nouveau->statut = 'brouillon';
        $nouveau->date_signature = null;
        $nouveau->save();

        return redirect()->route('contrats.show', $nouveau)->with('success', 'Contrat dupliqué.');
    }
}
