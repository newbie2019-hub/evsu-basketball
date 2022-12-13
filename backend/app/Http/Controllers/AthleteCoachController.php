<?php

namespace App\Http\Controllers;

use App\Http\Requests\AthleteCoachRequest;
use App\Models\AthleteCoachAssignee;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AthleteCoachController extends Controller
{
    use ApiResponse;

    public function all()
    {
        $categories = AthleteCoachAssignee::get();
        return $this->data($categories);
    }

    public function index(Request $request)
    {
        $category = AthleteCoachAssignee::with(['athlete', 'coach'])->when($request->search, fn ($query, $search)
        => $query->whereRelation('athlete', 'first_name', 'like', '%' . $search . '%'))
            ->paginate($request->per_page);

        return $this->data($category);
    }

    public function store(AthleteCoachRequest $request)
    {
        $athleteCoach = AthleteCoachAssignee::where('coach_id', $request->id)->get();

        $athleteCoach->each(function($assignee) {
            $assignee->delete();
        });

        foreach ($request->user_id as $data) {
            AthleteCoachAssignee::create([
                'coach_id' => $request->id,
                'athlete_id' => $data
            ]);
        }
        return $this->success('Athlete\'s has been assigned successfully!');
    }

    public function update(AthleteCoachRequest $request, AthleteCoachAssignee $athleteAssignee)
    {
        $athleteAssignee->delete();
        foreach ($request->user_id as $data) {
            AthleteCoachAssignee::create([
                'coach_id' => $request->coach_id,
                'athlete_id' => $data
            ]);
        }
        return $this->success('Athlete\'s has been updated successfully!');
    }
}
