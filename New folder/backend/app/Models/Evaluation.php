<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function category()
    {
        return $this->hasMany(EvaluationCategory::class, 'evaluation_id', 'id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('F j, Y h:i A');
    }
}
