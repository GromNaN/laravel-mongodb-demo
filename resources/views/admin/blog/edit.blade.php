@extends('admin.layout')

@section('content')
    <div class="container body-container">
        <div class="row">
            <div id="main" class="col-sm-9">
                <div class="messages"></div>
                <h1>Edit article #{{ $post->id }}</h1>
                <form action="{{ route('admin_post_update', $post) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" name="title" id="title" class="form-control" required value="{{ $post->title }}">
                    </div>

                    <div class="form-group">
                        <label for="summary">Summary:</label>
                        <textarea name="summary" id="summary" rows="3" class="form-control" required>{{ $post->summary }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="content">Content:</label>
                        <textarea name="content" id="content" rows="6" class="form-control" required>{{ $post->content }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="published_at">Published At:</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control" required value="{{ $post->published_at->format('Y-m-d\TH:i') }}">
                    </div>

                    <div class="form-group">
                        <label for="tags">Tags:</label>
                        <input type="text" name="tags" id="tags" class="form-control" value="{{ implode(', ', $post->tags()->pluck('name')->toArray())  }}">
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fa fa-save" aria-hidden="true"></i> Save</button>

                    <a href="{{ route('admin_post_index') }}" class="btn btn-link">
                        <i class="fa fa-list-alt" aria-hidden="true"></i> Back to article list
                    </a>
                </form>
            </div>
            <div id="sidebat" class="col-sm-3">
                <div class="section">
                    <form action="{{ route('admin_post_delete', $post) }}" method="DELETE">
                        <button type="submit" class="btn btn-lg btn-block btn-danger">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
