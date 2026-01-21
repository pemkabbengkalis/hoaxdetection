<?php

namespace Database\Factories;

use App\Models\Domain;
use App\Models\Team;
use App\Models\Validator;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResultFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'type' => fake()->word(),
            'keyword' => fake()->word(),
            'url' => fake()->url(),
            'target_account' => fake()->word(),
            'capture' => fake()->word(),
            'domain_id' => Domain::factory(),
            'validator_id' => Validator::factory(),
            'team_id' => Team::factory(),
            'validated_at' => fake()->dateTime(),
            'published_at' => fake()->dateTime(),
            'status' => fake()->word(),
        ];
    }
}
