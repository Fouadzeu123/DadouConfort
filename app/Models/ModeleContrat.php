<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeleContrat extends Model
{
    protected $table = 'modeles_contrats';

    protected $fillable = [
        'nom',
        'type',
        'contenu',
        'par_defaut',
    ];

    protected $casts = [
        'par_defaut' => 'boolean',
    ];
}
