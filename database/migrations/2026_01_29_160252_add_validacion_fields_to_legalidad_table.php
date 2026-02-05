<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('legalidad', function (Blueprint $table) {

            // Estatus de la solicitud
            $table->enum('estatus_reeleccion', [
                'pendiente',
                'aprobado',
                'rechazado'
            ])->default('pendiente')->after('fecha_inicio_reeleccion');

            // Fecha en la que se validó
            $table->date('fecha_validacion')
                  ->nullable()
                  ->after('estatus_reeleccion');

            // Usuario que validó
            $table->foreignId('validado_por')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->after('fecha_validacion');
        });
    }

    public function down(): void
    {
        Schema::table('legalidad', function (Blueprint $table) {
            $table->dropForeign(['validado_por']);
            $table->dropColumn([
                'estatus_reeleccion',
                'fecha_validacion',
                'validado_por',
            ]);
        });
    }
};
