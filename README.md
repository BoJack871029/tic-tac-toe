# Requirements
- docker desktop
- git

# Clone repo
# How to run
```bash
cd <project_dir>
./vendor/bin/sail up -d
```

# Run tests

```bash
./vendor/bin/sail test
```


# API
> POST api/game/create

Create a new game.

Sample response
```json
{
    "token": "def6a1d4-5e99-485a-a6e3-a66384ec2aad",
    "created_at": "2023-01-20T16:40:44.000000Z",
    "updated_at": "2023-01-20T16:40:44.000000Z"
}
```
>POST api/game/move

Make a move on given game. Request parameters:
- token: game to play
- player: player who is making the move. Can be either 1 or 2
- row: board row index where to make the move. Can be [0,1,2]
- col: board col index where to make the move. Can be [0,1,2]

Sample response
```json
{
    "board": [
        [
            0,
            0,
            -1
        ],
        [
            0,
            0,
            0
        ],
        [
            0,
            0,
            0
        ]
    ],
    "error": "error-message",
    "winner": 0
}
```
- board: 2D array representing the board game. Each item represent a row
- winner: states if there is a winner. Can be
    - 0: no winner
    - 1: player 1 won
    - 2: player 2 won
- error: optional. If there was an error, it will return the error message. Possible values:
    - "Not your turn!";
    - "Move already taken!";
    - "This game has already a winner!";
    - "This game is complete. No other moves available";

# How to play
Here few curl call example:

Create a new game
```bash
curl -X POST http://localhost/api/game/create  -H "Content-Type: application/json" -H "Accept: application/json"
```
Player 1 play on (0,0)
```bash
curl -X POST http://localhost/api/game/move  -H "Content-Type: application/json" -H "Accept: application/json" -d '{"token": "58d412e4-e083-4441-8e7f-3379633e31ec", "player": 1, "row":0, "col": 0}'
```

Player 2 play on (0,1)
```bash
curl -X POST http://localhost/api/game/move  -H "Content-Type: application/json" -H "Accept: application/json" -d '{"token": "58d412e4-e083-4441-8e7f-3379633e31ec", "player": 2, "row":0, "col": 1}'
```

PLayer 1 play on (2,2)
```bash
 curl -X POST http://localhost/api/game/move  -H "Content-Type: application/json" -H "Accept: application/json" -d '{"token": "58d412e4-e083-4441-8e7f-3379633e31ec", "player": 1, "row":2, "col": 2}'
```

Player 2 play on (0,2)
```bash
curl -X POST http://localhost/api/game/move  -H "Content-Type: application/json" -H "Accept: application/json" -d '{"token": "58d412e4-e083-4441-8e7f-3379633e31ec", "player": 2, "row":0, "col": 2}'
```

Player 1 play on (1,1). Player 1 win!
```bash
 curl -X POST http://localhost/api/game/move  -H "Content-Type: application/json" -H "Accept: application/json" -d '{"token": "58d412e4-e083-4441-8e7f-3379633e31ec", "player": 1, "row":1, "col": 1}'
```