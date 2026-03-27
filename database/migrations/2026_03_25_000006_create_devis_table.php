<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('chantier_id')->nullable()->constrained('chantiers')->nullOnDelete();
            $table->date('date');
            $table->date('date_validite')->nullable();
            $table->decimal('remise', 12, 2)->default(0);
            $table->decimal('cout_transport', 12, 2)->default(0);
            $table->decimal('cout_main_oeuvre', 12, 2)->default(0);
            $table->decimal('acompte', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('statut', ['brouillon', 'envoye', 'accepte', 'refuse', 'expire'])->default('brouillon');
            $table->timestamps();
        });

        Schema::create('devis_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devis_id')->constrained('devis')->cascadeOnDelete();
            $table->string('designation');
            $table->text('description')->nullable();
            $table->decimal('quantite', 10, 2)->default(1);
            $table->string('unite')->default('u');
            $table->decimal('prix_unitaire', 12, 2)->default(0);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devis_lignes');
        Schema::dropIfExists('devis');
    }
};
