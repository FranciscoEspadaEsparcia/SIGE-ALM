<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alertas_stock', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_articulo')
                ->constrained('articulos')
                ->cascadeOnDelete();

            $table->integer('stock_actual');
            $table->integer('stock_minimo');

            $table->string('estado', 20)->default('PENDIENTE'); // PENDIENTE | RESUELTA
            $table->timestamp('fecha_hora');

            $table->timestamps();

            $table->index(['id_articulo', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_stock');
    }
};
