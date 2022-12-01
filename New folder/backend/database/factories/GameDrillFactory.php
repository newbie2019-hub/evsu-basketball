<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameDrill>
 */
class GameDrillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'drill_category_id' => fake()->numberBetween(1, 7),
            'drill' => fake()->sentence(6),
            'description' => fake()->sentence(20),
            'duration' => fake()->numberBetween(10, 300),
        ];
    }
}
