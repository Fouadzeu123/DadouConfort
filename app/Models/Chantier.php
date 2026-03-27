<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chantier extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'client_id',
        'titre',
        'description',
        'localisation',
        'date_debut',
        'date_fin_prevue',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function prestataires(): BelongsToMany
    {
        return $this->belongsToMany(Prestataire::class, 'chantier_prestataire')
            ->withPivot(['id', 'role', 'montant_convenu', 'montant_paye', 'notes'])
            ->withTimestamps();
    }

    public function devis(): HasMany
    {
        return $this->hasMany(Devis::class);
    }

    public function contrat(): HasOne
    {
        return $this->hasOne(Contrat::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class);
    }

    /**
     * Calcule la marge estimée du chantier
     */
    public function margeEstimee(): float
    {
        $recettes = $this->paiements()->where('type', 'recu_client')->sum('montant');
        $depenses = $this->depenses()->sum('montant');
        $prestataires = $this->prestataires()->sum('chantier_prestataire.montant_paye');

        return (float) ($recettes - $depenses - $prestataires);
    }
}
