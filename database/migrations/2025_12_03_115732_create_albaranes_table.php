<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albaranes', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 50);
            $table->date('fecha');

            $table->foreignId('id_proveedor')
                  ->constrained('proveedores')
                  ->onDelete('restrict');

            $table->timestamps();

            $table->unique(['numero', 'id_proveedor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albaranes');
    }
};
