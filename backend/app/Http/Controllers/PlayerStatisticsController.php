<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlayerPerformanceRequest;
use App\Models\AthleteCoachAssignee;
use App\Models\PlayerPerformance;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PlayerStatisticsController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $athletes = AthleteCoachAssignee::where('coach_id', auth()->id())->pluck('athlete_id')->toArray();
        // return $this->data(['dateFrom' => (int)substr(ltrim($request->schoolYear, 'S.Y '), 0, 4), 'dateTo' => substr(ltrim($request->schoolYear, 'S.Y '), -4)]);
        if (auth()->user()->position === 'Assistant-Coach') {
            $statistics = PlayerPerformance::withWhereHas('user', function ($query) use ($athletes) {
                $query->whereIn('id', $athletes);
            })
                ->when(
                    $request->schoolYear,
                    fn ($query, $schoolYear)
                    => $query->whereYear('created_at', (int)substr(ltrim($schoolYear, 'S.Y '), 0, 4))
                        ->orWhereYear('created_at', (int)substr(ltrim($schoolYear, 'S.Y '), -4))
                )
                ->when($request->search, fn ($query, $search)
                => $query->where('team', 'like', '%' . $search . '%')
                    ->orWhereRelation('user', 'first_name', 'like', '%' . $search . '%')
                    ->orWhereRelation('user', 'last_name', 'like', '%' . $search . '%'))
                ->latest()
                ->paginate($request->per_page);
        } else if (auth()->user()->position === 'Coach') {
            $statistics = PlayerPerformance::with(['user'])
                ->when(
                    $request->schoolYear,
                    fn ($query, $schoolYear)
                    => $query->whereYear('created_at', (int)substr(ltrim($schoolYear, 'S.Y '), 0, 4))
                        ->orWhereYear('created_at', (int)substr(ltrim($schoolYear, 'S.Y '), -4))
                )
                ->when($request->search, fn ($query, $search)
                => $query->where('team', 'like', '%' . $search . '%')
                    ->orWhereRelation('user', 'first_name', 'like', '%' . $search . '%')
                    ->orWhereRelation('user', 'last_name', 'like', '%' . $search . '%'))
                ->latest()
                ->paginate($request->per_page);
        } else {
            $statistics = PlayerPerformance::withWhereHas(
                'user',
                fn ($query) =>
                $query->where('id', auth()->id())
            )->when(
                $request->schoolYear,
                fn ($query, $schoolYear)
                => $query->whereYear('created_at', '>=', (int)substr(ltrim($schoolYear, 'S.Y '), 0, 4))
                    ->whereYear('created_at', '<=', (int)substr(ltrim($schoolYear, 'S.Y '), -4))
            )->when($request->search, fn ($query, $search)
            => $query->where('team', 'like', '%' . $search . '%')
                ->orWhereRelation('user', 'first_name', 'like', '%' . $search . '%')
                ->orWhereRelation('user', 'last_name', 'like', '%' . $search . '%'))
                ->latest()
                ->paginate($request->per_page);
        }

        return $this->data($statistics);
    }

    public function store(PlayerPerformanceRequest $request)
    {
        PlayerPerformance::create($request->validated());

        return $this->success('Data has been created successfully!');
    }

    public function update(PlayerPerformanceRequest $request, PlayerPerformance $statistics)
    {
        $statistics->update($request->validated());

        return $this->success('Data has been updated successfully!');
    }

    public function destroy(PlayerPerformance $statistics)
    {
        $statistics->delete();
        return $this->success('Data has been removed successfully!');
    }
}
