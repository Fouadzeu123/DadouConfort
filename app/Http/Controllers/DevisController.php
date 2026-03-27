<?php

namespace App\Http\Controllers;

use App\Models\Chantier;
use App\Models\Client;
use App\Models\Contrat;
use App\Models\Devis;
use App\Models\DevisLigne;
use App\Models\Service;
use App\Services\NumeroService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DevisController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Devis::with('client', 'chantier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($c) => $c->where('nom_complet', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        return Inertia::render('Devis/Index', [
            'devis' => $query->latest()->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'statut']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Devis/Form', [
            'clients' => Client::orderBy('nom_complet')->get(['id', 'nom_complet']),
            'chantiers' => Chantier::with('client')->orderBy('titre')->get(['id', 'titre', 'client_id', 'numero']),
            'services' => Service::orderBy('nom')->get(),
            'nextNumero' => NumeroService::genererDevis(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'chantier_id' => 'nullable|exists:chantiers,id',
            'date' => 'required|date',
            'date_validite' => 'nullable|date',
            'remise' => 'nullable|numeric|min:0',
            'cout_transport' => 'nullable|numeric|min:0',
            'cout_main_oeuvre' => 'nullable|numeric|min:0',
            'acompte' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'statut' => 'required|in:brouillon,envoye,accepte,refuse,expire',
            'lignes' => 'required|array|min:1',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.description' => 'nullable|string',
            'lignes.*.quantite' => 'required|numeric|min:0',
            'lignes.*.unite' => 'nullable|string|max:50',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        $devis = Devis::create([
            'numero' => NumeroService::genererDevis(),
            'client_id' => $validated['client_id'],
            'chantier_id' => $validated['chantier_id'] ?? null,
            'date' => $validated['date'],
            'date_validite' => $validated['date_validite'] ?? null,
            'remise' => $validated['remise'] ?? 0,
            'cout_transport' => $validated['cout_transport'] ?? 0,
            'cout_main_oeuvre' => $validated['cout_main_oeuvre'] ?? 0,
            'acompte' => $validated['acompte'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'statut' => $validated['statut'],
        ]);

        foreach ($validated['lignes'] as $i => $ligne) {
            $devis->lignes()->create([
                'designation' => $ligne['designation'],
                'description' => $ligne['description'] ?? null,
                'quantite' => $ligne['quantite'],
                'unite' => $ligne['unite'] ?? 'u',
                'prix_unitaire' => $ligne['prix_unitaire'],
                'ordre' => $i,
            ]);
        }

        return redirect()->route('devis.show', $devis)->with('success', 'Devis créé avec succès.');
    }

    public function show(Devis $devi): Response
    {
        $devi->load(['client', 'chantier', 'lignes']);

        $sousTotal = $devi->lignes->sum(fn ($l) => $l->quantite * $l->prix_unitaire);
        $total = $sousTotal - $devi->remise + $devi->cout_transport + $devi->cout_main_oeuvre;

        return Inertia::render('Devis/Show', [
            'devis' => $devi,
            'sousTotal' => round((float) $sousTotal, 2),
            'total' => round((float) $total, 2),
            'resteAPayer' => round((float) ($total - $devi->acompte), 2),
        ]);
    }

    public function edit(Devis $devi): Response
    {
        $devi->load('lignes');

        return Inertia::render('Devis/Form', [
            'devis' => $devi,
            'clients' => Client::orderBy('nom_complet')->get(['id', 'nom_complet']),
            'chantiers' => Chantier::with('client')->orderBy('titre')->get(['id', 'titre', 'client_id', 'numero']),
            'services' => Service::orderBy('nom')->get(),
            'nextNumero' => $devi->numero,
        ]);
    }

    public function update(Request $request, Devis $devi): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'chantier_id' => 'nullable|exists:chantiers,id',
            'date' => 'required|date',
            'date_validite' => 'nullable|date',
            'remise' => 'nullable|numeric|min:0',
            'cout_transport' => 'nullable|numeric|min:0',
            'cout_main_oeuvre' => 'nullable|numeric|min:0',
            'acompte' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'statut' => 'required|in:brouillon,envoye,accepte,refuse,expire',
            'lignes' => 'required|array|min:1',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.description' => 'nullable|string',
            'lignes.*.quantite' => 'required|numeric|min:0',
            'lignes.*.unite' => 'nullable|string|max:50',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        $devi->update([
            'client_id' => $validated['client_id'],
            'chantier_id' => $validated['chantier_id'] ?? null,
            'date' => $validated['date'],
            'date_validite' => $validated['date_validite'] ?? null,
            'remise' => $validated['remise'] ?? 0,
            'cout_transport' => $validated['cout_transport'] ?? 0,
            'cout_main_oeuvre' => $validated['cout_main_oeuvre'] ?? 0,
            'acompte' => $validated['acompte'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'statut' => $validated['statut'],
        ]);

        $devi->lignes()->delete();
        foreach ($validated['lignes'] as $i => $ligne) {
            $devi->lignes()->create([
                'designation' => $ligne['designation'],
                'description' => $ligne['description'] ?? null,
                'quantite' => $ligne['quantite'],
                'unite' => $ligne['unite'] ?? 'u',
                'prix_unitaire' => $ligne['prix_unitaire'],
                'ordre' => $i,
            ]);
        }

        return redirect()->route('devis.show', $devi)->with('success', 'Devis mis à jour.');
    }

    public function destroy(Devis $devi): RedirectResponse
    {
        $devi->delete();

        return redirect()->route('devis.index')->with('success', 'Devis supprimé.');
    }

    /**
     * Dupliquer un devis
     */
    public function dupliquer(Devis $devi): RedirectResponse
    {
        $devi->load('lignes');

        $nouveau = $devi->replicate();
        $nouveau->numero = NumeroService::genererDevis();
        $nouveau->statut = 'brouillon';
        $nouveau->save();

        foreach ($devi->lignes as $ligne) {
            $nouvelle = $ligne->replicate();
            $nouvelle->devis_id = $nouveau->id;
            $nouvelle->save();
        }

        return redirect()->route('devis.show', $nouveau)->with('success', 'Devis dupliqué.');
    }

    /**
     * Convertir un devis accepté en contrat
     */
    public function convertirEnContrat(Devis $devi): RedirectResponse
    {
        $devi->load(['client', 'lignes', 'chantier']);

        $sousTotal = $devi->lignes->sum(fn ($l) => $l->quantite * $l->prix_unitaire);
        $total = $sousTotal - $devi->remise + $devi->cout_transport + $devi->cout_main_oeuvre;

        $contrat = Contrat::create([
            'numero' => NumeroService::genererContrat(),
            'client_id' => $devi->client_id,
            'chantier_id' => $devi->chantier_id,
            'devis_id' => $devi->id,
            'objet' => "Travaux - {$devi->client->nom_complet}",
            'montant_convenu' => $total,
            'date_debut' => now()->toDateString(),
            'statut' => 'brouillon',
        ]);

        return redirect()->route('contrats.show', $contrat)->with('success', 'Contrat créé depuis le devis.');
    }
}
