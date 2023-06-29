<?php

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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/blog/', [App\Http\Controllers\BlogController::class, 'index'])->name('blog_index');
Route::get('/blog/rss.xml', [App\Http\Controllers\BlogController::class, 'index'])->name('blog_rss');
Route::get('/blog/page/page<[1-9]\d{0,8}>}', [App\Http\Controllers\BlogController::class, 'index'])->name('blog_index_paginated');
Route::get('/blog/post/{slug}', [App\Http\Controllers\BlogController::class, 'postShow'])->name('blog_post');
Route::post('/blog/post/{id}/comments', [App\Http\Controllers\BlogController::class, 'commentNew'])->name('blog_post_comment_new')->middleware('auth');

Route::get('/admin/post/', [App\Http\Controllers\Admin\BlogController::class, 'index'])->name('admin_post_index');
Route::get('/admin/post/new', [App\Http\Controllers\Admin\BlogController::class, 'new'])->name('admin_post_new');
Route::post('/admin/post/', [App\Http\Controllers\Admin\BlogController::class, 'create'])->name('admin_post_create');
Route::get('/admin/post/{id}/edit', [App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('admin_post_edit');
Route::put('/admin/post/{id}', [App\Http\Controllers\Admin\BlogController::class, 'update'])->name('admin_post_update');
Route::get('/admin/post/{id}/delete', [App\Http\Controllers\Admin\BlogController::class, 'delete'])->name('admin_post_delete');
