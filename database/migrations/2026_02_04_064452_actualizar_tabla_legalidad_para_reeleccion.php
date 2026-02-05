<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('legalidad', function (Blueprint $table) {

        // NUEVOS DOCUMENTOS DE REELECCIÓN
        $table->string('doc_nombramiento')->nullable()->after('periodo_habil');
        $table->string('doc_carta_reeleccion')->nullable()->after('doc_nombramiento');
        $table->string('doc_otros')->nullable()->after('doc_carta_reeleccion');

        // CONTROL DE REELECCIÓN (solo una vez)
        $table->boolean('ya_reelegido')->default(false)->after('estatus_reeleccion');

        if (Schema::hasColumn('legalidad', 'archivo_reeleccion')) {
            $table->dropColumn('archivo_reeleccion');
        }
    });
}

public function down()
{
    Schema::table('legalidad', function (Blueprint $table) {
        $table->string('archivo_reeleccion')->nullable();

        $table->dropColumn([
            'doc_nombramiento',
            'doc_carta_reeleccion',
            'doc_otros',
            'ya_reelegido',
        ]);
    });
}
};
