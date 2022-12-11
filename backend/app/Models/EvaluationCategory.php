<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function score()
    {
        return $this->hasOne(PerformanceCategory::class, 'evaluation_categories_id', 'id');
    }

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class, 'evaluation_id', 'id');
    }
}
