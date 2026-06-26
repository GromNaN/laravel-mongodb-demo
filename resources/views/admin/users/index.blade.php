@extends('admin.layout')

@section('title', 'Admin – Users')

@section('admin-content')

<div class="flex flex-wrap items-center justify-between mb-4 gap-3">
    <h1 class="text-xl font-bold text-gray-800">Manage Users</h1>
</div>

{{-- Search/filter form --}}
<div class="bg-white border border-gray-200 rounded overflow-hidden mb-4">
    <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
        Search Users
    </div>
    <div class="p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Username or email..."
                   class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-400 flex-1 min-w-48">
            <select name="group_id"
                    class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-400">
                <option value="">All groups</option>
                @if(isset($groups))
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                @endif
            </select>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-1.5 rounded shadow-sm transition">
                Search
            </button>
            @if(request('search') || request('group_id'))
                <a href="{{ route('admin.users.index') }}"
                   class="text-sm text-gray-600 hover:underline px-3 py-1.5 border border-gray-300 rounded hover:bg-gray-50 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Pagination top --}}
@if($users->hasPages())
    <div class="mb-2 text-sm">{{ $users->links() }}</div>
@endif

{{-- Users table --}}
<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <div class="bg-gray-100 border-b border-gray-200 grid grid-cols-12 px-3 py-2 text-xs font-semibold text-gray-600 uppercase tracking-wide">
        <div class="col-span-3">Username</div>
        <div class="col-span-3">Email</div>
        <div class="col-span-2">Group</div>
        <div class="col-span-1 text-center">Posts</div>
        <div class="col-span-2">Registered</div>
        <div class="col-span-1 text-right">Actions</div>
    </div>

    @forelse($users as $user)
        <div class="border-b border-gray-100 grid grid-cols-12 px-3 py-2.5 hover:bg-gray-50 items-center
                    {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}
                    {{ $user->banned ? 'opacity-60' : '' }}">
            <div class="col-span-3 min-w-0">
                <div class="font-semibold text-sm truncate">
                    <a href="{{ route('profile.show', $user->id) }}" class="text-blue-700 hover:underline">{{ $user->username }}</a>
                </div>
                @if($user->banned)
                    <span class="text-xs text-red-600 font-semibold">BANNED</span>
                @endif
            </div>
            <div class="col-span-3 text-xs text-gray-600 truncate">{{ $user->email }}</div>
            <div class="col-span-2 text-xs text-gray-600">{{ $user->group->name ?? '-' }}</div>
            <div class="col-span-1 text-center text-sm text-gray-600">{{ number_format($user->num_posts ?? 0) }}</div>
            <div class="col-span-2 text-xs text-gray-600">{{ \Carbon\Carbon::parse($user->registered)->format('M d, Y') }}</div>
            <div class="col-span-1 flex items-center justify-end gap-2">
                <a href="{{ route('admin.users.edit', $user->id) }}"
                   class="text-xs text-blue-600 hover:underline" title="Edit">Edit</a>
                @if(!$user->banned)
                    <a href="{{ route('admin.bans.index') }}" class="text-xs text-orange-500 hover:underline" title="Ban">Ban</a>
                @else
                    <span class="text-xs text-gray-400">Banned</span>
                @endif
                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                      onsubmit="return confirm('Permanently delete user {{ $user->username }}? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:underline" title="Delete">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="px-4 py-8 text-center text-gray-500 italic text-sm">
            No users found matching your criteria.
        </div>
    @endforelse
</div>

{{-- Bottom pagination --}}
@if($users->hasPages())
    <div class="mt-3 text-sm">{{ $users->links() }}</div>
@endif

<div class="mt-2 text-xs text-gray-500 text-right">
    Total: {{ $users->total() }} user(s)
</div>

@endsection
