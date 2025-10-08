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
        Schema::create('integrante_consejo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('integrante_id'); 
            $table->unsignedBigInteger('consejo_id');
            $table->foreign('consejo_id')->references('id')->on('consejos')
                 ->onDelete('cascade');
            $table->foreign('integrante_id')->references('id')->on('integrantes')
                 ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integrante_consejo');
    }
};
