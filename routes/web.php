<?php

use App\Http\Controllers\ChantierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PrestataireController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clients
    Route::resource('clients', ClientController::class);

    // Prestataires
    Route::resource('prestataires', PrestataireController::class);

    // Services
    Route::resource('services', ServiceController::class)->except(['show', 'create', 'edit']);

    // Chantiers
    Route::resource('chantiers', ChantierController::class);
    Route::post('/chantiers/{chantier}/prestataires', [ChantierController::class, 'affecterPrestataire'])->name('chantiers.affecter-prestataire');
    Route::put('/chantiers/{chantier}/prestataires/{pivotId}', [ChantierController::class, 'updatePrestataire'])->name('chantiers.update-prestataire');
    Route::delete('/chantiers/{chantier}/prestataires/retirer', [ChantierController::class, 'retirerPrestataire'])->name('chantiers.retirer-prestataire');

    // Devis (note: Laravel uses 'devi' as singular for 'devis')
    Route::resource('devis', DevisController::class)->parameters(['devis' => 'devi']);
    Route::post('/devis/{devi}/dupliquer', [DevisController::class, 'dupliquer'])->name('devis.dupliquer');
    Route::post('/devis/{devi}/convertir-contrat', [DevisController::class, 'convertirEnContrat'])->name('devis.convertir-contrat');

    // Contrats
    Route::resource('contrats', ContratController::class);
    Route::post('/contrats/{contrat}/dupliquer', [ContratController::class, 'dupliquer'])->name('contrats.dupliquer');

    // Paiements
    Route::resource('paiements', PaiementController::class);

    // Dépenses
    Route::resource('depenses', DepenseController::class)->except(['show']);
});

require __DIR__.'/settings.php';
