<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlayerPerformanceRequest;
use App\Models\PlayerPerformance;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PlayerStatisticsController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {

        $statistics = PlayerPerformance::with('user')->when($request->search, fn ($query, $search)
        => $query->where('team', 'like', '%' . $search . '%')
            ->orWhereRelation('user', 'first_name', 'like', '%' . $search . '%')
            ->orWhereRelation('user', 'last_name', 'like', '%' . $search . '%'))
            ->paginate($request->per_page);

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
