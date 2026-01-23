<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_trabajo_articulos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_orden_trabajo');
            $table->unsignedBigInteger('id_articulo');

            $table->integer('cantidad');

            $table->timestamps();

            // FKs
            $table->foreign('id_orden_trabajo')
                ->references('id')->on('ordenes_trabajo')
                ->onDelete('cascade');

            $table->foreign('id_articulo')
                ->references('id')->on('articulos')
                ->onDelete('restrict');

            // Evita duplicar el mismo artículo en la misma OT
            $table->unique(['id_orden_trabajo', 'id_articulo'], 'ot_articulo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_trabajo_articulos');
    }
};
