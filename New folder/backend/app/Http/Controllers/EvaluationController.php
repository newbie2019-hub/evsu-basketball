<?php

namespace App\Http\Controllers;

use App\Http\Requests\EvaluationRequest;
use App\Models\Evaluation;
use App\Models\EvaluationCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    use ApiResponse;

    public function getEvaluations()
    {
        $evaluation = Evaluation::with('category')->get();
        return $this->data($evaluation);
    }

    public function index(Request $request)
    {
        $evaluation = Evaluation::with('category')->withCount('category')->when($request->search, fn ($query, $search)
            => $query->where('evaluation', 'like', '%' . $search . '%')
            ->orWhereRelation('category', 'category', 'like', '%'. $search .'%'))
        ->paginate($request->per_page);

        return $this->data($evaluation);
    }

    public function store(EvaluationRequest $request)
    {
        $evaluation = Evaluation::create($request->validated());

        foreach($request->categories as $categ) {
            EvaluationCategory::create(['evaluation_id' => $evaluation->id, 'category' => $categ['category']]);
        }

        return $this->success('Evaluation has been created successfully!');
    }

    public function update(EvaluationRequest $request, Evaluation $evaluation)
    {
        $evaluation->load('category');
        $evaluation->update($request->validated());
        $evaluation->category()->delete();

        foreach($request->category as $categ) {
            EvaluationCategory::create(['evaluation_id' => $evaluation->id, 'category' => $categ['category']]);
        }

        return $this->success('Evaluation has been updated successfully!');
    }

    public function destroy(Evaluation $evaluation)
    {
        $evaluation->delete();
        return $this->success('Evaluation has been removed successfully!');
    }
}
