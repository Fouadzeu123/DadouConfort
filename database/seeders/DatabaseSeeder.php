<?php

namespace Database\Seeders;

use App\Models\Chantier;
use App\Models\Client;
use App\Models\Contrat;
use App\Models\Depense;
use App\Models\Devis;
use App\Models\DevisLigne;
use App\Models\Paiement;
use App\Models\Prestataire;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer l'utilisateur DadouConfort
        $user = User::firstOrCreate(
            ['email' => 'dadouconfort@admin.com'],
            [
                'name' => 'DadouConfort',
                'password' => Hash::make('Dadou#2000'),
                'email_verified_at' => now(),
            ]
        );

    }
}
