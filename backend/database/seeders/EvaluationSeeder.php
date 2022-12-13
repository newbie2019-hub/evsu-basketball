<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EvaluationSeeder extends Seeder
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
                'evaluation' => 'Shooting'
            ],
            [
                'evaluation' => 'Dribbling'
            ],
            [
                'evaluation' => 'Passing'
            ],
            [
                'evaluation' => 'Defense'
            ],
            [
                'evaluation' => 'Rebounding'
            ],
            [
                'evaluation' => 'Athletic Ability'
            ],
            [
                'evaluation' => 'Game Play'
            ],
            [
                'evaluation' => 'Coachability'
            ],
            [
                'evaluation' => 'Overall Strengths'
            ],
        ];

        foreach($data as $eval) {
            Evaluation::create($eval);
        }
    }
}
