<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameScheduleRequest;
use App\Models\GameSchedule;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class GameScheduleController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $gameschedule = GameSchedule::when($request->search, fn ($query, $search)
            => $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('type', 'like', '%'. $search .'%'))
        ->paginate($request->per_page);

        return $this->data($gameschedule);
    }

    public function store(GameScheduleRequest $request)
    {
        GameSchedule::create($request->validated());
        return $this->success('Account created successfully!');
    }

    public function update(GameScheduleRequest $request, GameSchedule $schedule)
    {
        $schedule->update($request->validated());
        return $this->success('Game schedule has been updated successfully!');
    }

    public function destroy(GameSchedule $schedule)
    {
        $schedule->delete();
        return $this->success('Game schedule has been removed successfully!');
    }
}
