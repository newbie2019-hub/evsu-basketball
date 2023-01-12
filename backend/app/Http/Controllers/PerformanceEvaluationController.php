<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerformanceEvalRequest;
use App\Models\AthleteCoachAssignee;
use App\Models\Evaluation;
use App\Models\PerformanceCategory;
use App\Models\PerformanceEvaluation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceEvaluationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $athletes = AthleteCoachAssignee::where('coach_id', auth()->id())->pluck('athlete_id')->toArray();

        if (auth()->user()->position === 'Assistant-Coach') {
            $performanceEvaluation = PerformanceEvaluation::withWhereHas('user', function ($query) use ($athletes) {
                $query->whereIn('id', $athletes);
            })->when($request->search, fn ($query, $search)
            => $query->whereRelation('user', 'first_name', 'like', '%' . $search . '%')
                ->orWhereRelation('user', 'last_name', 'like', '%' . $search . '%'))
                ->with(['category'])
                ->paginate($request->per_page);
        } else if (auth()->user()->position === 'Coach') {
            $performanceEvaluation = PerformanceEvaluation::with(['user', 'category'])->when($request->search, fn ($query, $search)
            => $query->whereRelation('user', 'first_name', 'like', '%' . $search . '%')
                ->orWhereRelation('user', 'last_name', 'like', '%' . $search . '%'))
                ->paginate($request->per_page);
        } else {
            $performanceEvaluation = PerformanceEvaluation::withWhereHas('user', fn($query)
                => $query->where('id', auth()->id())
            )->with(['category'])->when($request->search, fn ($query, $search)
            => $query->whereRelation('user', 'first_name', 'like', '%' . $search . '%')
                ->orWhereRelation('user', 'last_name', 'like', '%' . $search . '%'))
                ->paginate($request->per_page);
        }

        return $this->data($performanceEvaluation);
    }

    public function store(PerformanceEvalRequest $request)
    {
        $evaluation = PerformanceEvaluation::create($request->validated());

        foreach ($request->evaluations as $eval) {
            foreach ($eval['category'] as $categ) {
                PerformanceCategory::create([
                    'evaluation_id' => $categ['evaluation_id'],
                    'per_eval_id' => $evaluation->id,
                    'evaluation_categories_id' => $categ['id'],
                    'score' => $categ['score'] ?? 0,
                    'max_score' => 5
                ]);
            }
        }

        return $this->success('Performance Evaluation created successfully!');
    }

    public function update(PerformanceEvalRequest $request, PerformanceEvaluation $performance)
    {
        $performance->update($request->validated());
        return $this->success('Performance Evaluation has been updated successfully!');
    }


    public function show(PerformanceEvaluation $performance)
    {
        $evaluation = Evaluation::with(['category', 'category.score' => function ($query) use ($performance) {
            $query->where('per_eval_id', $performance->id);
        }])->get();

        $performance->load(['user:id,first_name,last_name,weight,position,height']);
        return $this->data(['data' => $evaluation, 'performance' => $performance]);
    }

    public function destroy(PerformanceEvaluation $performance)
    {
        $performance->delete();
        return $this->success('Performance Evaluation has been removed successfully!');
    }
}
