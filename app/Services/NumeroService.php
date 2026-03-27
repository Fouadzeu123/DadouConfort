<?php

namespace App\Services;

use App\Models\Chantier;
use App\Models\Contrat;
use App\Models\Devis;
use App\Models\Paiement;

class NumeroService
{
    public static function genererDevis(): string
    {
        $annee = now()->year;
        $dernier = Devis::whereYear('created_at', $annee)->count();

        return sprintf('DEV-%d-%03d', $annee, $dernier + 1);
    }

    public static function genererContrat(string $type = 'client'): string
    {
        $annee = now()->year;
        $dernier = Contrat::where('type', $type)->whereYear('created_at', $annee)->count();
        $prefix = $type === 'client' ? 'CLC' : 'CLP';

        return sprintf('%s-%d-%03d', $prefix, $annee, $dernier + 1);
    }

    public static function genererPaiement(): string
    {
        $annee = now()->year;
        $dernier = Paiement::whereYear('created_at', $annee)->count();

        return sprintf('PAY-%d-%03d', $annee, $dernier + 1);
    }

    public static function genererChantier(): string
    {
        $annee = now()->year;
        $dernier = Chantier::whereYear('created_at', $annee)->count();

        return sprintf('CH-%d-%03d', $annee, $dernier + 1);
    }
}
