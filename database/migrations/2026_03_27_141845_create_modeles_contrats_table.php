<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modeles_contrats', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['client', 'prestataire']);
            $table->text('contenu');
            $table->boolean('par_defaut')->default(false);
            $table->timestamps();
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->decimal('acompte', 12, 2)->default(0)->after('montant_convenu');
            $table->foreignId('modele_contrat_id')->nullable()->after('type')->constrained('modeles_contrats')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropForeign(['modele_contrat_id']);
            $table->dropColumn(['acompte', 'modele_contrat_id']);
        });
        Schema::dropIfExists('modeles_contrats');
    }
};
