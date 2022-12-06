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
            'drill' => fake()->sentence(4),
            'description' => fake()->sentence(15),
            'hours' => '00',
            'minutes' => fake()->numberBetween(1, 59),
            'seconds' => fake()->numberBetween(5, 59),
        ];
    }
}
