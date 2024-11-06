<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/movies', [\App\Http\Controllers\MovieSearchController::class, 'index'])->name('movies.index');
