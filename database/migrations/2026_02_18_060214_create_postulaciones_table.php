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
        Schema::create('postulaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos');
            $table->foreignId('consejo_id')->constrained()->onDelete('cascade');
            $table->string('puesto');
            //ruta de almacenamiento del documento de postulación
            $table->string('documento')->nullable();
            //validacion (flujo)
            $table->enum('estatus', ['pendiente', 'aprobada', 'no_aprobada'])
            ->default('pendiente');
            $table->foreignId('validado_por')->nullable()->constrained('users')
            ->nullOnDelete();
            //timestamps para saber cuándo se validó
            $table->timestamp('fecha_validacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulaciones');
    }
};
