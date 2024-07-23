<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/tokens/create', function (Request $request) {
    $user = \App\Models\User::first();
    $token = $user->createToken('token-name');
    //$token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

Route::get('/tokens/list', function (Request $request) {
    $user = \App\Models\PersonalAccessToken::findToken($request->get('access_token'))
        ->tokenable()
        ->first(['name', 'created_at', 'expires_at', 'abilities']);

    $tokens = \App\Models\PersonalAccessToken::where('tokenable_id', $user->id)->get();

    return ['tokens' => $tokens];
});

Route::get('/users', function (Request $request) {
    return ['users' => \App\Models\User::all()];
})->middleware(['auth:sanctum', 'abilities:list-users']);
