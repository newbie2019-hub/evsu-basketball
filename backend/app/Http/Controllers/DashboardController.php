<?php

namespace App\Http\Controllers;

use App\Models\AthleteCoachAssignee;
use App\Models\Evaluation;
use App\Models\GameDrill;
use App\Models\GameSchedule;
use App\Models\PerformanceCategory;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    public function getData()
    {
        $performanceCategory = PerformanceCategory::count();
        $user = User::where('user_type', '<>', 'admin')->where('position', '<>', 'Assistant-Coach')->count();
        $coach = User::where('user_type', '<>', 'admin')->where('position', 'Assistant-Coach')->count();
        $gameschedule = GameSchedule::count();
        $evaluation = Evaluation::count();
        $drills = GameDrill::count();
        $assignedAthletes = AthleteCoachAssignee::where('coach_id', auth()->id())->count();

        return $this->data([
            'performanceCategory' => $performanceCategory,
            'user' => $user,
            'coach' => $coach,
            'gameSchedule' => $gameschedule,
            'evaluation' => $evaluation,
            'drills' => $drills,
            'assignedAthletes' => $assignedAthletes,
        ]);
    }
}
