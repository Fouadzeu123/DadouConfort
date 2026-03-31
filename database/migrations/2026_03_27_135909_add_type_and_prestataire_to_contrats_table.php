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
        });
        
        // Pour permettre de passer null si c'est un contrat prestataire,
        // on doit supprimer la contrainte, modifier la colonne, et remettre la contrainte.
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->change();
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropForeign(['prestataire_id']);
            $table->dropColumn(['type', 'prestataire_id']);
        });
        
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
        });
    }
};
