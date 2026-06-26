@extends('admin.layout')

@section('title', 'Admin – Reports')

@section('admin-content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold text-gray-800">
        Reports
        @if($reports->where('zapped', false)->count())
            <span class="ml-2 bg-red-500 text-white text-sm font-bold px-2 py-0.5 rounded-full">
                {{ $reports->where('zapped', false)->count() }}
            </span>
        @endif
    </h1>
    <div class="flex items-center gap-2">
        <a href="{{ request()->fullUrlWithQuery(['show' => 'pending']) }}"
           class="text-sm px-3 py-1.5 border rounded transition {{ !request('show') || request('show') === 'pending' ? 'bg-blue-600 text-white border-blue-600' : 'text-blue-600 border-blue-300 hover:bg-blue-50' }}">
            Pending
        </a>
        <a href="{{ request()->fullUrlWithQuery(['show' => 'handled']) }}"
           class="text-sm px-3 py-1.5 border rounded transition {{ request('show') === 'handled' ? 'bg-blue-600 text-white border-blue-600' : 'text-blue-600 border-blue-300 hover:bg-blue-50' }}">
            Handled
        </a>
    </div>
</div>

@if($reports->isEmpty())
    <div class="bg-white border border-gray-200 rounded p-8 text-center">
        <div class="text-gray-400 text-4xl mb-3">✅</div>
        <div class="text-gray-600 font-semibold text-lg mb-1">
            @if(!request('show') || request('show') === 'pending')
                No pending reports
            @else
                No handled reports found
            @endif
        </div>
        <div class="text-gray-500 text-sm">Everything is clean!</div>
    </div>
@else
    {{-- Bulk action --}}
    @if(!request('show') || request('show') === 'pending')
        <form method="POST" action="{{ route('admin.reports.markall') }}" class="mb-3">
            @csrf
            <button type="submit"
                    onclick="return confirm('Mark all pending reports as handled?')"
                    class="text-sm text-blue-600 hover:underline border border-blue-200 px-3 py-1.5 rounded hover:bg-blue-50 transition">
                Mark all as handled
            </button>
        </form>
    @endif

    <div class="space-y-3">
        @foreach($reports as $report)
            <div class="bg-white border border-gray-200 rounded overflow-hidden
                        {{ $report->zapped ? 'opacity-70' : '' }}">
                <div class="flex items-start gap-4 p-4">
                    {{-- Severity indicator --}}
                    <div class="flex-shrink-0">
                        @if(!$report->zapped)
                            <div class="w-3 h-3 bg-red-500 rounded-full mt-1.5" title="Pending"></div>
                        @else
                            <div class="w-3 h-3 bg-green-500 rounded-full mt-1.5" title="Handled"></div>
                        @endif
                    </div>

                    {{-- Report content --}}
                    <div class="flex-1 min-w-0">
                        {{-- Topic/post link --}}
                        <div class="font-semibold text-sm mb-1">
                            Topic:
                            <a href="{{ route('topic.show', $report->topic_id) }}#p{{ $report->post_id }}"
                               class="text-blue-700 hover:underline" target="_blank">
                                {{ $report->topic->subject ?? 'Unknown topic' }}
                            </a>
                        </div>

                        <div class="text-xs text-gray-500 mb-2">
                            in
                            <a href="{{ route('forum.show', $report->topic->forum_id ?? 0) }}" class="text-blue-500 hover:underline">
                                {{ $report->topic->forum->name ?? 'Unknown forum' }}
                            </a>
                            &bull; Post by
                            <a href="{{ route('profile.show', $report->post->poster_id ?? 0) }}" class="text-blue-500 hover:underline">
                                {{ $report->post->poster ?? 'Unknown user' }}
                            </a>
                        </div>

                        {{-- Report message --}}
                        <div class="bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm text-gray-700 mb-2">
                            {{ $report->message }}
                        </div>

                        {{-- Reporter info --}}
                        <div class="text-xs text-gray-400">
                            Reported by
                            <a href="{{ route('profile.show', $report->reported_by) }}" class="text-blue-500 hover:underline">
                                {{ $report->reporter->username ?? 'Unknown' }}
                            </a>
                            {{ \Carbon\Carbon::parse($report->created_at)->diffForHumans() }}
                            @if($report->zapped && $report->zapped_by)
                                &bull; Handled by
                                <a href="{{ route('profile.show', $report->zapped_by) }}" class="text-blue-500 hover:underline">
                                    {{ $report->zapper->username ?? 'Unknown' }}
                                </a>
                                {{ \Carbon\Carbon::parse($report->zapped_at)->diffForHumans() }}
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex-shrink-0 flex flex-col gap-2">
                        @if(!$report->zapped)
                            <form method="POST" action="{{ route('admin.reports.markhandled', $report->id) }}">
                                @csrf
                                <button type="submit"
                                        class="text-xs bg-green-600 hover:bg-green-700 text-white font-semibold px-3 py-1.5 rounded transition whitespace-nowrap">
                                    Mark handled
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('topic.show', $report->topic_id) }}#p{{ $report->post_id }}"
                           target="_blank"
                           class="text-xs text-blue-600 hover:underline border border-blue-200 px-3 py-1.5 rounded hover:bg-blue-50 transition text-center whitespace-nowrap">
                            View post
                        </a>
                        @if(!$report->zapped)
                            <a href="{{ route('post.edit', $report->post_id) }}"
                               class="text-xs text-orange-600 hover:underline border border-orange-200 px-3 py-1.5 rounded hover:bg-orange-50 transition text-center whitespace-nowrap">
                                Edit post
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($reports->hasPages())
        <div class="mt-4 text-sm">
            {{ $reports->links() }}
        </div>
    @endif
@endif

@endsection
