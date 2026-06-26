@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('admin-content')

<h1 class="text-xl font-bold text-gray-800 mb-4">Dashboard</h1>

{{-- Stats cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Total Users', $stats['total_users'] ?? 0, 'bg-blue-500', '👥'],
        ['Total Topics', $stats['total_topics'] ?? 0, 'bg-green-500', '💬'],
        ['Total Posts', $stats['total_posts'] ?? 0, 'bg-purple-500', '📝'],
        ['Online Now', $stats['online_count'] ?? 0, 'bg-orange-500', '🟢'],
    ] as [$label, $value, $color, $icon])
        <div class="bg-white border border-gray-200 rounded p-4 flex items-center gap-3">
            <div class="{{ $color }} text-white w-10 h-10 rounded-lg flex items-center justify-center text-lg flex-shrink-0">
                {{ $icon }}
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-800">{{ number_format($value) }}</div>
                <div class="text-xs text-gray-500">{{ $label }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Pending reports --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="flex items-center justify-between bg-gray-100 border-b border-gray-200 px-4 py-2">
            <span class="font-semibold text-sm text-gray-700">Pending Reports</span>
            @if(isset($pendingReportsCount) && $pendingReportsCount > 0)
                <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ $pendingReportsCount }}
                </span>
            @endif
        </div>
        <div class="p-4">
            @if(isset($pendingReportsCount) && $pendingReportsCount > 0)
                <p class="text-sm text-gray-600 mb-3">
                    There are <strong class="text-red-600">{{ $pendingReportsCount }}</strong> pending reports that need attention.
                </p>
                <a href="{{ route('admin.reports.index') }}"
                   class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded shadow-sm transition inline-block">
                    View Reports
                </a>
            @else
                <p class="text-sm text-gray-500 italic">No pending reports. 🎉</p>
            @endif
        </div>
    </div>

    {{-- Quick links --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-gray-100 border-b border-gray-200 px-4 py-2">
            <span class="font-semibold text-sm text-gray-700">Quick Actions</span>
        </div>
        <div class="p-4 grid grid-cols-2 gap-2">
            @foreach([
                ['New Forum', route('admin.forums.index'), 'border-green-300 text-green-700 hover:bg-green-50'],
                ['New Category', route('admin.categories.index'), 'border-blue-300 text-blue-700 hover:bg-blue-50'],
                ['Manage Users', route('admin.users.index'), 'border-purple-300 text-purple-700 hover:bg-purple-50'],
                ['Settings', route('admin.settings.index'), 'border-gray-300 text-gray-700 hover:bg-gray-50'],
                ['Manage Bans', route('admin.bans.index'), 'border-red-300 text-red-700 hover:bg-red-50'],
                ['Censoring', route('admin.censoring.index'), 'border-yellow-300 text-yellow-700 hover:bg-yellow-50'],
            ] as [$label, $href, $classes])
                <a href="{{ $href }}"
                   class="text-center border rounded px-3 py-2 text-sm font-semibold transition {{ $classes }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Latest users --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="flex items-center justify-between bg-gray-100 border-b border-gray-200 px-4 py-2">
            <span class="font-semibold text-sm text-gray-700">Newest Members</span>
            <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-600 hover:underline">View all</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse(isset($latestUsers) ? $latestUsers : [] as $user)
                <div class="flex items-center justify-between px-4 py-2">
                    <div class="text-sm">
                        <a href="{{ route('profile.show', $user->id) }}" class="text-blue-700 hover:underline font-semibold">{{ $user->username }}</a>
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($user->registered)->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div class="px-4 py-3 text-sm text-gray-500 italic">No recent users.</div>
            @endforelse
        </div>
    </div>

    {{-- Server info --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-gray-100 border-b border-gray-200 px-4 py-2">
            <span class="font-semibold text-sm text-gray-700">System Information</span>
        </div>
        <div class="p-4 space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">PHP Version</span>
                <span class="text-gray-700 font-mono text-xs">{{ phpversion() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Laravel Version</span>
                <span class="text-gray-700 font-mono text-xs">{{ app()->version() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Environment</span>
                <span class="text-gray-700 font-mono text-xs">{{ app()->environment() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Server Time</span>
                <span class="text-gray-700 font-mono text-xs">{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Cache Driver</span>
                <span class="text-gray-700 font-mono text-xs">{{ config('cache.default') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Database</span>
                <span class="text-gray-700 font-mono text-xs">{{ config('database.default') }}</span>
            </div>
        </div>
    </div>

</div>

@endsection
