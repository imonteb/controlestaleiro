<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EpiItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => strtoupper($this->faker->words(3, true)),
            'codigo' => $this->faker->unique()->bothify('EPI-###??'),
            'tipo' => 'individual',
            'requiere_talla' => false,
            'unidade' => 'unidade',
            'stock_total' => 10,
            'stock_minimo' => 2,
            'activo' => true,
        ];
    }

    public function withStock(int $stock): static
    {
        return $this->state(['stock_total' => $stock]);
    }

    public function semStock(): static
    {
        return $this->state(['stock_total' => 0]);
    }
}
