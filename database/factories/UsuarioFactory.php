<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('1234'),
            'nombre' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'activo' => 1,
            'id_rol' => 1,
        ];
    }
}
