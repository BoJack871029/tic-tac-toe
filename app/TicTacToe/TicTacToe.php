<?php

namespace App\TicTacToe;

use App\Models\Game;
use App\Models\Move;
use Exception;

enum Errors: string
{
    case NotYourTurn = "Not your turn!";
    case MoveTaken = "Move already taken!";
    case AlreadyWon = "This game has already a winner!";
    case BoardComplete = "This game is complete. No other moves available";
}

class TicTacToe
{
    private $game;
    private $move;
    public $board = [[0, 0, 0], [0, 0, 0], [0, 0, 0]];
    private $lastPlayer = 0;
    public $error;
    public $winner = 0;
    private $winnerSum = 0;

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
        if ($this->thereIsWinner()) {
            $this->winner = $this->getWinner();
            $this->error = Errors::AlreadyWon;
            return false;
        }

        if ($this->isBoardComplete()) {
            $this->error = Errors::BoardComplete;
            return false;
        }

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
        $this->board[$row][$col] = $this->getPlayerSymbol($player);
        if ($this->thereIsWinner()) {
            $this->winner = $this->getWinner();
        }
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

    public function getPlayerSymbol($player)
    {
        return $player == 1 ? 1 : -1;
    }

    public function thereIsWinner()
    {
        return ($this->isWinnerByRow() || $this->isWinnerByCol() || $this->isWinnerByOblique());
    }

    private function isWinnerByRow()
    {
        foreach ($this->board as $row) {
            $sum = array_sum($row);
            if (abs($sum) == 3) {
                $this->winnerSum = $sum;
                return true;
            }
        }
        return false;
    }

    private function isWinnerByCol()
    {
        for ($col = 0; $col < 3; $col++) {
            $sum = 0;
            for ($row = 0; $row < 3; $row++) {
                $sum += $this->board[$row][$col];
            }
            if (abs($sum) == 3) {
                $this->winnerSum = $sum;
                return true;
            }
            $sum = 0;
        }
        return false;
    }

    private function isWinnerByOblique()
    {
        $sum = 0;
        for ($i = 0; $i < 3; $i++) {
            $sum += $this->board[$i][$i];
        }

        if (abs($sum) == 3) {
            $this->winnerSum = $sum;
            return true;
        }

        $sum = $this->board[2][0] + $this->board[1][1] + $this->board[0][2];
        if (abs($sum) == 3) {
            $this->winnerSum = $sum;
            return true;
        }

        return false;
    }

    private function getWinner()
    {
        if ($this->winnerSum == 3) return 1;
        if ($this->winnerSum == -3) return 2;
        throw new Exception("There is no winner yet!");
    }

    private function isBoardComplete()
    {
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                if ($this->board[$row][$col] == 0) return false;
            }
        }
        return true;
    }
}
