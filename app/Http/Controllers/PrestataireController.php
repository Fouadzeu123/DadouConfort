<?php

namespace App\Http\Controllers;

use App\Models\Prestataire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PrestataireController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Prestataire::withCount('chantiers');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom_complet', 'like', "%{$search}%")
                    ->orWhere('specialite', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('disponibilite')) {
            $query->where('disponibilite', $request->disponibilite);
        }

        return Inertia::render('Prestataires/Index', [
            'prestataires' => $query->latest()->paginate(20)->withQueryString(),
            'filters' => $request->only(['search', 'disponibilite']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Prestataires/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom_complet' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:50',
            'specialite' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:500',
            'disponibilite' => 'required|in:disponible,indisponible,en_mission',
            'notes' => 'nullable|string',
            'tarif_indicatif' => 'nullable|string|max:255',
        ]);

        Prestataire::create($validated);

        return redirect()->route('prestataires.index')->with('success', 'Prestataire ajouté avec succès.');
    }

    public function show(Prestataire $prestataire): Response
    {
        $prestataire->load([
            'chantiers' => fn ($q) => $q->with('client')->orderBy('chantiers.created_at', 'desc')->limit(10),
            'paiements' => fn ($q) => $q->with('chantier')->latest()->limit(10),
        ]);

        $totalPaye = $prestataire->paiements()->sum('montant');
        $totalConvenu = \DB::table('chantier_prestataire')
            ->where('prestataire_id', $prestataire->id)
            ->sum('montant_convenu');
        $soldeDu = $totalConvenu - \DB::table('chantier_prestataire')
            ->where('prestataire_id', $prestataire->id)
            ->sum('montant_paye');

        return Inertia::render('Prestataires/Show', [
            'prestataire' => $prestataire,
            'totalPaye' => round((float) $totalPaye, 2),
            'totalConvenu' => round((float) $totalConvenu, 2),
            'soldeDu' => round((float) $soldeDu, 2),
        ]);
    }

    public function edit(Prestataire $prestataire): Response
    {
        return Inertia::render('Prestataires/Form', [
            'prestataire' => $prestataire,
        ]);
    }

    public function update(Request $request, Prestataire $prestataire): RedirectResponse
    {
        $validated = $request->validate([
            'nom_complet' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:50',
            'specialite' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:500',
            'disponibilite' => 'required|in:disponible,indisponible,en_mission',
            'notes' => 'nullable|string',
            'tarif_indicatif' => 'nullable|string|max:255',
        ]);

        $prestataire->update($validated);

        return redirect()->route('prestataires.show', $prestataire)->with('success', 'Prestataire modifié.');
    }

    public function destroy(Prestataire $prestataire): RedirectResponse
    {
        $prestataire->delete();

        return redirect()->route('prestataires.index')->with('success', 'Prestataire supprimé.');
    }
}
