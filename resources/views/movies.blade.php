<!DOCTYPE html>
<html>
<head>
    <title>Laravel - Laravel Scout MongoDB Search Example</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="text-bold">Laravel Full-Text Search Using Scout </h2><br/>

    <div class="panel panel-primary">
        <div class="panel-heading">Movies</div>
        <div class="panel-body">
            <form method="GET" action="{{ route('movies.index') }}">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ old('search') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <button class="btn btn-primary">Search</button>
                        </div>
                    </div>
                </div>
            </form>

            <table class="table">
                <thead>
                <th>Id</th>
                <th>Title</th>
                </thead>
                <tbody>
                @if($movies->count())
                    @foreach($movies as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $item->title }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">No train data available</td>
                    </tr>
                @endif
                </tbody>
            </table>
            {{ $movies->links() }}
        </div>
    </div>
</div>
</body>
</html>
