@extends('layouts.app')

@section('title', 'Search')

@section('content')

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
        Search the Forum
    </div>

    <div class="p-6">
        <form method="GET" action="{{ route('search.index') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left column --}}
                <div class="space-y-4">
                    {{-- Keywords --}}
                    <div>
                        <label for="keywords" class="block text-sm font-semibold text-gray-700 mb-1">Keywords</label>
                        <input type="text" id="keywords" name="keywords"
                               value="{{ old('keywords', request('keywords')) }}"
                               placeholder="Enter search terms..."
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                        <div class="text-xs text-gray-400 mt-1">
                            Separate multiple words with spaces. Prefix with + to require, - to exclude.
                        </div>
                    </div>

                    {{-- Author --}}
                    <div>
                        <label for="author" class="block text-sm font-semibold text-gray-700 mb-1">Posted by</label>
                        <input type="text" id="author" name="author"
                               value="{{ old('author', request('author')) }}"
                               placeholder="Username (use * as wildcard)"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                    </div>

                    {{-- Forum select --}}
                    <div>
                        <label for="forum_id" class="block text-sm font-semibold text-gray-700 mb-1">Forum</label>
                        <select id="forum_id" name="forum_id"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                            <option value="0">All forums</option>
                            @foreach($forums as $forum)
                                <option value="{{ $forum->id }}"
                                        {{ old('forum_id', request('forum_id')) == $forum->id ? 'selected' : '' }}>
                                    {{ $forum->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Right column --}}
                <div class="space-y-4">
                    {{-- Date range --}}
                    <div>
                        <label for="date_range" class="block text-sm font-semibold text-gray-700 mb-1">Date range</label>
                        <select id="date_range" name="date_range"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                            <option value="0" {{ old('date_range', request('date_range', '0')) == '0' ? 'selected' : '' }}>All time</option>
                            <option value="1" {{ old('date_range', request('date_range')) == '1' ? 'selected' : '' }}>Last day</option>
                            <option value="7" {{ old('date_range', request('date_range')) == '7' ? 'selected' : '' }}>Last week</option>
                            <option value="30" {{ old('date_range', request('date_range')) == '30' ? 'selected' : '' }}>Last month</option>
                            <option value="90" {{ old('date_range', request('date_range')) == '90' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="365" {{ old('date_range', request('date_range')) == '365' ? 'selected' : '' }}>Last year</option>
                        </select>
                    </div>

                    {{-- Search in --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Search in</label>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="search_in" value="all"
                                       {{ old('search_in', request('search_in', 'all')) === 'all' ? 'checked' : '' }}
                                       class="text-blue-600">
                                Topics and posts
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="search_in" value="topics"
                                       {{ old('search_in', request('search_in')) === 'topics' ? 'checked' : '' }}
                                       class="text-blue-600">
                                Topics only (subject)
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="radio" name="search_in" value="posts"
                                       {{ old('search_in', request('search_in')) === 'posts' ? 'checked' : '' }}
                                       class="text-blue-600">
                                Posts only (message)
                            </label>
                        </div>
                    </div>

                    {{-- Sort by --}}
                    <div>
                        <label for="sort_by" class="block text-sm font-semibold text-gray-700 mb-1">Sort results by</label>
                        <select id="sort_by" name="sort_by"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                            <option value="last_post" {{ old('sort_by', request('sort_by', 'last_post')) === 'last_post' ? 'selected' : '' }}>Last post date</option>
                            <option value="post_date" {{ old('sort_by', request('sort_by')) === 'post_date' ? 'selected' : '' }}>Post date</option>
                            <option value="replies" {{ old('sort_by', request('sort_by')) === 'replies' ? 'selected' : '' }}>Number of replies</option>
                            <option value="views" {{ old('sort_by', request('sort_by')) === 'views' ? 'selected' : '' }}>Number of views</option>
                        </select>
                    </div>

                    {{-- Sort order --}}
                    <div>
                        <label for="sort_dir" class="block text-sm font-semibold text-gray-700 mb-1">Sort direction</label>
                        <select id="sort_dir" name="sort_dir"
                                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                            <option value="desc" {{ old('sort_dir', request('sort_dir', 'desc')) === 'desc' ? 'selected' : '' }}>Descending (newest first)</option>
                            <option value="asc" {{ old('sort_dir', request('sort_dir')) === 'asc' ? 'selected' : '' }}>Ascending (oldest first)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="mt-6 flex items-center gap-3 border-t border-gray-100 pt-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm transition">
                    Search
                </button>
                <a href="{{ route('search.index') }}"
                   class="text-sm text-gray-600 hover:underline px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Quick links --}}
<div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <a href="{{ route('search.index', ['sort_by' => 'last_post', 'date_range' => '1']) }}"
       class="bg-white border border-gray-200 rounded px-4 py-3 text-sm text-blue-600 hover:bg-gray-50 hover:underline text-center transition">
        Posts from the last 24 hours
    </a>
    @auth
        <a href="{{ route('search.index', ['author' => auth()->user()->username, 'sort_by' => 'post_date']) }}"
           class="bg-white border border-gray-200 rounded px-4 py-3 text-sm text-blue-600 hover:bg-gray-50 hover:underline text-center transition">
            My recent posts
        </a>
    @endauth
    <a href="{{ route('search.index', ['sort_by' => 'replies', 'date_range' => '30']) }}"
       class="bg-white border border-gray-200 rounded px-4 py-3 text-sm text-blue-600 hover:bg-gray-50 hover:underline text-center transition">
        Most active topics this month
    </a>
</div>

@endsection
