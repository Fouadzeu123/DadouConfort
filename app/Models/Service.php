<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'unite',
        'prix_indicatif',
        'categorie',
    ];

    protected $casts = [
        'prix_indicatif' => 'decimal:2',
    ];
}
