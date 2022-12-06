<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use App\Models\TeamPlayer;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {

        $teams = Team::with('players')->withCount('players')->when($request->search, fn ($query, $search)
            => $query->where('team', 'like', '%' . $search . '%')
            ->orWhereRelation('user', 'first_name', 'like', '%'. $search .'%')
            ->orWhereRelation('user', 'last_name', 'like', '%'. $search .'%'))
        ->paginate($request->per_page);

        // $teams->players()->attach();
        return $this->data($teams);
    }

    public function store(TeamRequest $request)
    {
        $team = Team::create($request->validated());

        foreach($request->user_id as $user){
            TeamPlayer::create([
                'user_id' => $user,
                'team_id' => $team->id
            ]);
        }

        return $this->success('Team has been created successfully!');
    }

    public function update(TeamRequest $request, Team $team)
    {
        $team->update($request->validated());
        $team->players()->delete();

        foreach($request->user_id as $user){
            TeamPlayer::create([
                'user_id' => $user,
                'team_id' => $team->id
            ]);
        }

        return $this->success('Team has been updated successfully!');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return $this->success('Team has been removed successfully!');
    }
}
