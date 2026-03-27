<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\ModeleContrat;
use Illuminate\Support\Str;

class ContractGeneratorService
{
    /**
     * Génère le titre du contrat selon le type.
     */
    public function getTitle(string $type): string
    {
        return $type === 'client' 
            ? 'CONTRAT DE PRESTATION DE SERVICES' 
            : 'CONTRAT DE SOUS-TRAITANCE';
    }

    /**
     * Remplace les variables dans le contenu du contrat.
     */
    public function generate(Contrat $contrat): string
    {
        $contrat->load(['client', 'prestataire', 'chantier']);
        
        $modele = $contrat->modeleContrat ?? ModeleContrat::where('type', $contrat->type)->where('par_defaut', true)->first();
        
        if (!$modele) {
            return $this->getDefaultContent($contrat);
        }

        $content = $modele->contenu;
        $variables = $this->getVariables($contrat);

        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }

        return $content;
    }

    /**
     * Retourne la liste des variables disponibles.
     */
    public function getVariables(Contrat $contrat): array
    {
        $today = now()->format('d/m/Y');
        $formatCurrency = fn($val) => number_format($val, 0, ',', ' ') . ' FCFA';

        return [
            'client_name' => $contrat->client?->nom_complet ?? '[Nom du client]',
            'client_phone' => $contrat->client?->telephone ?? '[Téléphone]',
            'client_address' => $contrat->client?->adresse ?? '[Adresse]',
            'provider_name' => $contrat->prestataire?->nom_complet ?? '[Nom du prestataire]',
            'provider_phone' => $contrat->prestataire?->telephone ?? '[Téléphone prestataire]',
            'provider_specialty' => $contrat->prestataire?->specialite ?? '[Spécialité]',
            'project_name' => $contrat->chantier?->titre ?? $contrat->objet,
            'project_location' => $contrat->chantier?->localisation ?? '[Lieu]',
            'start_date' => $contrat->date_debut ? $contrat->date_debut->format('d/m/Y') : '[Date début]',
            'end_date' => $contrat->date_fin_estimee ? $contrat->date_fin_estimee->format('d/m/Y') : '[Date fin]',
            'amount' => $formatCurrency($contrat->montant_convenu),
            'advance' => $formatCurrency($contrat->acompte),
            'balance' => $formatCurrency($contrat->montant_convenu - $contrat->acompte),
            'today_date' => $today,
            'contract_number' => $contrat->numero,
            'responsable_name' => 'Monsieur Dadou',
        ];
    }

    /**
     * Contenu par défaut si aucun template n'est trouvé.
     */
    private function getDefaultContent(Contrat $contrat): string
    {
        if ($contrat->type === 'client') {
            return "Entre les soussignés :\nL’Entreprise DadouConfort Menuiserie, représentée par Monsieur Dadou, ci-après dénommé « Le Prestataire »,\nEt\nMonsieur/Madame {client_name}, Téléphone : {client_phone}, demeurant à {client_address}, ci-après dénommé « Le Client ».\n\nObjet : {project_name}\nMontant : {amount}\nAcompte : {advance}\nSolde : {balance}";
        }
        
        return "Entre :\nL’Entreprise DadouConfort Menuiserie, représentée par Monsieur Dadou, ci-après dénommé « L’Entreprise »,\nEt :\nMonsieur {provider_name}, Téléphone : {provider_phone}, Spécialité : {provider_specialty}, ci-après dénommé « Le Prestataire ».\n\nObjet : {project_name}\nMontant : {amount}";
    }
}
