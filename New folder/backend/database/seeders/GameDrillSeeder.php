<?php

namespace Database\Seeders;

use App\Models\GameDrill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameDrillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GameDrill::factory(20)->create();
    }
}
