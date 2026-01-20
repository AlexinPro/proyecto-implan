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
        Schema::create('legalidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consejo_id')->constrained('consejos')->onDelete('cascade');
            $table->foreignId('integrante_id')->constrained('integrantes')->onDelete('cascade');
            $table->date('inicio_cargo');
            $table->date('fin_cargo');
            $table->string('periodo_habil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legalidad');
    }
};
