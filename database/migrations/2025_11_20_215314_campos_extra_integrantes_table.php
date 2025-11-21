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
        Schema::table('integrantes', function (Blueprint $table) {
            $table->string('genero')->nullable()->after('apellido');
            $table->string('colonia')->nullable()->after('genero');
            $table->string('discapacidad')->nullable()->after('colonia');
            $table->string('discapacidad_tipo')->nullable()->after('discapacidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('integrantes', function (Blueprint $table) {
              $table->dropColumn(['genero', 'colonia', 'discapacidad']);
        });
    }
};
