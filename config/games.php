<?php

/*
|--------------------------------------------------------------------------
| Online game servers
|--------------------------------------------------------------------------
|
| The board games in public/{chaukabaara,aadupuliatam,…} connect to a
| WebSocket server for online rooms; without one they still play hot-seat.
| The React front-end read a Vite env var per game (VITE_CHAUK_SERVER,
| VITE_AP_SERVER, …). Those become the *_SERVER values below, with
| PP_GAMES_SERVER as the fallback for every game.
|
*/

return [

    'default_server' => env('PP_GAMES_SERVER', 'ws://localhost:8377'),

    'servers' => [
        'chaukabaara' => env('CHAUKABAARA_SERVER'),
        'aadu-puli-aatam' => env('AADU_PULI_AATAM_SERVER'),
        'chaturvimshati' => env('CHATURVIMSHATI_SERVER'),
        'vish-amrit' => env('VISH_AMRIT_SERVER'),
    ],

];
