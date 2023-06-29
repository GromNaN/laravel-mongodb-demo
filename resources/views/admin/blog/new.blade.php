@extends('admin.layout')

@section('content')
    <div class="container body-container">
        <div class="row">
            <div id="main" class="col-sm-9">
                <div class="messages"></div>
                <h1>Add a new article</h1>
                <form action="{{ route('admin_post_create') }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" name="title" id="title" class="form-control" required value="">
                    </div>

                    <div class="form-group">
                        <label for="summary">Summary:</label>
                        <textarea name="summary" id="summary" rows="3" class="form-control" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="content">Content:</label>
                        <textarea name="content" id="content" rows="6" class="form-control" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="published_at">Published At:</label>
                        <input type="datetime-local" name="published_at" id="published_at" class="form-control" required value="{{ (new DateTimeImmutable())->format('Y-m-d\TH:i') }}">
                    </div>

                    <div class="form-group">
                        <label for="tags">Tags:</label>
                        <input type="text" name="tags" id="tags" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fa fa-save" aria-hidden="true"></i> Create</button>

                    <a href="{{ route('admin_post_index') }}" class="btn btn-link">
                        <i class="fa fa-list-alt" aria-hidden="true"></i> Back to article list
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection
