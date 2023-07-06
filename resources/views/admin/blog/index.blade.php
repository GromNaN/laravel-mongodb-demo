@extends('admin.layout')

@section('content')
    <div class="container body-container">
        <div class="row">
            <div id="main" class="col-sm-9">
                <div class="messages"></div>
                <h1>Article list</h1>

                <label for="nbTags">Filter by number of tags :</label>
                <select onchange="window.location.href = this.value" id="nbTags" style="width: 3em">
                    <option value="{{ route('admin_post_index') }}" {{ $nbTags ? '' : 'selected' }}>&nbsp;</option>
                    @foreach($nbTagsChoices as $number)
                        <option value="{{ route('admin_post_index', ['nbTags' => $number]) }}" {{ $nbTags == $number ? 'selected' : '' }}>{{ $number }}</option>
                    @endforeach
                </select>

                <table class="table table-striped table-middle-aligned table-borderless">
                    <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col"><i class="fa fa-calendar" aria-hidden="true"></i> Published</th>
                        <th scope="col" class="text-center"><i class="fa fa-cogs" aria-hidden="true"></i> Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($paginator as $post)
                    <tr>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->published_at }}</td>
                        <td class="text-right">
                            <div class="item-actions">
                                <a href="{{ route('admin_post_edit', [$post]) }}" class="btn btn-sm btn-secondary">
                                    <i class="fa fa-eye" aria-hidden="true"></i> Show
                                </a>

                                <a href="{{ route('admin_post_edit', [$post]) }}" class="btn btn-sm btn-primary">
                                    <i clas s="fa fa-edit" aria-hidden="true"></i> Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="4" align="center">No posts found</td>
                        </tr>
                    @endforelse
                </table>

                {{ $paginator->links() }}
            </div>
            <div id="sidebar" class="col-sm-3">
                <div class="section actions">
                    <a href="{{ route('admin_post_new') }}" class="btn btn-lg btn-block btn-success">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Create a new article
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
