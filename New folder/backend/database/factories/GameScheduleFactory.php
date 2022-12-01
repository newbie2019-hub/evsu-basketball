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
        return [
           'schedule' => fake()->dateTimeBetween('-2 weeks', '+4 weeks'),
           'name' => 'Strength Conditioning',
           'type' => 'Practice Drills'
        ];
    }
}
