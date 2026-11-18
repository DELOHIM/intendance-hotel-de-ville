<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories_biens', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });

        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
            $table->foreignId('categorie_id')->constrained('categories_biens')->restrictOnDelete();
            $table->string('numero_serie')->nullable()->index(); // numéro de série / inventaire
            $table->string('etat', 30)->default('bon')->index();
            $table->foreignId('bureau_id')->constrained('bureaux')->restrictOnDelete();
            $table->date('date_acquisition')->nullable();
            $table->text('remarques')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bien_historiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('biens')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 30);              // creation, modification, transfert, etat
            $table->foreignId('ancien_bureau_id')->nullable()->constrained('bureaux')->nullOnDelete();
            $table->foreignId('nouveau_bureau_id')->nullable()->constrained('bureaux')->nullOnDelete();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bien_historiques');
        Schema::dropIfExists('biens');
        Schema::dropIfExists('categories_biens');
    }
};
