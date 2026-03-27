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
            ['email' => 'dadou@confort.cm'],
            [
                'name' => 'DadouConfort',
                'password' => Hash::make('dadou2026'),
                'email_verified_at' => now(),
            ]
        );

        // === CLIENTS ===
        $clients = [
            ['nom_complet' => 'Jean-Pierre Mbarga', 'telephone' => '677 123 456', 'adresse' => 'Bastos, Yaoundé', 'localisation' => 'Yaoundé', 'notes' => 'Client fidèle, préfère les finitions en bois massif'],
            ['nom_complet' => 'Marie-Claire Fotso', 'telephone' => '699 234 567', 'adresse' => 'Bonanjo, Douala', 'localisation' => 'Douala', 'notes' => 'Projet villa 4 chambres'],
            ['nom_complet' => 'Emmanuel Talla', 'telephone' => '655 345 678', 'adresse' => 'Mendong, Yaoundé', 'localisation' => 'Yaoundé', 'notes' => null],
            ['nom_complet' => 'Agnès Kom', 'telephone' => '670 456 789', 'adresse' => 'Akwa, Douala', 'localisation' => 'Douala', 'notes' => 'Rénovation appartement'],
            ['nom_complet' => 'Paul Njoya', 'telephone' => '693 567 890', 'adresse' => 'Ngousso, Yaoundé', 'localisation' => 'Yaoundé', 'notes' => null],
        ];

        $clientModels = collect($clients)->map(fn ($c) => Client::create($c));

        // === PRESTATAIRES ===
        $prestataires = [
            ['nom_complet' => 'Thomas Onana', 'telephone' => '677 111 222', 'specialite' => 'Soudeur', 'adresse' => 'Melen, Yaoundé', 'disponibilite' => 'disponible', 'tarif_indicatif' => '15 000 FCFA/jour', 'notes' => 'Très fiable, disponible'],
            ['nom_complet' => 'René Abanda', 'telephone' => '699 222 333', 'specialite' => 'Peintre', 'adresse' => 'Biyem-Assi, Yaoundé', 'disponibilite' => 'disponible', 'tarif_indicatif' => '10 000 FCFA/jour', 'notes' => null],
            ['nom_complet' => 'Cédric Nkomo', 'telephone' => '655 333 444', 'specialite' => 'Maçon', 'adresse' => 'Essos, Yaoundé', 'disponibilite' => 'en_mission', 'tarif_indicatif' => '12 000 FCFA/jour', 'notes' => 'En mission jusqu\'au 15 avril'],
            ['nom_complet' => 'André Mvondo', 'telephone' => '670 444 555', 'specialite' => 'Charpentier', 'adresse' => 'Nkol-Eton, Yaoundé', 'disponibilite' => 'disponible', 'tarif_indicatif' => '20 000 FCFA/jour', 'notes' => null],
            ['nom_complet' => 'Eric Biyong', 'telephone' => '693 555 666', 'specialite' => 'Électricien', 'adresse' => 'Omnisports, Yaoundé', 'disponibilite' => 'disponible', 'tarif_indicatif' => '18 000 FCFA/jour', 'notes' => null],
        ];

        $prestataireModels = collect($prestataires)->map(fn ($p) => Prestataire::create($p));

        // === SERVICES ===
        $services = [
            ['nom' => 'Porte en bois massif', 'description' => 'Fabrication et pose de porte en bois massif', 'unite' => 'unité', 'prix_indicatif' => 120000, 'categorie' => 'Menuiserie'],
            ['nom' => 'Fenêtre en bois', 'description' => 'Fabrication et pose de fenêtre en bois', 'unite' => 'unité', 'prix_indicatif' => 80000, 'categorie' => 'Menuiserie'],
            ['nom' => 'Armoire encastrée', 'description' => 'Armoire sur mesure encastrée', 'unite' => 'unité', 'prix_indicatif' => 350000, 'categorie' => 'Mobilier'],
            ['nom' => 'Cuisine équipée', 'description' => 'Installation cuisine complète bois', 'unite' => 'forfait', 'prix_indicatif' => 800000, 'categorie' => 'Mobilier'],
            ['nom' => 'Plafond lambris', 'description' => 'Pose de lambris au plafond', 'unite' => 'm²', 'prix_indicatif' => 15000, 'categorie' => 'Menuiserie'],
            ['nom' => 'Escalier bois', 'description' => 'Fabrication et pose escalier bois', 'unite' => 'forfait', 'prix_indicatif' => 450000, 'categorie' => 'Menuiserie'],
            ['nom' => 'Lit en bois', 'description' => 'Lit sur mesure en bois massif', 'unite' => 'unité', 'prix_indicatif' => 180000, 'categorie' => 'Mobilier'],
            ['nom' => 'Vernissage', 'description' => 'Vernissage finition bois', 'unite' => 'm²', 'prix_indicatif' => 5000, 'categorie' => 'Finition'],
            ['nom' => 'Réparation menuiserie', 'description' => 'Réparation et restauration', 'unite' => 'heure', 'prix_indicatif' => 8000, 'categorie' => 'Réparation'],
            ['nom' => 'Charpente', 'description' => 'Réalisation de charpente bois', 'unite' => 'm²', 'prix_indicatif' => 25000, 'categorie' => 'Charpente'],
        ];

        collect($services)->each(fn ($s) => Service::create($s));

        // === CHANTIERS ===
        $chantier1 = Chantier::create([
            'numero' => 'CH-2026-001',
            'client_id' => $clientModels[0]->id,
            'titre' => 'Villa Mbarga - Menuiserie complète',
            'description' => 'Fabrication et pose de toutes les menuiseries de la villa 5 pièces: portes, fenêtres, armoires et cuisine.',
            'localisation' => 'Bastos, Yaoundé',
            'date_debut' => '2026-01-15',
            'date_fin_prevue' => '2026-04-30',
            'statut' => 'en_cours',
            'notes' => 'Client très exigeant sur la qualité des finitions',
        ]);

        $chantier2 = Chantier::create([
            'numero' => 'CH-2026-002',
            'client_id' => $clientModels[1]->id,
            'titre' => 'Appartement Fotso - Rénovation',
            'description' => 'Rénovation complète de l\'appartement: plafond lambris, dressing, bureau.',
            'localisation' => 'Bonanjo, Douala',
            'date_debut' => '2026-02-01',
            'date_fin_prevue' => '2026-03-31',
            'statut' => 'termine',
            'notes' => null,
        ]);

        $chantier3 = Chantier::create([
            'numero' => 'CH-2026-003',
            'client_id' => $clientModels[2]->id,
            'titre' => 'Maison Talla - Charpente & Portes',
            'description' => 'Réalisation de la charpente et pose de 8 portes en bois massif.',
            'localisation' => 'Mendong, Yaoundé',
            'date_debut' => '2026-03-10',
            'date_fin_prevue' => '2026-05-15',
            'statut' => 'en_attente',
            'notes' => null,
        ]);

        // Affecter prestataires aux chantiers
        $chantier1->prestataires()->attach($prestataireModels[0]->id, [
            'role' => 'Soudeur - Ferronnerie',
            'montant_convenu' => 150000,
            'montant_paye' => 75000,
            'notes' => 'Paiement en 2 tranches',
        ]);
        $chantier1->prestataires()->attach($prestataireModels[1]->id, [
            'role' => 'Peintre - Finitions',
            'montant_convenu' => 80000,
            'montant_paye' => 0,
            'notes' => 'Paiement à la fin',
        ]);

        $chantier2->prestataires()->attach($prestataireModels[1]->id, [
            'role' => 'Peintre',
            'montant_convenu' => 60000,
            'montant_paye' => 60000,
            'notes' => 'Payé en totalité',
        ]);

        // === DEVIS ===
        $devis1 = Devis::create([
            'numero' => 'DEV-2026-001',
            'client_id' => $clientModels[0]->id,
            'chantier_id' => $chantier1->id,
            'date' => '2026-01-10',
            'date_validite' => '2026-02-10',
            'remise' => 50000,
            'cout_transport' => 25000,
            'cout_main_oeuvre' => 200000,
            'acompte' => 500000,
            'notes' => 'Devis accepté et signé',
            'statut' => 'accepte',
        ]);

        DevisLigne::create(['devis_id' => $devis1->id, 'designation' => 'Portes en bois massif', 'quantite' => 8, 'unite' => 'unité', 'prix_unitaire' => 120000, 'ordre' => 0]);
        DevisLigne::create(['devis_id' => $devis1->id, 'designation' => 'Fenêtres en bois', 'quantite' => 12, 'unite' => 'unité', 'prix_unitaire' => 80000, 'ordre' => 1]);
        DevisLigne::create(['devis_id' => $devis1->id, 'designation' => 'Cuisine équipée', 'quantite' => 1, 'unite' => 'forfait', 'prix_unitaire' => 800000, 'ordre' => 2]);
        DevisLigne::create(['devis_id' => $devis1->id, 'designation' => 'Armoire chambre principale', 'quantite' => 1, 'unite' => 'unité', 'prix_unitaire' => 350000, 'ordre' => 3]);

        $devis2 = Devis::create([
            'numero' => 'DEV-2026-002',
            'client_id' => $clientModels[2]->id,
            'chantier_id' => $chantier3->id,
            'date' => '2026-03-05',
            'date_validite' => '2026-04-05',
            'remise' => 0,
            'cout_transport' => 30000,
            'cout_main_oeuvre' => 120000,
            'acompte' => 0,
            'notes' => null,
            'statut' => 'envoye',
        ]);

        DevisLigne::create(['devis_id' => $devis2->id, 'designation' => 'Charpente bois', 'quantite' => 45, 'unite' => 'm²', 'prix_unitaire' => 25000, 'ordre' => 0]);
        DevisLigne::create(['devis_id' => $devis2->id, 'designation' => 'Portes en bois massif', 'quantite' => 8, 'unite' => 'unité', 'prix_unitaire' => 120000, 'ordre' => 1]);

        // === CONTRAT ===
        Contrat::create([
            'numero' => 'CTR-2026-001',
            'client_id' => $clientModels[0]->id,
            'chantier_id' => $chantier1->id,
            'devis_id' => $devis1->id,
            'objet' => 'Menuiserie complète - Villa Mbarga',
            'description_travaux' => 'Fourniture et pose de menuiseries complètes: 8 portes, 12 fenêtres, cuisine équipée, armoire sur mesure.',
            'montant_convenu' => 2835000,
            'date_signature' => '2026-01-12',
            'date_debut' => '2026-01-15',
            'date_fin_estimee' => '2026-04-30',
            'conditions_paiement' => 'Acompte 500 000 FCFA à la signature, solde à la livraison.',
            'clauses' => 'Les travaux seront réalisés selon les règles de l\'art et les normes en vigueur. En cas de modifications, un avenant sera signé.',
            'statut' => 'actif',
        ]);

        // === PAIEMENTS ===
        Paiement::create([
            'numero' => 'PAY-2026-001',
            'type' => 'recu_client',
            'montant' => 500000,
            'date' => '2026-01-12',
            'mode_paiement' => 'Virement',
            'reference' => 'VIR-001-2026',
            'note' => 'Acompte à la signature du contrat',
            'client_id' => $clientModels[0]->id,
            'chantier_id' => $chantier1->id,
            'contrat_id' => 1,
        ]);

        Paiement::create([
            'numero' => 'PAY-2026-002',
            'type' => 'verse_prestataire',
            'montant' => 75000,
            'date' => '2026-02-15',
            'mode_paiement' => 'Espèces',
            'reference' => null,
            'note' => 'Premier versement Thomas Onana',
            'prestataire_id' => $prestataireModels[0]->id,
            'chantier_id' => $chantier1->id,
        ]);

        Paiement::create([
            'numero' => 'PAY-2026-003',
            'type' => 'recu_client',
            'montant' => 300000,
            'date' => '2026-03-01',
            'mode_paiement' => 'Orange Money',
            'reference' => 'OM-20260301-001',
            'note' => 'Paiement partiel chantier en cours',
            'client_id' => $clientModels[0]->id,
            'chantier_id' => $chantier1->id,
        ]);

        // === DEPENSES ===
        Depense::create([
            'titre' => 'Achat bois massif - Lot 1',
            'description' => '20 m³ de bois de fraké pour les portes et fenêtres',
            'montant' => 380000,
            'date' => '2026-01-20',
            'categorie' => 'materiaux',
            'chantier_id' => $chantier1->id,
        ]);

        Depense::create([
            'titre' => 'Transport matériaux',
            'description' => 'Location camion pour transport bois depuis Mbalmayo',
            'montant' => 45000,
            'date' => '2026-01-21',
            'categorie' => 'transport',
            'chantier_id' => $chantier1->id,
        ]);

        Depense::create([
            'titre' => 'Achat quincaillerie',
            'description' => 'Serrures, gonds, charnières et accessoires',
            'montant' => 120000,
            'date' => '2026-02-05',
            'categorie' => 'materiaux',
            'chantier_id' => $chantier1->id,
        ]);

        Depense::create([
            'titre' => 'Location toupie',
            'description' => 'Location toupie professionnelle pour les moulures',
            'montant' => 35000,
            'date' => '2026-02-10',
            'categorie' => 'location_outils',
            'chantier_id' => $chantier1->id,
        ]);

        Depense::create([
            'titre' => 'Peinture et vernis',
            'description' => 'Produits de finition pour ébénisterie',
            'montant' => 85000,
            'date' => '2026-03-01',
            'categorie' => 'materiaux',
            'chantier_id' => $chantier2->id,
        ]);
    }
}
