<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DomainFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'extension' => fake()->word(),
            'description' => fake()->text(),
            'type' => fake()->word(),
        ];
    }
}
