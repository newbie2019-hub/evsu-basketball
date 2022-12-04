<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameSchedule>
 */
class GameScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $types = ["Practice Drills", "Practice Game", "Official Game", "Conditioning", "Individual Training"];
        $name = ["Game Preparation", "Strength Conditioning", "Hand Strength"];

        return [
            'name' => fake()->randomElement($name),
            'description' => fake()->sentence(8),
            'type' => fake()->randomElement($types),
            'schedule' => fake()->dateTimeBetween('-2 weeks', '+4 weeks'),
        ];
    }
}
