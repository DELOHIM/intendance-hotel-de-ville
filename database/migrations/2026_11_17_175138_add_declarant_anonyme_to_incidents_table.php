<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Ajout des colonnes pour le déclarant anonyme (sans le "after")
            $table->string('declarant_nom')->nullable();
            $table->string('declarant_telephone')->nullable();
            
            // Rendre user_id nullable pour permettre les signalements anonymes
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['declarant_nom', 'declarant_telephone']);
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};