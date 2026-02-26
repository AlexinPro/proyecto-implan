<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postulacion_documentos', function (Blueprint $table) {
            $table->id();

            // Relación con postulación
            $table->foreignId('postulacion_id')
                ->constrained('postulaciones')
                ->cascadeOnDelete();

            // Tipo de documento (ine, acta_constitutiva, etc.)
            $table->string('tipo');

            // Ruta del archivo en storage
            $table->string('archivo');

            $table->timestamps();

            // Evita que una postulación tenga dos documentos del mismo tipo
            $table->unique(['postulacion_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulacion_documentos');
    }
};