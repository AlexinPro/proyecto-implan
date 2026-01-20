<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('legalidad', function (Blueprint $table) {
            $table->string('archivo_reeleccion')->nullable()->after('periodo_habil');
            $table->date('fecha_inicio_reeleccion')->nullable()->after('archivo_reeleccion');
        });
    }

    public function down(): void
    {
        Schema::table('legalidad', function (Blueprint $table) {
            $table->dropColumn([
                'archivo_reeleccion',
                'fecha_inicio_reeleccion'
            ]);
        });
    }
};

