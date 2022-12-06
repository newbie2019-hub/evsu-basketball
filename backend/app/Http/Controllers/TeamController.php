<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $gameschedule = Team::with('players')->withCount('players')->when($request->search, fn ($query, $search)
            => $query->where('team', 'like', '%' . $search . '%')
            ->orWhereRelation('user', 'first_name', 'like', '%'. $search .'%')
            ->orWhereRelation('user', 'last_name', 'like', '%'. $search .'%'))
        ->paginate($request->per_page);

        return $this->data($gameschedule);
    }

    public function store(TeamRequest $request)
    {
        Team::create($request->validated());
        return $this->success('Team has been created successfully!');
    }

    public function update(TeamRequest $request, Team $team)
    {
        $team->update($request->validated());
        return $this->success('Team has been updated successfully!');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return $this->success('Team has been removed successfully!');
    }
}
