<?php

namespace Database\Seeders;

use App\Models\DrillCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DrillCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['category' => 'Conditioning'],
            ['category' => 'Defense'],
            ['category' => 'Ball Handling'],
            ['category' => 'Offense'],
            ['category' => 'Individual Defense'],
            ['category' => 'Footwork'],
            ['category' => 'Passing'],
            ['category' => 'Rebounding'],
            ['category' => 'Shooting'],
        ];

        foreach($data as $categ) {
            DrillCategory::create($categ);
        }
    }
}
