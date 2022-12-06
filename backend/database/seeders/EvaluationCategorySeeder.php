<?php

namespace Database\Seeders;

use App\Models\EvaluationCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EvaluationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shooting = [
            [
                'category' => 'Lay-up'
            ],
            [
                'category' => 'Mechanics & Arc'
            ],
            [
                'category' => '2-point range'
            ],
            [
                'category' => '3-point range'
            ],
            [
                'category' => 'Catch & Shoot'
            ],
            [
                'category' => 'Shoot off dribble'
            ],
            [
                'category' => 'Use of weak hand'
            ],
        ];

        $dribbling = [
            [
                'category' => 'Maintains control'
            ],
            [
                'category' => 'Sees the court'
            ],
            [
                'category' => 'Goes both ways'
            ],
            [
                'category' => 'Handles pressure'
            ],
            [
                'category' => 'Speed'
            ],
            [
                'category' => 'Dribbles with purpose'
            ],
            [
                'category' => 'Penetrates to hoop'
            ],
        ];

        $passing = [
            [
                'category' => 'Timing'
            ],
            [
                'category' => 'Catching'
            ],
            [
                'category' => 'Avoids turnovers'
            ],
            [
                'category' => '2 handed'
            ],
            [
                'category' => '1 handed'
            ],
            [
                'category' => 'Bounce pass'
            ],
            [
                'category' => 'Overhead'
            ],
        ];

        $defense = [
            [
                'category' => 'Position'
            ],
            [
                'category' => 'Transition'
            ],
            [
                'category' => 'Stance'
            ],
            [
                'category' => 'On ball'
            ],
            [
                'category' => 'Off ball'
            ],
            [
                'category' => 'Closes out'
            ],
            [
                'category' => 'Help'
            ],
            [
                'category' => 'Recover to man'
            ]
        ];

        $rebounding = [
            [
                'category' => 'Anticipates'
            ],
            [
                'category' => 'Goes for the ball'
            ],
            [
                'category' => 'Boxes out'
            ],
            [
                'category' => 'Finds the right spot'
            ],
            [
                'category' => 'Protects/chins the ball'
            ],
        ];

        $athletic_ability = [
            [
                'category' => 'Speed'
            ],
            [
                'category' => 'Quickness'
            ],
            [
                'category' => 'Stamina'
            ],
            [
                'category' => 'Coordination'
            ],
        ];

        $gameplay = [
            [
                'category' => 'Court Sense'
            ],
            [
                'category' => 'Team play/assists'
            ],
            [
                'category' => 'Vision'
            ],
            [
                'category' => 'Anticipation'
            ],
        ];

        $coachability = [
            [
                'category' => 'Attitude'
            ],
            [
                'category' => 'Accepts criticism'
            ],
            [
                'category' => 'Focus'
            ],
            [
                'category' => 'Interaction with teammates'
            ],
            [
                'category' => 'Team play'
            ],
            [
                'category' => 'Work ethic'
            ]
        ];

        $overall = [
            [
                'category' => 'Defense'
            ],
            [
                'category' => 'Dribbling'
            ],
            [
                'category' => 'Passing'
            ],
            [
                'category' => 'Rebounding'
            ],
            [
                'category' => 'Shooting'
            ],
            [
                'category' => 'Court Sense'
            ]
        ];

        foreach ($shooting as $s) {
            EvaluationCategory::create(['evaluation_id' => 1] + $s);
        }

        foreach ($dribbling as $d) {
            EvaluationCategory::create(['evaluation_id' => 2] + $d);
        }

        foreach ($passing as $p) {
            EvaluationCategory::create(['evaluation_id' => 3] + $p);
        }

        foreach ($defense as $def) {
            EvaluationCategory::create(['evaluation_id' => 4] + $def);
        }

        foreach ($rebounding as $r) {
            EvaluationCategory::create(['evaluation_id' => 5] + $r);
        }

        foreach ($athletic_ability as $ab) {
            EvaluationCategory::create(['evaluation_id' => 6] + $ab);
        }

        foreach ($gameplay as $g) {
            EvaluationCategory::create(['evaluation_id' => 7] + $g);
        }

        foreach ($coachability as $c) {
            EvaluationCategory::create(['evaluation_id' => 8] + $c);
        }

        foreach ($overall as $o) {
            EvaluationCategory::create(['evaluation_id' => 9] + $o);
        }
    }
}
