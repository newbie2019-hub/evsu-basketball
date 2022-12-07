<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DefaultAccountSeeder::class,
            AthleteSeeder::class,
            AssistantCoachSeeder::class,
            DrillCategorySeeder::class,
            GameDrillSeeder::class,
            GameScheduleSeeder::class,
            EvaluationSeeder::class,
            EvaluationCategorySeeder::class,
            TeamSeeder::class
        ]);
    }
}
