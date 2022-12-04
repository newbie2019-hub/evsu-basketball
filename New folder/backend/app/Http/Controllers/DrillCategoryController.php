<?php

namespace App\Http\Controllers;

use App\Http\Requests\DrillCategoryRequest;
use App\Models\DrillCategory;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;


class DrillCategoryController extends Controller
{
    use ApiResponse;

    public function all()
    {
        $categories = DrillCategory::get();
        return $this->data($categories);
    }

    public function index(Request $request)
    {
        $category = DrillCategory::withCount('drills')->when($request->search, fn ($query, $search)
            => $query->where('category', 'like', '%' . $search . '%'))
        ->paginate($request->per_page);

        return $this->data($category);
    }

    public function store(DrillCategoryRequest $request)
    {
        DrillCategory::create($request->validated());
        return $this->success('Category created successfully!');
    }

    public function update(DrillCategoryRequest $request, DrillCategory $category)
    {
        $category->update($request->validated());
        return $this->success('Category has been updated successfully!');
    }

    public function destroy(DrillCategory $category)
    {
        $category->load(['drills']);

        $category->drills->each(function($drill){
            $drill->update(['drill_category_id' => null]);
        });

        $category->delete();

        return $this->success('Category data was removed successfully!');
    }
}
