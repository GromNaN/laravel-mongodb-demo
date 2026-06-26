<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/forum/{forum}', [ForumController::class, 'show'])->name('forum.show');
Route::get('/topic/{topic}', [TopicController::class, 'show'])->name('topic.show');
Route::get('/topic/{topic}/new-post', [TopicController::class, 'newPost'])->name('topic.new-post');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/user/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/userlist', [UserController::class, 'index'])->name('user.index');
Route::get('/help', [HelpController::class, 'index'])->name('help.index');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Auth routes
Route::middleware('auth')->group(function () {
    Route::get('/post/new/{forum}', [PostController::class, 'create'])->name('post.create');
    Route::post('/post/new/{forum}', [PostController::class, 'store'])->name('post.store');
    Route::get('/post/reply/{topic}', [PostController::class, 'reply'])->name('post.reply');
    Route::post('/post/reply/{topic}', [PostController::class, 'storeReply'])->name('post.storeReply');
    Route::get('/post/{post}/edit', [PostController::class, 'edit'])->name('post.edit');
    Route::put('/post/{post}', [PostController::class, 'update'])->name('post.update');
    Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');
    Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/topic/{topic}/subscribe', [TopicController::class, 'subscribe'])->name('topic.subscribe');
    Route::delete('/topic/{topic}/subscribe', [TopicController::class, 'unsubscribe'])->name('topic.unsubscribe');
    Route::post('/forum/{forum}/subscribe', [ForumController::class, 'subscribe'])->name('forum.subscribe');
    Route::delete('/forum/{forum}/subscribe', [ForumController::class, 'unsubscribe'])->name('forum.unsubscribe');
    Route::post('/mark-read', [HomeController::class, 'markAllRead'])->name('home.markRead');
    Route::post('/forum/{forum}/mark-read', [ForumController::class, 'markRead'])->name('forum.markRead');
});

// Moderator routes
Route::middleware(['auth', 'can:moderate'])->group(function () {
    Route::post('/topic/{topic}/close', [ModeratorController::class, 'closeTopic'])->name('topic.close');
    Route::post('/topic/{topic}/open', [ModeratorController::class, 'openTopic'])->name('topic.open');
    Route::post('/topic/{topic}/stick', [ModeratorController::class, 'stickTopic'])->name('topic.stick');
    Route::post('/topic/{topic}/unstick', [ModeratorController::class, 'unstickTopic'])->name('topic.unstick');
    Route::post('/topic/{topic}/move', [ModeratorController::class, 'moveTopic'])->name('topic.move');
    Route::delete('/topic/{topic}', [ModeratorController::class, 'deleteTopic'])->name('topic.delete');
    Route::post('/report/{post}', [ModeratorController::class, 'reportPost'])->name('post.report');
});

// Admin routes
Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/bans', [Admin\BanController::class, 'index'])->name('bans.index');
    Route::post('/bans', [Admin\BanController::class, 'store'])->name('bans.store');
    Route::delete('/bans/{ban}', [Admin\BanController::class, 'destroy'])->name('bans.destroy');

    Route::get('/forums', [Admin\ForumController::class, 'index'])->name('forums.index');
    Route::post('/forums', [Admin\ForumController::class, 'store'])->name('forums.store');
    Route::get('/forums/{forum}/edit', [Admin\ForumController::class, 'edit'])->name('forums.edit');
    Route::put('/forums/{forum}', [Admin\ForumController::class, 'update'])->name('forums.update');
    Route::delete('/forums/{forum}', [Admin\ForumController::class, 'destroy'])->name('forums.destroy');
    Route::post('/forums/{forum}/moveup', [Admin\ForumController::class, 'moveup'])->name('forums.moveup');
    Route::post('/forums/{forum}/movedown', [Admin\ForumController::class, 'movedown'])->name('forums.movedown');

    Route::get('/categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{cat}', [Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{cat}', [Admin\CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{cat}/moveup', [Admin\CategoryController::class, 'moveup'])->name('categories.moveup');
    Route::post('/categories/{cat}/movedown', [Admin\CategoryController::class, 'movedown'])->name('categories.movedown');

    Route::get('/groups', [Admin\GroupController::class, 'index'])->name('groups.index');
    Route::put('/groups/{group}', [Admin\GroupController::class, 'update'])->name('groups.update');

    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/{report}/zap', [Admin\ReportController::class, 'zap'])->name('reports.zap');

    Route::get('/settings', [Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');

    Route::get('/censoring', [Admin\CensorController::class, 'index'])->name('censoring.index');
    Route::post('/censoring', [Admin\CensorController::class, 'store'])->name('censoring.store');
});
