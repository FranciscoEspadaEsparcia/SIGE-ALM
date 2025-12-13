<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['ENTRADA', 'SALIDA', 'AJUSTE', 'DEVOLUCION']);
            $table->integer('cantidad');
            $table->dateTime('fecha_hora');

            $table->foreignId('id_articulo')
                  ->constrained('articulos')
                  ->onDelete('restrict');

            $table->foreignId('id_usuario')
                  ->constrained('usuarios')
                  ->onDelete('restrict');

            $table->foreignId('id_orden_trabajo')
                  ->nullable()
                  ->constrained('ordenes_trabajo')
                  ->nullOnDelete();

            $table->foreignId('id_albaran')
                  ->nullable()
                  ->constrained('albaranes')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};

