<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bureaux', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();        // ex: B-101 (non modifiable)
            $table->string('libelle');
            $table->foreignId('etage_id')->constrained('etages')->restrictOnDelete();
            $table->string('service')->nullable()->index();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();                     // archivage
        });

        Schema::create('bureau_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bureau_id')->constrained('bureaux')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['bureau_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bureau_user');
        Schema::dropIfExists('bureaux');
    }
};
