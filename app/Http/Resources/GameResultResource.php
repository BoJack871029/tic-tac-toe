<?php

namespace App\Http\Resources;

use App\TicTacToe\Errors;
use App\TicTacToe\TicTacToe;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResultResource
{
    public static $wrap = null;

    public $board;
    public $error;

    public function __construct(TicTacToe $ticTacToe)
    {
        $this->board = $ticTacToe->board;
        $this->error = $ticTacToe->error;
    }
}
