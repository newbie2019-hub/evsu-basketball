<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'team' => 'Prospect Team',
                'description' => 'Prospect players will be placed in this team'
            ],
            [
                'team' => 'Team A',
                'description' => 'Players who will be participating in most of the games'
            ],
            [
                'team' => 'Team B',
                'description' => 'Players who will be the backup for the playing team'
            ],
        ];

        foreach($data as $team) {
            Team::create($team);
        }
    }
}
