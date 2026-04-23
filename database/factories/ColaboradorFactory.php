<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ColaboradorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'numero_colaborador' => $this->faker->unique()->numerify('C###'),
            'nombre' => strtoupper($this->faker->firstName()),
            'apellido' => strtoupper($this->faker->lastName()),
            'telefono' => $this->faker->phoneNumber(),
            'pin' => $this->faker->numerify('####'),
            'denominacion_cargo' => $this->faker->jobTitle(),
            'activo' => true,
            'visible_en_dashboard' => true,
        ];
    }
}
