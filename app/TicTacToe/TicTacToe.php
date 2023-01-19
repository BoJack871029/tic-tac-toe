<?php

namespace App\TicTacToe;

use App\Models\Game;
use App\Models\Move;

enum Errors: string
{
    case NotYourTurn = "Not your turn!";
    case MoveTaken = "Move already taken!";
}

class TicTacToe
{
    private $game;
    private $move;
    public $board = [[0, 0, 0], [0, 0, 0], [0, 0, 0]];
    private $lastPlayer = 0;
    public $error;

    public function __construct(Game $game, Move $move, $board = null, $lastPlayer = 0)
    {
        $this->game = $game;
        $this->move = $move;
        if ($board != null) $this->board = $board;
        $this->lastPlayer = $lastPlayer;
    }

    public function init($gameId)
    {
        $this->game = $this->game->find($gameId);
        foreach ($this->game->moves->sortByDesc('id') as $move) {
            $playerSymbol = $this->getPlayerSymbol($move->player);
            $this->board[$move->row][$move->col] = $playerSymbol;
        }

        if ($this->game->moves->count() > 0)
            $this->lastPlayer = $this->game->moves->sortByDesc('id')->first()->player;
    }



    public function move($player, $row, $col)
    {
        if (!$this->isPlayerTurn($player)) {
            $this->error = Errors::NotYourTurn;
            return false;
        }

        if (!$this->isFree($row, $col)) {
            $this->error = Errors::MoveTaken;
            return false;
        }

        $this->move->game_id = $this->game->id;
        $this->move->player = $player;
        $this->move->row = $row;
        $this->move->col = $col;
        $this->move->save();
        return true;
    }

    public function isFree($row, $col)
    {
        return $this->board[$row][$col] == 0;
    }

    public function isPlayerTurn($player)
    {
        if ($this->lastPlayer == 0) return true;
        if ($this->lastPlayer == $player) return false;
        return true;
    }

    private function getPlayerSymbol($player)
    {
        return $player == 1 ? 1 : -1;
    }
}
