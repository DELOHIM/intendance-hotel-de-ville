<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->nullable()->constrained('incidents')->nullOnDelete();
            $table->string('destinataire');
            $table->text('message');
            $table->string('statut', 20)->default('en_attente')->index(); // en_attente, envoye, echec
            $table->unsignedSmallInteger('tentatives')->default(0);
            $table->text('erreur')->nullable();
            $table->timestamp('envoye_le')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('cle')->primary();
            $table->text('valeur')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50)->index();
            $table->string('sujet_type')->nullable();
            $table->unsignedBigInteger('sujet_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index(['sujet_type', 'sujet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notification_logs');
    }
};
