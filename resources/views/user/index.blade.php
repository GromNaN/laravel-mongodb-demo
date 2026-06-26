@extends('layouts.app')

@section('title', 'User List')

@section('content')

{{-- Header + search --}}
<div class="flex flex-wrap items-center justify-between mb-4 gap-3">
    <h1 class="text-lg font-bold text-gray-800">User List</h1>
    <form method="GET" action="{{ route('user.index') }}" class="flex items-center gap-2">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search username..."
               class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-1.5 rounded shadow-sm transition">
            Search
        </button>
        @if(request('search'))
            <a href="{{ route('user.index') }}" class="text-sm text-gray-600 hover:underline">Clear</a>
        @endif
    </form>
</div>

{{-- Sort links --}}
<div class="text-xs text-gray-500 mb-2">
    Sort by:
    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'username', 'sort_dir' => request('sort_dir', 'asc') === 'asc' && request('sort_by', 'username') === 'username' ? 'desc' : 'asc']) }}"
       class="text-blue-600 hover:underline {{ request('sort_by', 'username') === 'username' ? 'font-bold' : '' }}">
        Username {{ request('sort_by', 'username') === 'username' ? (request('sort_dir', 'asc') === 'asc' ? '▲' : '▼') : '' }}
    </a>
    &nbsp;|&nbsp;
    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'num_posts', 'sort_dir' => request('sort_dir', 'asc') === 'desc' && request('sort_by') === 'num_posts' ? 'asc' : 'desc']) }}"
       class="text-blue-600 hover:underline {{ request('sort_by') === 'num_posts' ? 'font-bold' : '' }}">
        Posts {{ request('sort_by') === 'num_posts' ? (request('sort_dir', 'desc') === 'desc' ? '▼' : '▲') : '' }}
    </a>
    &nbsp;|&nbsp;
    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'registered', 'sort_dir' => request('sort_dir', 'asc') === 'desc' && request('sort_by') === 'registered' ? 'asc' : 'desc']) }}"
       class="text-blue-600 hover:underline {{ request('sort_by') === 'registered' ? 'font-bold' : '' }}">
        Joined {{ request('sort_by') === 'registered' ? (request('sort_dir', 'desc') === 'desc' ? '▼' : '▲') : '' }}
    </a>
</div>

{{-- Top pagination --}}
@if($users->hasPages())
    <div class="mb-3 text-sm">
        {{ $users->links() }}
    </div>
@endif

{{-- Users table --}}
<div class="bg-white border border-gray-200 rounded overflow-hidden">
    {{-- Table header --}}
    <div class="bg-gray-100 border-b border-gray-200 grid grid-cols-12 px-3 py-2 text-xs font-semibold text-gray-600 uppercase tracking-wide">
        <div class="col-span-5">Username</div>
        <div class="col-span-2 text-center">Posts</div>
        <div class="col-span-3">Joined</div>
        <div class="col-span-2 hidden sm:block">Last active</div>
    </div>

    @forelse($users as $user)
        <div class="border-b border-gray-100 grid grid-cols-12 px-3 py-2.5 hover:bg-gray-50 items-center
                    {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
            {{-- Username + avatar --}}
            <div class="col-span-5 flex items-center gap-2 min-w-0">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->username }}"
                         class="w-7 h-7 rounded-full border border-gray-200 object-cover flex-shrink-0">
                @else
                    <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-blue-500 text-xs font-bold flex-shrink-0 border border-blue-200">
                        {{ strtoupper(substr($user->username, 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <div class="font-semibold text-sm truncate">
                        <a href="{{ route('profile.show', $user->id) }}" class="text-blue-700 hover:underline">{{ $user->username }}</a>
                    </div>
                    @if($user->title)
                        <div class="text-xs text-gray-400 truncate">{{ $user->title }}</div>
                    @elseif($user->group)
                        <div class="text-xs text-gray-400 truncate">{{ $user->group->name }}</div>
                    @endif
                </div>
            </div>

            {{-- Posts --}}
            <div class="col-span-2 text-center text-sm text-gray-600">
                {{ number_format($user->num_posts ?? 0) }}
            </div>

            {{-- Joined --}}
            <div class="col-span-3 text-xs text-gray-600">
                {{ \Carbon\Carbon::parse($user->registered)->format('M d, Y') }}
            </div>

            {{-- Last active --}}
            <div class="col-span-2 text-xs text-gray-500 hidden sm:block">
                {{ $user->last_visit ? \Carbon\Carbon::parse($user->last_visit)->diffForHumans() : 'Never' }}
            </div>
        </div>
    @empty
        <div class="px-4 py-8 text-center text-gray-500 italic text-sm">
            @if(request('search'))
                No users found matching &ldquo;{{ request('search') }}&rdquo;.
            @else
                No users found.
            @endif
        </div>
    @endforelse
</div>

{{-- Bottom pagination --}}
@if($users->hasPages())
    <div class="mt-4 text-sm">
        {{ $users->links() }}
    </div>
@endif

{{-- Stats bar --}}
<div class="mt-3 text-xs text-gray-500 text-right">
    Total registered users: <strong class="text-gray-700">{{ $users->total() }}</strong>
</div>

@endsection
