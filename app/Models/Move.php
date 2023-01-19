<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player',
        'row',
        'col'
    ];

    public function create(){

    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
