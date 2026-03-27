<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Devis extends Model
{
    use HasFactory;

    protected $table = 'devis';

    protected $fillable = [
        'numero',
        'client_id',
        'chantier_id',
        'date',
        'date_validite',
        'remise',
        'cout_transport',
        'cout_main_oeuvre',
        'acompte',
        'notes',
        'statut',
    ];

    protected $casts = [
        'date' => 'date',
        'date_validite' => 'date',
        'remise' => 'decimal:2',
        'cout_transport' => 'decimal:2',
        'cout_main_oeuvre' => 'decimal:2',
        'acompte' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(DevisLigne::class)->orderBy('ordre');
    }

    public function contrat(): HasMany
    {
        return $this->hasMany(Contrat::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }

    /**
     * Calcule le sous-total (somme des lignes)
     */
    public function sousTotalAttribute(): float
    {
        return (float) $this->lignes->sum(fn ($l) => $l->quantite * $l->prix_unitaire);
    }

    /**
     * Calcule le total général
     */
    public function totalGeneralAttribute(): float
    {
        return (float) ($this->sousTotalAttribute() - $this->remise + $this->cout_transport + $this->cout_main_oeuvre);
    }

    /**
     * Calcule le reste à payer
     */
    public function resteAPayerAttribute(): float
    {
        return (float) ($this->totalGeneralAttribute() - $this->acompte);
    }
}
