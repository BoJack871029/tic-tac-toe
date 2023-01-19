<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Move;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GameMoveTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_receives_an_error_when_try_to_make_a_move_without_token_player_row_and_col()
    {
        $response = $this->post(route('game-move'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['token', 'player', 'row', 'col']);
    }

    public function test_user_cant_make_a_move_when_provides_an_unknown_game_token()
    {
        Game::factory()->create();
        $response = $this->post(route('game-move'), ['token' => 'invalid-token', 'player' => 1, 'row' => 1, 'col' => 2]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['token']);
    }

    public function test_user_cant_make_a_move_with_a_player_different_from_1_or_2()
    {
        $game = Game::factory()->create();
        $response = $this->post(route('game-move'), ['token' => $game->token, 'player' => 3, 'row' => 1, 'col' => 2]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['player']);
    }

    public function test_user_cant_make_a_move_with_a_row_different_from_0_or_1_or_2()
    {
        $game = Game::factory()->create();
        $response = $this->post(route('game-move'), ['token' => $game->token, 'player' => 2, 'row' => 3, 'col' => 2]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['row']);
    }

    public function test_user_cant_make_a_move_with_a_col_different_from_0_or_1_or_2()
    {
        $game = Game::factory()->create();
        $response = $this->post(route('game-move'), ['token' => $game->token, 'player' => 2, 'row' => 0, 'col' => 3]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['col']);
    }

    public function test_user_receive_error_message_when_makes_already_taken_move()
    {
        $game = Game::factory()->create();
        Move::factory()->create(['game_id' => $game->id, 'player' => 1, 'row' => 1, 'col' => 1]);
        $response = $this->post(route('game-move'), ['token' => $game->token, 'player' => 2, 'row' => 1, 'col' => 1]);
        $response->assertStatus(400);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('error')
                ->etc()
        );
    }

    public function test_user_receive_error_message_when_not_his_turn()
    {
        $game = Game::factory()->create();
        Move::factory()->create(['game_id' => $game->id, 'player' => 1, 'row' => 1, 'col' => 1]);
        $response = $this->post(route('game-move'), ['token' => $game->token, 'player' => 1, 'row' => 1, 'col' => 1]);
        $response->assertStatus(400);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('error')
                ->etc()
        );
    }

    public function test_when_there_is_a_winner_returns_the_winner_player()
    {
        $game = Game::factory()->create();
        Move::factory()->create(['game_id' => $game->id, 'player' => 2, 'row' => 0, 'col' => 0]);
        Move::factory()->create(['game_id' => $game->id, 'player' => 2, 'row' => 1, 'col' => 1]);
        Move::factory()->create(['game_id' => $game->id, 'player' => 1, 'row' => 0, 'col' => 1]);

        $response = $this->post(route('game-move'), ['token' => $game->token, 'player' => 2, 'row' => 2, 'col' => 2]);
        $response->assertStatus(200);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->where('winner', 2)
                ->etc()
        );
    }
}
