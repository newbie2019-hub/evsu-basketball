<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameDrillRequest;
use App\Models\GameDrill;
use App\Models\GameDrillUser;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class GameDrillController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = User::where('id', auth()->id())->get();
        $drills = $user->load(['drills'])->pluck('drills')->flatten(2)->pluck('id');

        $gameschedule = GameDrill::with('category')->when($request->assigned_drills, fn($query)
            => $query->whereIn('id', $drills))->when($request->search, fn ($query, $search)
        => $query->where('description', 'like', '%' . $search . '%')
            ->orWhere('drill', 'like', '%' . $search . '%')
            ->orWhereRelation('category', 'category', 'like', '%' . $search . '%'))
            ->paginate($request->per_page);

        return $this->data($gameschedule);
    }

    public function store(GameDrillRequest $request)
    {
        GameDrill::create($request->validated());
        return $this->success('Game Drill created successfully!');
    }

    public function update(GameDrillRequest $request, GameDrill $drill)
    {
        $drill->update($request->validated());
        return $this->success('Game Drill has been updated successfully!');
    }

    public function destroy(GameDrill $drill)
    {
        $drill->delete();
        return $this->success('Game Drill has been removed successfully!');
    }

    public function getDrills(Request $request)
    {

        $gamedrills = GameDrill::when($request->search, fn ($query, $search)
                    => $query->where('drill', 'like', '%' . $search . '%'))
                    ->latest()->take(10)->get();

        return $this->data($gamedrills);
    }
}
