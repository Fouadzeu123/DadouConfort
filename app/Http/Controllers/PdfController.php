<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Devis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    protected $contractService;

    public function __construct(\App\Services\ContractGeneratorService $contractService)
    {
        $this->contractService = $contractService;
    }

    /**
     * Générer le PDF d'un devis
     */
    public function devis(Devis $devi): Response
    {
        $devi->load(['client', 'chantier', 'lignes']);

        $pdf = Pdf::loadView('pdf.devis', [
            'devi' => $devi,
            'title' => "Devis {$devi->numero}",
        ]);

        return $pdf->download("Devis_{$devi->numero}.pdf");
    }

    /**
     * Générer le PDF d'un contrat
     */
    public function contrat(Contrat $contrat): Response
    {
        $contrat->load(['client', 'prestataire', 'chantier', 'devis']);

        $pdf = Pdf::loadView('pdf.contrat', [
            'contrat' => $contrat,
            'title' => $this->contractService->getTitle($contrat->type),
            'generatedContent' => $this->contractService->generate($contrat),
            'partyLabel' => $contrat->type === 'client' ? 'Le Client (Maître d\'ouvrage)' : 'Le Prestataire (Sous-traitant)',
        ]);

        return $pdf->download("Contrat_{$contrat->numero}.pdf");
    }
}
