<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Game;

class GameController extends Controller
{
    public function create(){
        $game = new Game;
        $game->save();
        return new GameResource($game);
    }
}
