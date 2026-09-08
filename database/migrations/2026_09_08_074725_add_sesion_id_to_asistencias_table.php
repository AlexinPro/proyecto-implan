<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->foreignId('sesion_id')
                ->nullable()
                ->after('id')
                ->constrained('sesiones')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sesion_id');
        });
    }
};