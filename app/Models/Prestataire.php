<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prestataire extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_complet',
        'telephone',
        'specialite',
        'adresse',
        'disponibilite',
        'notes',
        'tarif_indicatif',
    ];

    public function chantiers(): BelongsToMany
    {
        return $this->belongsToMany(Chantier::class, 'chantier_prestataire')
            ->withPivot(['role', 'montant_convenu', 'montant_paye', 'notes'])
            ->withTimestamps();
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class);
    }
}
