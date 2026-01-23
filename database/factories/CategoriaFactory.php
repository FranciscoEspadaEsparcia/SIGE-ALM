<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        return [
            'codigo' => strtoupper($this->faker->bothify('CAT-###')),
            'nombre' => ucfirst($this->faker->words(2, true)),
            'descripcion' => $this->faker->sentence(10),
        ];
    }
}
