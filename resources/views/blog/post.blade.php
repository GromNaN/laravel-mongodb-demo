@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h1>{{ $post->title }}</h1>
                        Published <time>{{ $post->published_at }}</time> by {{ $post->author->name }}
                    </div>
                    <div class="card-body">
                        <x-markdown>{{ $post->content }}</x-markdown>
                        <h5>Tags</h5>
                        <ul>
                            @foreach($post->tags as $tag)
                                <li>{{ $tag->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Leave a comment</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('blog_post_comment_new', $post) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="content">Comment:</label>
                                <textarea name="content" id="content" rows="5" class="form-control"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit Comment</button>
                        </form>
                    </div>
                </div>

                <h2>Comments</h2>
                @foreach($post->comments as $comment)
                <div class="card">
                    <div class="card-header">
                        <strong>{{ $comment->author->name }}</strong>
                        commented on
                        <time>{{ $comment->published_at }}</time>
                    </div>
                    <div class="card-body">
                        <x-markdown>{{ $comment->content }}</x-markdown>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
