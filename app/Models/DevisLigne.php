<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevisLigne extends Model
{
    use HasFactory;

    protected $table = 'devis_lignes';

    protected $fillable = [
        'devis_id',
        'designation',
        'description',
        'quantite',
        'unite',
        'prix_unitaire',
        'ordre',
    ];

    protected $casts = [
        'quantite' => 'decimal:2',
        'prix_unitaire' => 'decimal:2',
    ];

    public function devis(): BelongsTo
    {
        return $this->belongsTo(Devis::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) ($this->quantite * $this->prix_unitaire);
    }
}
