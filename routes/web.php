<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

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

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index']);

Route::get('/blog/', [BlogController::class, 'index'])->name('blog_index');
Route::get('/blog/rss.xml', [BlogController::class, 'index'])->name('blog_rss');
Route::get('/blog/page/page<[1-9]\d{0,8}>}', [BlogController::class, 'index'])->name('blog_index_paginated');
Route::get('/blog/post/{slug}', [BlogController::class, 'postShow'])->name('blog_post');
Route::post('/blog/post/{id}/comments', [BlogController::class, 'commentNew'])->name('blog_post_comment_new')->middleware('auth');
Route::get('/blog/search', [BlogController::class, 'search'])->name('blog_search');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/admin/post/', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('admin_post_index');
    Route::get('/admin/post/new', [App\Http\Controllers\Admin\BlogController::class, 'new'])->name('admin_post_new');
    Route::post('/admin/post/', [App\Http\Controllers\Admin\BlogController::class, 'create'])->name('admin_post_create');
    Route::get('/admin/post/{id}/edit', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('admin_post_edit');
    Route::put('/admin/post/{id}', [App\Http\Controllers\Admin\BlogController::class, 'update'])->name('admin_post_update');
    Route::get('/admin/post/{id}/delete', [App\Http\Controllers\Admin\BlogController::class, 'delete'])->name('admin_post_delete');
});

require __DIR__.'/auth.php';
