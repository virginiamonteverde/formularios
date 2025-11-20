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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');              // Título del formulario
            $table->string('slug')->unique();     // URL pública única
            $table->text('description')->nullable(); // Descripción opcional
            $table->boolean('is_published')->default(false); // Si está publicado
            $table->json('schema')->nullable();   // Acá después guardamos la estructura drag & drop
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
