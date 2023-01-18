<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    public function test_when_user_creates_a_game_a_new_game_available_with_token()
    {
        $response = $this->post(route('game-create'));

        $json = json_decode($response->getContent());
        $token = $json->token;
        $response->assertStatus(201);
        $this->assertDatabaseHas('games', ['token' => $token]);
    }

    public function test_when_user_creates_a_new_game_api_returns_game_token_as_part_of_response()
    {
        $response = $this->post(route('game-create'));
        $response->assertStatus(201);

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->has('token')
                ->etc()
        );
    }
    public function test_two_users_receives_different_tokens_when_create_games()
    {
        $response = $this->post(route('game-create'));
        $response->assertStatus(201);
        $json = json_decode($response->getContent());
        $tokenA = $json->token;

        $response = $this->post(route('game-create'));
        $response->assertStatus(201);
        $json = json_decode($response->getContent());
        $tokenB = $json->token;

        $this->assertNotEquals($tokenA,$tokenB);
    }
}
