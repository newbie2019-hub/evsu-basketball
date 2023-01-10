<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerDrillLog extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function drill()
    {
        return $this->belongsTo(GameDrill::class, 'game_drill_id', 'id');
    }
}
