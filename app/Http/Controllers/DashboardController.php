<?php

namespace App\Http\Controllers;

use App\Models\Chantier;
use App\Models\Client;
use App\Models\Contrat;
use App\Models\Depense;
use App\Models\Devis;
use App\Models\Paiement;
use App\Models\Prestataire;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Statistiques devis
        $devisStats = [
            'brouillon' => Devis::where('statut', 'brouillon')->count(),
            'envoye' => Devis::where('statut', 'envoye')->count(),
            'accepte' => Devis::where('statut', 'accepte')->count(),
            'refuse' => Devis::where('statut', 'refuse')->count(),
        ];

        // Statistiques chantiers
        $chantiersStats = [
            'en_cours' => Chantier::where('statut', 'en_cours')->count(),
            'termine' => Chantier::where('statut', 'termine')->count(),
            'suspendu' => Chantier::where('statut', 'suspendu')->count(),
            'en_attente' => Chantier::where('statut', 'en_attente')->count(),
        ];

        // Financier
        $totalARecevoir = Devis::where('statut', 'accepte')->with('lignes')->get()->sum(function ($d) {
            $sousTotal = $d->lignes->sum(fn ($l) => $l->quantite * $l->prix_unitaire);
            $total = $sousTotal - $d->remise + $d->cout_transport + $d->cout_main_oeuvre;
            return $total - $d->acompte;
        });

        $totalAPayerPrestataires = \DB::table('chantier_prestataire')
            ->selectRaw('SUM(montant_convenu - montant_paye) as solde')
            ->value('solde') ?? 0;

        // Dépenses récentes
        $depensesRecentes = Depense::with('chantier')
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        // Contrats actifs
        $contratsActifs = Contrat::where('statut', 'actif')->count();

        return Inertia::render('Dashboard', [
            'devisStats' => $devisStats,
            'chantiersStats' => $chantiersStats,
            'totalARecevoir' => round((float) $totalARecevoir, 2),
            'totalAPayerPrestataires' => round((float) $totalAPayerPrestataires, 2),
            'depensesRecentes' => $depensesRecentes,
            'contratsActifs' => $contratsActifs,
            'totalClients' => Client::count(),
            'totalPrestataires' => Prestataire::count(),
        ]);
    }
}
