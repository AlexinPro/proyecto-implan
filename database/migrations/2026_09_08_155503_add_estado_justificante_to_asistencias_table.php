<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::table('asistencias', function (Blueprint $table) {
            // Estado administrativo del justificante
            $table->enum('estado_justificante', [
                'pendiente',
                'aprobado',
                'rechazado'
            ])->nullable()->after('justificante');
        });
    }

    public function down(): void {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropColumn('estado_justificante');
        });
    }
};