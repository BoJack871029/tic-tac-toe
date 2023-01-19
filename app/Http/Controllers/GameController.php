<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameMoveRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\GameResultResource;
use App\Models\Game;
use App\Models\Move;
use App\TicTacToe\TicTacToe;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    private $ticTacToe;

    public function __construct(TicTacToe $ticTacToe)
    {
        $this->ticTacToe = $ticTacToe;
    }
    public function create()
    {
        Log::info("New game request");
        $game = new Game;
        $game->save();
        Log::info("New game created: $game->token");
        return new GameResource($game);
    }

    public function move(GameMoveRequest $request)
    {
        Log::info("New game move request: " . json_encode($request->post()));

        $game = Game::where('token', $request->post('token'))->first();
        Log::debug("Found game with id: $game->id");

        $move = new Move($request->post());
        $move->game_id = $game->id;

        $this->ticTacToe->init($game->id);

        if (!$this->ticTacToe->move($request->player, $request->row, $request->col)) {
            return response()->json(new GameResultResource($this->ticTacToe), 400);
        }
        
        Log::info("Move request created: " . json_encode($move));
        return response()->json(new GameResultResource($this->ticTacToe), 200);
    }
}
