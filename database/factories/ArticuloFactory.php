<?php

namespace Database\Factories;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticuloFactory extends Factory
{
    protected $model = Articulo::class;

    public function definition(): array
    {
        $categoriaId = Categoria::query()->inRandomOrder()->value('id');
        $proveedorId = Proveedor::query()->inRandomOrder()->value('id');

        return [
            'codigo' => strtoupper($this->faker->bothify('MAT-####')),
            'nombre' => ucfirst($this->faker->words(3, true)),
            'descripcion' => $this->faker->sentence(12),
            'stock_actual' => $this->faker->numberBetween(20, 200),
            'stock_minimo' => $this->faker->numberBetween(5, 30),
            'activo' => 1,
            'id_categoria' => $categoriaId,
            'id_proveedor_preferente' => $proveedorId,
        ];
    }
}
