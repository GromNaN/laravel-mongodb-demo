@extends('layouts.app')

@section('title', 'Search Results')

@section('content')

{{-- Search summary --}}
<div class="flex flex-wrap items-center justify-between mb-4 gap-2">
    <div class="text-sm text-gray-600">
        @if(request('keywords'))
            Results for <strong class="text-gray-800">&ldquo;{{ request('keywords') }}&rdquo;</strong>
        @endif
        @if(request('author'))
            by <strong class="text-gray-800">{{ request('author') }}</strong>
        @endif
        &mdash; <span class="text-gray-500">{{ $results->total() }} result(s) found</span>
    </div>
    <a href="{{ route('search.index') }}?{{ http_build_query(request()->all()) }}"
       class="text-sm text-blue-600 hover:underline">
        &larr; Modify search
    </a>
</div>

{{-- No results --}}
@if($results->isEmpty())
    <div class="bg-white border border-gray-200 rounded p-8 text-center">
        <div class="text-gray-400 text-4xl mb-3">&#128270;</div>
        <div class="text-gray-600 font-semibold text-lg mb-1">No results found</div>
        <div class="text-gray-500 text-sm mb-4">
            Try different keywords, check your spelling, or broaden your search criteria.
        </div>
        <a href="{{ route('search.index') }}" class="text-blue-600 hover:underline text-sm">
            Back to search &rarr;
        </a>
    </div>
@else
    {{-- Top pagination --}}
    @if($results->hasPages())
        <div class="mb-3 text-sm">
            {{ $results->links() }}
        </div>
    @endif

    {{-- Results list --}}
    <div class="space-y-3">
        @foreach($results as $result)
            <div class="bg-white border border-gray-200 rounded hover:border-blue-200 transition overflow-hidden">
                <div class="flex flex-wrap items-start gap-3 p-4">
                    {{-- Result icon --}}
                    <div class="flex-shrink-0 text-blue-400 mt-0.5">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 2H4a2 2 0 00-2 2v18l4-4h14a2 2 0 002-2V4a2 2 0 00-2-2z"/>
                        </svg>
                    </div>

                    {{-- Result content --}}
                    <div class="flex-1 min-w-0">
                        {{-- Topic subject --}}
                        <div class="font-semibold text-sm">
                            <a href="{{ route('topic.show', $result->topic_id ?? $result->id) }}{{ isset($result->post_id) ? '#p'.$result->post_id : '' }}"
                               class="text-blue-700 hover:underline">
                                {{ $result->subject }}
                            </a>
                        </div>

                        {{-- Meta info --}}
                        <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-x-3 gap-y-0.5">
                            <span>
                                by <a href="{{ route('profile.show', $result->poster_id) }}" class="text-blue-500 hover:underline">{{ $result->poster }}</a>
                            </span>
                            <span>in
                                <a href="{{ route('forum.show', $result->forum_id) }}" class="text-blue-500 hover:underline">{{ $result->forum_name }}</a>
                            </span>
                            <span>{{ \Carbon\Carbon::parse($result->posted)->diffForHumans() }}</span>
                            @if(isset($result->num_replies))
                                <span>{{ number_format($result->num_replies) }} replies</span>
                            @endif
                            @if(isset($result->num_views))
                                <span>{{ number_format($result->num_views) }} views</span>
                            @endif
                        </div>

                        {{-- Snippet --}}
                        @if(isset($result->message) && $result->message)
                            <div class="mt-2 text-sm text-gray-600 leading-relaxed">
                                @php
                                    $snippet = strip_tags($result->message);
                                    $keywords = request('keywords');
                                    if ($keywords) {
                                        $pos = stripos($snippet, $keywords);
                                        if ($pos !== false) {
                                            $start = max(0, $pos - 80);
                                            $snippet = ($start > 0 ? '&hellip;' : '') . e(substr($snippet, $start, 250)) . '&hellip;';
                                            $snippet = preg_replace('/(' . preg_quote($keywords, '/') . ')/i', '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>', $snippet);
                                        } else {
                                            $snippet = e(Str::limit($snippet, 200));
                                        }
                                    } else {
                                        $snippet = e(Str::limit($snippet, 200));
                                    }
                                @endphp
                                {!! $snippet !!}
                            </div>
                        @endif
                    </div>

                    {{-- Last post info --}}
                    @if(isset($result->last_post_time))
                        <div class="flex-shrink-0 text-right text-xs text-gray-500 whitespace-nowrap hidden sm:block">
                            <div>Last post</div>
                            <div class="text-gray-700">{{ \Carbon\Carbon::parse($result->last_post_time)->diffForHumans() }}</div>
                            @if(isset($result->last_poster))
                                <div>by <a href="{{ route('profile.show', $result->last_poster_id) }}" class="text-blue-500 hover:underline">{{ $result->last_poster }}</a></div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Bottom pagination --}}
    @if($results->hasPages())
        <div class="mt-4 text-sm">
            {{ $results->links() }}
        </div>
    @endif
@endif

@endsection
