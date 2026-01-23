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
    Schema::table('categorias', function (\Illuminate\Database\Schema\Blueprint $table) {
        if (!Schema::hasColumn('categorias', 'codigo')) {
            $table->string('codigo', 20)->unique()->after('id');
        }

        if (!Schema::hasColumn('categorias', 'nombre')) {
            $table->string('nombre', 100)->after('codigo');
        }
    });
}


public function down(): void
{
    Schema::table('categorias', function (\Illuminate\Database\Schema\Blueprint $table) {
        if (Schema::hasColumn('categorias', 'codigo')) {
            $table->dropUnique(['codigo']);
            $table->dropColumn('codigo');
        }

        if (Schema::hasColumn('categorias', 'nombre')) {
            $table->dropColumn('nombre');
        }
    });
}


};
