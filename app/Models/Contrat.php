<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrat extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'type', // 'client' ou 'prestataire'
        'modele_contrat_id',
        'client_id',
        'prestataire_id',
        'chantier_id',
        'devis_id',
        'objet',
        'description_travaux',
        'montant_convenu',
        'acompte',
        'date_signature',
        'date_debut',
        'date_fin_estimee',
        'conditions_paiement',
        'clauses',
        'statut',
    ];

    protected $casts = [
        'date_signature' => 'date',
        'date_debut' => 'date',
        'date_fin_estimee' => 'date',
        'montant_convenu' => 'decimal:2',
        'acompte' => 'decimal:2',
    ];

    public function modeleContrat(): BelongsTo
    {
        return $this->belongsTo(ModeleContrat::class, 'modele_contrat_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class);
    }

    public function prestataire(): BelongsTo
    {
        return $this->belongsTo(Prestataire::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }
}
