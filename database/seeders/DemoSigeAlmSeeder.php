<?php

namespace Database\Seeders;

use App\Models\Albaran;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Movimiento;
use App\Models\OrdenTrabajo;
use App\Models\OrdenTrabajoArticulo;
use App\Models\Proveedor;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSigeAlmSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ROLES (mínimo 1)
            if (Rol::query()->count() === 0) {
                Rol::query()->create([
                    'nombre' => 'ADMIN',
                    'descripcion' => 'Administrador del sistema',
                ]);
                Rol::query()->create([
                    'nombre' => 'ALMACEN',
                    'descripcion' => 'Operario de almacén',
                ]);
            }

            // CATEGORIAS
            if (Categoria::query()->count() < 8) {
                Categoria::factory()->count(10)->create();
            }

            // PROVEEDORES
            if (Proveedor::query()->count() < 10) {
                Proveedor::factory()->count(12)->create();
            }

            // USUARIOS
            if (Usuario::query()->count() < 5) {
                Usuario::factory()->count(6)->create();
            }

            // ARTICULOS (30)
            if (Articulo::query()->count() < 30) {
                Articulo::factory()->count(30)->create();
            }

            // ALBARANES (10)
            if (Albaran::query()->count() < 10) {
                $proveedorIds = Proveedor::query()->pluck('id')->all();
                for ($i = 1; $i <= 10; $i++) {
                    Albaran::query()->create([
                        'numero' => 'ALB-' . str_pad((string)$i, 5, '0', STR_PAD_LEFT),
                        'fecha' => now()->subDays(rand(1, 60))->toDateString(),
                        'id_proveedor' => $proveedorIds[array_rand($proveedorIds)],
                    ]);
                }
            }

            // MOVIMIENTOS ENTRADA "extra" para dar vida al stock
            $usuarioId = Usuario::query()->inRandomOrder()->value('id');
            $albaranId = Albaran::query()->inRandomOrder()->value('id');

            foreach (Articulo::query()->get() as $a) {
                $n = rand(0, 2);
                for ($k = 0; $k < $n; $k++) {
                    $cantidad = rand(5, 40);

                    Movimiento::query()->create([
                        'tipo' => 'ENTRADA',
                        'cantidad' => $cantidad,
                        'fecha_hora' => now()->subDays(rand(1, 40)),
                        'id_articulo' => $a->id,
                        'id_usuario' => $usuarioId,
                        'id_orden_trabajo' => null,
                        'id_albaran' => $albaranId,
                    ]);

                    $a->stock_actual += $cantidad;
                    $a->save();
                }
            }

            // ORDENES DE TRABAJO (50) + LINEAS + SALIDAS
            if (OrdenTrabajo::query()->count() < 50) {
                for ($i = 1; $i <= 50; $i++) {

                    $ot = OrdenTrabajo::query()->create([
                        'codigo' => 'OT-' . str_pad((string)$i, 5, '0', STR_PAD_LEFT),
                        'descripcion' => 'OT demo ' . $i . ' - ' . fake()->sentence(6),
                        'estado' => fake()->randomElement(['PENDIENTE', 'EN_CURSO', 'FINALIZADA']),
                        'fecha_apertura' => now()->subDays(rand(1, 60))->toDateString(),
                    ]);

                    $lineas = rand(1, 5);

                    $lineas = rand(1, 5);

// Elegimos artículos únicos para esta OT (evita duplicados por unique constraint)
$articulos = Articulo::query()
    ->inRandomOrder()
    ->limit($lineas)
    ->get();

foreach ($articulos as $art) {

    $max = max(1, min(10, (int)$art->stock_actual));
    $cantidad = rand(1, $max);

    OrdenTrabajoArticulo::query()->create([
        'id_orden_trabajo' => $ot->id,
        'id_articulo' => $art->id,
        'cantidad' => $cantidad,
    ]);

    // Movimiento SALIDA asociado a OT
    Movimiento::query()->create([
        'tipo' => 'SALIDA',
        'cantidad' => $cantidad,
        'fecha_hora' => now()->subDays(rand(0, 30)),
        'id_articulo' => $art->id,
        'id_usuario' => Usuario::query()->inRandomOrder()->value('id'),
        'id_orden_trabajo' => $ot->id,
        'id_albaran' => null,
    ]);

    $art->stock_actual -= $cantidad;
    if ($art->stock_actual < 0) { $art->stock_actual = 0; }
    $art->save();

    // Algunas devoluciones parciales (30% prob.)
    if (rand(1, 100) <= 30) {
        $dev = rand(1, $cantidad);

        Movimiento::query()->create([
            'tipo' => 'DEVOLUCION',
            'cantidad' => $dev,
            'fecha_hora' => now()->subDays(rand(0, 20)),
            'id_articulo' => $art->id,
            'id_usuario' => Usuario::query()->inRandomOrder()->value('id'),
            'id_orden_trabajo' => $ot->id,
            'id_albaran' => null,
        ]);

        $art->stock_actual += $dev;
        $art->save();
    }
}

                }
            }
        });
    }
}
