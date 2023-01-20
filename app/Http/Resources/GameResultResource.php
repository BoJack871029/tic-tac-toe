<?php

namespace App\Http\Resources;

use App\TicTacToe\Errors;
use App\TicTacToe\TicTacToe;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResultResource
{
    public static $wrap = null;

    public $board;
    public $winner;

    public function __construct(TicTacToe $ticTacToe)
    {
        $this->board = $ticTacToe->board;
        if ($ticTacToe->error != null) {
            $this->error = $ticTacToe->error;
        }
        $this->winner = $ticTacToe->winner;
    }
}
