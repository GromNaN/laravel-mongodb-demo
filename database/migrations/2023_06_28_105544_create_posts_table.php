<?php

use Illuminate\Database\Migrations\Migration;;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $collection) {
            $collection->id();
            $collection->timestamps();
            $collection->string('title');
            $collection->string('slug')->unique();
            $collection->text('summary');
            $collection->text('content');
            $collection->date('published_at');
            $collection->date('deleted_at');
            $collection->integer('author_id')->unsigned();
            $collection->foreign('author_id')
                ->references('id')->on('users')
                ->onDelete('restrict');
            $collection->index(['title' => 'text']);
            $collection->index('published_at');
            $collection->index('deleted_at');
        });

        Schema::create('comments', function (Blueprint $collection) {
            $collection->id();
            $collection->timestamps();
            $collection->text('content');
            $collection->date('published_at');
            $collection->integer('author_id')->unsigned();
            $collection->foreign('author_id')
                ->references('id')->on('users')
                ->onDelete('restrict');
            $collection->integer('post_id')->unsigned();
            $collection->foreign('post_id')
                ->references('id')->on('posts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');
    }
};
