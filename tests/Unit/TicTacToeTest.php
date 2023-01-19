<?php

use App\Models\Game;
use App\Models\Move;
use App\TicTacToe\Errors;
use App\TicTacToe\TicTacToe;
use Tests\TestCase;

class TicTacToeTest extends TestCase
{
    private $gameMock;
    private $moveMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gameMock = Mockery::mock(Game::class);
        $this->moveMock = Mockery::mock(Move::class);
    }
    public function test_user_can_always_make_first_move_as_player_1()
    {
        $fakeGame = new Game(['id' => 1]);
        $this->gameMock->shouldReceive('find')->with(1)->once()->andReturn($fakeGame);
        $this->moveMock->shouldReceive('setAttribute')->andReturnSelf();
        $this->moveMock->shouldReceive('save')->once();

        $sut = new TicTacToe($this->gameMock, $this->moveMock);

        $sut->init(gameId: 1);
        $ret = $sut->move(player: 1, row: 0, col: 0);
        $this->assertTrue($ret);
    }

    public function test_user_can_always_make_first_move_as_player_2()
    {
        $fakeGame = new Game(['id' => 1]);
        $this->gameMock->shouldReceive('find')->with(1)->once()->andReturn($fakeGame);
        $this->moveMock->shouldReceive('setAttribute')->andReturnSelf();
        $this->moveMock->shouldReceive('save');

        $sut = new TicTacToe($this->gameMock, $this->moveMock);
        $sut->init(gameId: 1);
        $ret = $sut->move(player: 2, row: 0, col: 0);
        $this->assertTrue($ret);
    }

    public function test_user_cant_play_twice_in_a_row()
    {
        $fakeGame = new Game(['id' => 1]);
        $this->gameMock->shouldReceive('find')->with(1)->once()->andReturn($fakeGame);
        $this->moveMock->shouldReceive('setAttribute')->andReturnSelf();
        $this->moveMock->shouldReceive('save');

        $sut = new TicTacToe($this->gameMock, $this->moveMock, null, 2);
        $sut->init(gameId: 1);
        $ret = $sut->move(player: 2, row: 0, col: 0);
        $this->assertFalse($ret);
        $this->assertEquals($sut->error, Errors::NotYourTurn);
    }

    public function test_user_cant_make_move_if_already_taken()
    {
        $fakeGame = new Game(['id' => 1]);
        $this->gameMock->shouldReceive('find')->with(1)->once()->andReturn($fakeGame);
        $this->moveMock->shouldReceive('setAttribute')->andReturnSelf();
        $this->moveMock->shouldReceive('save');

        $board = [[1, 0, 0], [0, 0, 0], [0, 0, 0]];
        $sut = new TicTacToe($this->gameMock, $this->moveMock, $board);
        $sut->init(gameId: 1);
        $ret = $sut->move(player: 2, row: 0, col: 0);
        $this->assertFalse($ret);
        $this->assertEquals($sut->error, Errors::MoveTaken);
    }

    /**
     * @dataProvider provideBoardWithWinnerByRow
     */
    public function test_user_win_when_makes_horizontal_line($board)
    {
        $sut = new TicTacToe($this->gameMock, $this->moveMock, $board, lastPlayer: 2);
        $ret = $sut->thereIsWinner();
        $this->assertTrue($ret);
    }

    public function provideBoardWithWinnerByRow()
    {
        return [
            [
                [[1, 1, 1], [0, 0, 0], [0, 0, 0]], 1
            ],
            [
                [[0, 0, 0], [-1, -1, -1], [0, 0, 0]], 2
            ],
            [
                [[0, 0, 0], [0, 0, 0], [1, 1, 1]], 1
            ],
        ];
    }

    /**
     * @dataProvider provideBoardWithWinnerByCol
     */
    public function test_user_win_when_makes_vertical_line($board)
    {
        $sut = new TicTacToe($this->gameMock, $this->moveMock, $board, lastPlayer: 2);
        $ret = $sut->thereIsWinner();
        $this->assertTrue($ret);
    }

    public function provideBoardWithWinnerByCol()
    {
        return [
            [
                [[1, 0, 0], [1, 0, 0], [1, 0, 0]]
            ],
            [
                [[0, 1, 0], [0, 1, 0], [0, 1, 0]]
            ],
            [
                [[0, 0, 1], [0, 0, 1], [0, 0, 1]]
            ],
        ];
    }

    /**
     * @dataProvider provideBoardWithWinnerOblique
     */
    public function test_user_win_when_makes_oblique_line($board)
    {
        $sut = new TicTacToe($this->gameMock, $this->moveMock, $board, lastPlayer: 2);
        $ret = $sut->thereIsWinner();
        $this->assertTrue($ret);
    }

    public function provideBoardWithWinnerOblique()
    {
        return [
            [
                [[1, 0, 0], [0, 1, 0], [0, 0, 1]]
            ],
            [
                [[0, 0, 1], [0, 1, 0], [1, 0, 0]]
            ]
        ];
    }

    public function test_user_cant_make_a_move_on_already_won_game()
    {
        $fakeGame = new Game(['id' => 1]);
        $this->gameMock->shouldReceive('find')->with(1)->once()->andReturn($fakeGame);
        $this->moveMock->shouldReceive('setAttribute')->andReturnSelf();
        $this->moveMock->shouldReceive('save');

        $board = [[1, 0, 0], [0, 1, 0], [0, 0, 1]];
        $sut = new TicTacToe($this->gameMock, $this->moveMock, $board, lastPlayer: 2);
        $sut->init(gameId: 1);
        $ret = $sut->move(player: 1, row: 0, col: 1);
        $this->assertFalse($ret);
        $this->assertEquals(Errors::AlreadyWon, $sut->error);
    }

    public function test_user_cant_make_a_move_on_complete_game()
    {
        $fakeGame = new Game(['id' => 1]);
        $this->gameMock->shouldReceive('find')->with(1)->once()->andReturn($fakeGame);
        $this->moveMock->shouldReceive('setAttribute')->andReturnSelf();
        $this->moveMock->shouldReceive('save');

        $board = [[1, -1, -1], [-1, -1, 1], [1, 1, -1]];
        $sut = new TicTacToe($this->gameMock, $this->moveMock, $board, lastPlayer: 2);
        $sut->init(gameId: 1);
        $ret = $sut->move(player: 1, row: 0, col: 1);
        $this->assertFalse($ret);
        $this->assertEquals(Errors::BoardComplete, $sut->error);
    }

}
