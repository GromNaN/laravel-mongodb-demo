<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/session', function(Request $request) {
    $request->session()->put('name', 'John Doe ' . time());

    return $request->session()->all();
});

require __DIR__.'/auth.php';
