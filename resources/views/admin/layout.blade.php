@extends('layouts.app')

@section('content')
<div class="flex gap-4">

    {{-- Admin Sidebar --}}
    <div class="w-48 flex-shrink-0">
        <div class="bg-white border border-gray-200 rounded overflow-hidden sticky top-4">
            <div class="bg-gray-800 text-white px-3 py-2 font-semibold text-sm">
                Administration
            </div>
            <nav class="p-2 space-y-0.5">
                @php
                    $navItems = [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '📊'],
                        ['route' => 'admin.settings.index', 'label' => 'Settings', 'icon' => '⚙️'],
                        ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => '👥'],
                        ['route' => 'admin.bans.index', 'label' => 'Bans', 'icon' => '🚫'],
                        ['route' => 'admin.forums.index', 'label' => 'Forums', 'icon' => '📋'],
                        ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => '📁'],
                        ['route' => 'admin.groups.index', 'label' => 'Groups', 'icon' => '🔑'],
                        ['route' => 'admin.reports.index', 'label' => 'Reports', 'icon' => '🚨'],
                        ['route' => 'admin.censoring.index', 'label' => 'Censoring', 'icon' => '🔇'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @php $active = request()->routeIs($item['route']) @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-2 px-3 py-1.5 rounded text-sm transition
                              {{ $active ? 'bg-blue-600 text-white font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                        <span class="text-base leading-none">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                        @if($item['route'] === 'admin.reports.index' && isset($pendingReportsCount) && $pendingReportsCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">
                                {{ $pendingReportsCount }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="border-t border-gray-100 p-2">
                <a href="{{ url('/') }}"
                   class="flex items-center gap-2 px-3 py-1.5 rounded text-sm text-gray-600 hover:bg-gray-100 transition">
                    &larr; Back to Forum
                </a>
            </div>
        </div>
    </div>

    {{-- Main admin content --}}
    <div class="flex-1 min-w-0">
        @yield('admin-content')
    </div>

</div>
@endsection
