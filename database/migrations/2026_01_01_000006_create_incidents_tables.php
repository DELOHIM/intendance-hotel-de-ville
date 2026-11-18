<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('types_incidents', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });

        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();     // INC-2026-00001
            $table->foreignId('bureau_id')->constrained('bureaux')->restrictOnDelete();
            $table->foreignId('bien_id')->nullable()->constrained('biens')->nullOnDelete();
            $table->foreignId('type_incident_id')->constrained('types_incidents')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // déclarant
            $table->foreignId('assigne_a')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description');
            $table->string('priorite', 20)->default('moyenne')->index();
            $table->string('statut', 20)->default('nouveau')->index();
            $table->string('photo')->nullable();
            $table->timestamp('resolu_le')->nullable();
            $table->timestamps();
        });

        Schema::create('incident_commentaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('contenu')->nullable();
            $table->string('ancien_statut', 20)->nullable();
            $table->string('nouveau_statut', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_commentaires');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('types_incidents');
    }
};
