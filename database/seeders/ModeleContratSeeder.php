<?php

namespace Database\Seeders;

use App\Models\ModeleContrat;
use Illuminate\Database\Seeder;

class ModeleContratSeeder extends Seeder
{
    public function run(): void
    {
        ModeleContrat::create([
            'nom' => 'Modèle Client Standard',
            'type' => 'client',
            'par_defaut' => true,
            'contenu' => "ENTRE LES SOUSSIGNÉS :

Monsieur Mezadi Darios, exerçant sous l’appellation commerciale DadouConfort, Téléphone : {responsable_phone}, ci-après dénommé « Le Prestataire »,
Et
Monsieur/Madame {client_name}, Téléphone : {client_phone}, demeurant à {client_address}, ci-après dénommé « Le Client ».

1. OBJET DU CONTRAT
Le présent contrat a pour objet la réalisation des travaux de menuiserie suivants : {project_name} sur le chantier situé à {project_location}.

2. DURÉE DES TRAVAUX
Les travaux débuteront le {start_date} et se termineront le {end_date} sous réserve des conditions météorologiques, des imprévus de chantier et de l'approvisionnement des matériaux.

3. MONTANT ET MODALITÉS DE PAIEMENT
Le montant total convenu pour la réalisation des travaux est de : {amount}.
- Acompte versé à la signature : {advance}.
- Reste à payer (Solde) : {balance}.

Les paiements s'effectuent par tranche selon l'avancement des travaux. Le solde doit être payé avant la livraison finale des travaux.

4. OBLIGATIONS DU PRESTATAIRE
- Réaliser les travaux conformément aux règles de l'art.
- Utiliser des matériaux de qualité.
- Respecter les délais convenus sauf cas de force majeure.
- Informer le client en cas de difficulté rencontrée sur le chantier.

5. OBLIGATIONS DU CLIENT
- Faciliter l'accès au chantier.
- Effectuer les paiements selon le calendrier convenu.
- Valider les choix de matériaux avant exécution.
- Fournir l’eau et l’électricité si nécessaire au chantier.

6. MODIFICATION DES TRAVAUX
Toute modification ou ajout de travaux fera l’objet d’un accord sur le coût supplémentaire avant exécution.

7. LITIGES
En cas de litige, les parties privilégient un règlement à l’amiable. À défaut, les tribunaux compétents du Cameroun seront saisis.

Fait à {project_city}, le {today_date} en deux exemplaires originaux.

Signature du Prestataire                           Signature du Client
Mezadi Darios                                      {client_name}",
        ]);

        ModeleContrat::create([
            'nom' => 'Modèle Prestataire Standard',
            'type' => 'prestataire',
            'par_defaut' => true,
            'contenu' => "ENTRE LES SOUSSIGNÉS :

Monsieur Mezadi Darios, agissant sous l’appellation DadouConfort, ci-après dénommé « Le Donneur d’Ordre »,
Et
Monsieur {provider_name}, Téléphone : {provider_phone}, Spécialité : {provider_specialty}, ci-après dénommé « Le Prestataire ».

1. OBJET DE LA SOUS-TRAITANCE
Le prestataire est engagé pour la réalisation des travaux suivants : {project_name} sur le site de {project_location}.

2. DURÉE DES TRAVAUX
Le prestataire interviendra du {start_date} au {end_date}.

3. RÉMUNÉRATION ET PAIEMENT
Le montant convenu pour la réalisation des travaux est de : {amount}.
- Avance versée : {advance}.
- Solde après réception des travaux : {balance}.

4. OBLIGATIONS DU PRESTATAIRE
- Réaliser correctement les travaux confiés.
- Respecter les délais.
- Respecter les consignes du donneur d’ordre.
- Répondre de la qualité de son travail.

5. OBLIGATIONS DU DONNEUR D’ORDRE
- Mettre le prestataire dans les conditions nécessaires pour travailler.
- Payer le prestataire selon le montant convenu.

6. RÉSILIATION
En cas de mauvaise exécution des travaux ou de non-respect des engagements, le présent contrat peut être résilié.

7. LITIGES
En cas de litige, les parties privilégient un règlement à l’amiable. À défaut, les tribunaux compétents du Cameroun seront saisis.

Fait à {project_city}, le {today_date}.

Signature DadouConfort                            Signature Prestataire
Mezadi Darios                                     {provider_name}",
        ]);
    }
}
