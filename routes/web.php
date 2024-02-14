<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/blog/', [App\Http\Controllers\BlogController::class, 'index'])->name('blog_index');
Route::get('/blog/rss.xml', [App\Http\Controllers\BlogController::class, 'index'])->name('blog_rss');
Route::get('/blog/page/page<[1-9]\d{0,8}>}', [App\Http\Controllers\BlogController::class, 'index'])->name('blog_index_paginated');
Route::get('/blog/post/{slug}', [App\Http\Controllers\BlogController::class, 'postShow'])->name('blog_post');
Route::post('/blog/post/{id}/comments', [App\Http\Controllers\BlogController::class, 'commentNew'])->name('blog_post_comment_new')->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
