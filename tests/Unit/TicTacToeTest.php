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

}
