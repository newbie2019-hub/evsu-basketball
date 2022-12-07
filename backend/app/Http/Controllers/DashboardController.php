<?php

namespace App\Http\Controllers;

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
        $user = User::where('user_type', '<>', 'admin')->count();
        $gameschedule = GameSchedule::count();
        $evaluation = Evaluation::count();
        $drills = GameDrill::count();

        return $this->data([
            'performanceCategory' => $performanceCategory,
            'user' => $user,
            'gameSchedule' => $gameschedule,
            'evaluation' => $evaluation,
            'drills' => $drills,
        ]);
    }
}
