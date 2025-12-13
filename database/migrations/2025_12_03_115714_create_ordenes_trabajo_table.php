<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('descripcion', 255);
            $table->enum('estado', ['PENDIENTE', 'EN_CURSO', 'FINALIZADA', 'ARCHIVADA']);
            $table->date('fecha_apertura');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_trabajo');
    }
};
