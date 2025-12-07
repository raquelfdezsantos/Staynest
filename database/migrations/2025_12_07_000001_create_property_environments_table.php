<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_environments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            
            // Header / Hero
            $table->string('title')->nullable();
            $table->text('subtitle')->nullable();
            $table->string('hero_photo')->nullable();
            $table->text('summary')->nullable();
            
            // Bloque: Naturaleza
            $table->text('nature_description')->nullable();
            $table->string('nature_photo')->nullable();
            
            // Bloque: Cultura y Patrimonio
            $table->text('culture_description')->nullable();
            $table->string('culture_photo')->nullable();
            
            // Bloque: Actividades
            $table->text('activities_description')->nullable();
            $table->string('activities_photo')->nullable();
            
            // Bloque: Servicios Cercanos
            $table->text('services_description')->nullable();
            $table->string('services_photo')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_environments');
    }
};
