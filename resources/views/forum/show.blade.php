@extends('layouts.app')

@section('title', $forum->name)

@section('content')

{{-- Breadcrumb --}}
<div class="text-xs text-gray-500 mb-3 flex items-center flex-wrap gap-1">
    <a href="{{ url('/') }}" class="text-blue-600 hover:underline">Index</a>
    <span class="text-gray-400">&raquo;</span>
    <a href="{{ route('forum.show', $forum->id) }}" class="text-blue-600 hover:underline">{{ $forum->name }}</a>
</div>

{{-- Action bar (top) --}}
<div class="flex flex-wrap items-center justify-between mb-3 gap-2">
    <div class="flex items-center gap-3">
        @auth
            @if(!($forum->post_topics == false))
                <a href="{{ route('post.create', $forum->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-1.5 rounded shadow-sm transition">
                    + New Topic
                </a>
            @endif
        @endauth
    </div>
    <div class="flex items-center gap-3 text-sm text-gray-600">
        <span>Sort by:</span>
        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'last_post']) }}"
           class="text-blue-600 hover:underline {{ request('sort_by', 'last_post') === 'last_post' ? 'font-bold' : '' }}">Last post</a>
        <span class="text-gray-400">|</span>
        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'posted']) }}"
           class="text-blue-600 hover:underline {{ request('sort_by') === 'posted' ? 'font-bold' : '' }}">Topic start</a>
        @auth
            <span class="text-gray-400">|</span>
            <a href="{{ route('forum.markread', $forum->id) }}" class="text-blue-600 hover:underline">Mark forum read</a>
        @endauth
    </div>
</div>

{{-- Top pagination --}}
@if($topics->hasPages())
    <div class="mb-2 text-sm text-gray-600">
        Pages: {{ $topics->links('vendor.pagination.simple-tailwind') }}
    </div>
@endif

{{-- Topics table --}}
<div class="bg-white border border-gray-200 rounded overflow-hidden">
    {{-- Table header --}}
    <div class="bg-gray-100 border-b border-gray-200 grid grid-cols-12 px-3 py-2 text-xs font-semibold text-gray-600 uppercase tracking-wide">
        <div class="col-span-1 text-center">!</div>
        <div class="col-span-5">Topic Subject</div>
        <div class="col-span-1 text-center hidden sm:block">Replies</div>
        <div class="col-span-1 text-center hidden sm:block">Views</div>
        <div class="col-span-4 hidden md:block">Last Post</div>
    </div>

    {{-- Sticky topics first --}}
    @php
        $stickyTopics = $topics->where('sticky', true);
        $regularTopics = $topics->where('sticky', false);
    @endphp

    @foreach($topics as $topic)
        @php
            $isRead = auth()->check() ? ($topic->last_post_time <= (auth()->user()->last_visit ?? 0)) : true;
        @endphp
        <div class="border-b border-gray-100 grid grid-cols-12 px-3 py-2.5 hover:bg-gray-50 items-center
                    {{ $topic->sticky ? 'bg-blue-50' : '' }}
                    {{ $topic->closed ? 'opacity-80' : '' }}">

            {{-- Status icons --}}
            <div class="col-span-1 flex items-center justify-center gap-1">
                @if($topic->sticky)
                    <span title="Sticky" class="text-blue-500">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l2.09 6.41H21l-5.47 3.97 2.09 6.42L12 14.83l-5.62 3.97 2.09-6.42L3 8.41h6.91z"/>
                        </svg>
                    </span>
                @endif
                @if($topic->closed)
                    <span title="Closed" class="text-red-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                    </span>
                @endif
                @if(!$topic->sticky && !$topic->closed)
                    <span class="{{ $isRead ? 'text-gray-300' : 'text-blue-500' }}">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 2H4a2 2 0 00-2 2v18l4-4h14a2 2 0 002-2V4a2 2 0 00-2-2z"/>
                        </svg>
                    </span>
                @endif
            </div>

            {{-- Subject --}}
            <div class="col-span-5 min-w-0">
                <div class="font-semibold text-sm">
                    @if($topic->sticky)
                        <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-1.5 py-0.5 rounded mr-1">Sticky</span>
                    @endif
                    @if($topic->closed)
                        <span class="text-xs font-semibold text-red-600 bg-red-100 px-1.5 py-0.5 rounded mr-1">Closed</span>
                    @endif
                    <a href="{{ route('topic.show', $topic->id) }}" class="text-blue-700 hover:underline">
                        {{ $topic->subject }}
                    </a>
                </div>
                <div class="text-xs text-gray-500 mt-0.5">
                    by <a href="{{ route('profile.show', $topic->poster_id) }}" class="text-blue-500 hover:underline">{{ $topic->poster }}</a>
                    &bull; {{ \Carbon\Carbon::parse($topic->posted)->diffForHumans() }}
                </div>
            </div>

            {{-- Replies --}}
            <div class="col-span-1 text-center text-sm text-gray-600 hidden sm:block">
                {{ number_format($topic->num_replies ?? 0) }}
            </div>

            {{-- Views --}}
            <div class="col-span-1 text-center text-sm text-gray-600 hidden sm:block">
                {{ number_format($topic->num_views ?? 0) }}
            </div>

            {{-- Last post --}}
            <div class="col-span-4 text-xs text-gray-600 hidden md:block min-w-0">
                @if($topic->last_post_id)
                    <div class="text-gray-700 truncate">
                        by <a href="{{ route('profile.show', $topic->last_poster_id) }}" class="text-blue-500 hover:underline">{{ $topic->last_poster }}</a>
                    </div>
                    <div class="text-gray-400">
                        <a href="{{ route('topic.show', $topic->id) }}#p{{ $topic->last_post_id }}" class="hover:underline">
                            {{ \Carbon\Carbon::parse($topic->last_post_time)->diffForHumans() }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    @if($topics->isEmpty())
        <div class="px-4 py-8 text-center text-gray-500 italic text-sm">
            No topics have been posted in this forum yet.
        </div>
    @endif
</div>

{{-- Bottom actions & pagination --}}
<div class="flex flex-wrap items-center justify-between mt-3 gap-2">
    <div class="flex items-center gap-3">
        @auth
            <a href="{{ route('post.create', $forum->id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-1.5 rounded shadow-sm transition">
                + New Topic
            </a>
            <a href="{{ route('forum.subscribe', $forum->id) }}"
               class="text-sm text-blue-600 hover:underline border border-blue-200 px-3 py-1.5 rounded hover:bg-blue-50 transition">
                @if(isset($isSubscribed) && $isSubscribed)
                    Unsubscribe
                @else
                    Subscribe
                @endif
            </a>
        @endauth
    </div>

    @if($topics->hasPages())
        <div class="text-sm">
            {{ $topics->links() }}
        </div>
    @endif
</div>

@endsection
