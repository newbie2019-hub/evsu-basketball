<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceEvaluation extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('F j, Y h:i A');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->hasMany(PerformanceCategory::class, 'per_eval_id', 'id');
    }
}
