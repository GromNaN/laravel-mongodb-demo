@extends('layouts.app')

@section('title', 'Board Index')

@section('content')

{{-- Announcement bar --}}
@if(config('forum.announcement'))
    <div class="bg-yellow-50 border border-yellow-300 text-yellow-900 px-4 py-3 rounded mb-4 text-sm">
        {!! nl2br(e(config('forum.announcement'))) !!}
    </div>
@endif

{{-- Category and forum list --}}
@forelse($categories as $category)
    <div class="mb-6">
        {{-- Category header --}}
        <div class="bg-forum-header text-white px-4 py-2 rounded-t font-semibold text-sm">
            {{ $category->name }}
        </div>

        {{-- Forums table --}}
        <table class="w-full border-collapse bg-white text-sm">
            <tbody>
            @forelse($category->forums as $forum)
                <tr class="border-b border-gray-200 hover:bg-gray-50 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                    {{-- Forum icon --}}
                    <td class="w-10 px-3 py-3 text-center text-blue-400">
                        @if($forum->redirect_url)
                            {{-- Redirect icon --}}
                            <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        @else
                            {{-- Speech bubble icon --}}
                            <svg class="w-6 h-6 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 2H4a2 2 0 00-2 2v18l4-4h14a2 2 0 002-2V4a2 2 0 00-2-2z"/>
                            </svg>
                        @endif
                    </td>

                    {{-- Forum name, description, moderators --}}
                    <td class="px-3 py-3 flex-1">
                        <div class="font-semibold">
                            @if($forum->redirect_url)
                                <a href="{{ $forum->redirect_url }}" class="text-blue-700 hover:underline" target="_blank" rel="noopener">{{ $forum->name }}</a>
                            @else
                                <a href="{{ route('forum.show', $forum->id) }}" class="text-blue-700 hover:underline">{{ $forum->name }}</a>
                            @endif
                        </div>
                        @if($forum->description)
                            <div class="text-gray-500 text-xs mt-0.5">{{ $forum->description }}</div>
                        @endif
                        @if(!empty($forum->moderators))
                            <div class="text-xs text-gray-400 mt-0.5">
                                Moderators:
                                @foreach($forum->moderators as $modName)
                                    <span class="text-blue-500">{{ $modName }}</span>@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        @endif
                    </td>

                    {{-- Posts / Topics stats --}}
                    <td class="px-3 py-3 text-center text-xs text-gray-600 whitespace-nowrap hidden sm:table-cell">
                        <div class="font-semibold text-gray-700">{{ number_format($forum->num_posts ?? 0) }}</div>
                        <div class="text-gray-400">Posts</div>
                        <div class="font-semibold text-gray-700 mt-1">{{ number_format($forum->num_topics ?? 0) }}</div>
                        <div class="text-gray-400">Topics</div>
                    </td>

                    {{-- Last post info --}}
                    <td class="px-3 py-3 text-xs text-gray-600 whitespace-nowrap hidden md:table-cell min-w-[180px]">
                        @php $lp = $forum->last_post; @endphp
                        @if(!empty($lp['topic_id']))
                            <div class="font-semibold text-gray-700 truncate max-w-[200px]">
                                <a href="{{ route('topic.show', $lp['topic_id']) }}#p{{ $lp['post_id'] ?? '' }}" class="text-blue-600 hover:underline">
                                    {{ Str::limit($lp['subject'] ?? '', 30) }}
                                </a>
                            </div>
                            <div class="text-gray-500 mt-0.5">
                                by <span class="text-blue-500">{{ $lp['poster'] ?? '' }}</span>
                            </div>
                            <div class="text-gray-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($lp['time'] ?? now())->diffForHumans() }}
                            </div>
                        @else
                            <span class="text-gray-400 italic">No posts yet</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-3 text-gray-500 italic text-sm">No forums in this category.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <div class="border border-t-0 border-gray-200 rounded-b bg-white h-1"></div>
    </div>
@empty
    <div class="bg-white border border-gray-200 rounded p-6 text-center text-gray-500">
        No categories or forums have been created yet.
    </div>
@endforelse

{{-- Board stats --}}
<div class="mt-4 bg-white border border-gray-200 rounded px-4 py-3 text-sm text-gray-600 flex flex-wrap gap-x-6 gap-y-1">
    <span>Total posts: <strong class="text-gray-800">{{ number_format($stats['total_posts'] ?? 0) }}</strong></span>
    <span>Total topics: <strong class="text-gray-800">{{ number_format($stats['total_topics'] ?? 0) }}</strong></span>
    <span>Total users: <strong class="text-gray-800">{{ number_format($stats['total_users'] ?? 0) }}</strong></span>
    @if(isset($stats['newest_user']))
        <span>Newest member: <a href="{{ route('profile.show', $stats['newest_user']->id) }}" class="text-blue-600 hover:underline font-semibold">{{ $stats['newest_user']->username }}</a></span>
    @endif
</div>

{{-- Online users --}}
<div class="mt-2 bg-white border border-gray-200 rounded px-4 py-3 text-sm text-gray-600">
    <span class="font-semibold">Users online:</span>
    @if(isset($onlineUsers) && $onlineUsers->count())
        @foreach($onlineUsers as $onlineUser)
            <a href="{{ route('profile.show', $onlineUser->id) }}" class="text-blue-600 hover:underline">{{ $onlineUser->username }}</a>@if(!$loop->last), @endif
        @endforeach
        ({{ $onlineUsers->count() }} registered)
    @else
        No registered users online.
    @endif
    @if(isset($guestCount))
        &nbsp;&bull;&nbsp;{{ $guestCount }} guest(s) online
    @endif
</div>

@endsection
