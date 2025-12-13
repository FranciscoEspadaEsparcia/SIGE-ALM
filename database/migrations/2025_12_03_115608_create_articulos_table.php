<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50);
            $table->string('nombre', 150);
            $table->string('descripcion', 255)->nullable();
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->boolean('activo')->default(true);

            $table->foreignId('id_categoria')
                  ->constrained('categorias')
                  ->onDelete('cascade');

            $table->foreignId('id_proveedor_preferente')
                  ->nullable()
                  ->constrained('proveedores')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};

