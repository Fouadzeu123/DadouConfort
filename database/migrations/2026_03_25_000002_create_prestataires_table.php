<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestataires', function (Blueprint $table) {
            $table->id();
            $table->string('nom_complet');
            $table->string('telephone')->nullable();
            $table->string('specialite')->nullable();
            $table->string('adresse')->nullable();
            $table->enum('disponibilite', ['disponible', 'indisponible', 'en_mission'])->default('disponible');
            $table->text('notes')->nullable();
            $table->string('tarif_indicatif')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestataires');
    }
};
