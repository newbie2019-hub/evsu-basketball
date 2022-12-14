<?php

namespace App\Http\Controllers;

use App\Http\Requests\AthleteRequest;
use App\Models\AthleteCoachAssignee;
use App\Models\Evaluation;
use App\Models\PerformanceEvaluation;
use App\Models\PlayerPerformance;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class AthletesController extends Controller
{
    use ApiResponse;

    public function assign(User $user, Request $request)
    {
        $user->drills()->sync($request->all());
        return $this->success('Drills has been assigned successfully!');
    }

    public function index(Request $request)
    {
        $teamQuery = $request->filter_by_team == 'null' ? '' : $request->filter_by_team;
        $athletes = AthleteCoachAssignee::where('coach_id', auth()->id())->pluck('athlete_id')->toArray();

        $athletes = User::with(['team.team', 'drills'])->where('user_type', '<>', 'admin')
            ->where('position', '<>', 'Assistant-Coach')
            ->when(
                $request->assignedPlayers,
                fn ($query)
                => $query->whereIn('id', $athletes)
            )
            ->when(
                $teamQuery,
                fn ($query, $team)
                => $query->whereRelation('team.team', 'id', $team)
            )
            ->where(function ($query) use ($request) {
                $query->when($request->search, fn ($query, $search)
                => $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%'));
            })->orderBy('first_name', 'asc')->paginate($request->per_page);

        return $this->data($athletes);
    }

    public function store(AthleteRequest $request)
    {
        User::create($request->validated() + ['password' => '123123']);
        return $this->success('Account created successfully!');
    }

    public function show(User $user)
    {
        $user->load(['team.team', 'drills.category']);
        $pf = PlayerPerformance::where('user_id', $user->id)->get();

        $evaluation = PerformanceEvaluation::where('user_id', $user->id)->get();

        return $this->data([
            'user' => $user,
            'performance' => $pf,
            'evaluation' => $evaluation
        ]);
    }

    public function update(AthleteRequest $request, User $user)
    {
        $user->update($request->validated());
        return $this->success('User data updated successfully!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return $this->success('Athlete\'s data was removed successfully!');
    }


    public function getAthletes(Request $request)
    {

        $athletes = User::where('user_type', '<>', 'admin')
            ->where('position', '<>', 'Assistant-Coach')
            ->where(function ($query) use ($request) {
                $query->when($request->search, fn ($query, $search)
                => $query->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%'));
            })->latest()->take(10)->get(['id', 'first_name', 'last_name', 'email']);

        return $this->data($athletes);
    }
}
