<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->enum('type', ['client', 'prestataire'])->default('client')->after('numero');
            $table->foreignId('prestataire_id')->nullable()->after('client_id')->constrained('prestataires')->nullOnDelete();
            $table->string('client_id')->change(); // Pour permettre de passer null si c'est un contrat prestataire
        });
        
        // Comme SQLite ne gère pas bien les changements de colonnes complexes via Blueprint, on va ruser
        // ou simplement s'assurer que client_id est nullable
        Schema::table('contrats', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropForeign(['prestataire_id']);
            $table->dropColumn(['type', 'prestataire_id']);
        });
    }
};
