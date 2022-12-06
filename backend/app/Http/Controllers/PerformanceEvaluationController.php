<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerformanceEvalRequest;
use App\Models\PerformanceCategory;
use App\Models\PerformanceEvaluation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class PerformanceEvaluationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $performanceEvaluation = PerformanceEvaluation::with(['user', 'category'])->when($request->search, fn ($query, $search)
            => $query->whereRelation('user', 'first_name', 'like', '%' . $search . '%')
            ->orWhereRelation('user', 'last_name', 'like', '%' . $search . '%'))
        ->paginate($request->per_page);

        return $this->data($performanceEvaluation);
    }

    public function store(PerformanceEvalRequest $request)
    {
        $evaluation = PerformanceEvaluation::create($request->validated());

        foreach($request->evaluations as $eval) {
            foreach($eval['category'] as $categ) {
                PerformanceCategory::create([
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

    public function destroy(PerformanceEvaluation $performance)
    {
        $performance->delete();
        return $this->success('Performance Evaluation has been removed successfully!');
    }
}
