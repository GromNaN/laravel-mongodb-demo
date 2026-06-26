<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Board Index') - {{ config('app.name', 'FluxBB') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forum: {
                            header: '#4a6fa5',
                            'header-dark': '#3a5f95',
                            border: '#c9d1d9',
                            bg: '#f6f8fa',
                            row: '#ffffff',
                            'row-alt': '#f0f4f8',
                            link: '#1a56db',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 13px; }
        .bbcode-bold { font-weight: bold; }
        .bbcode-italic { font-style: italic; }
        .bbcode-underline { text-decoration: underline; }
        .bbcode-code { font-family: monospace; background: #f4f4f4; padding: 2px 4px; border: 1px solid #ddd; }
        .bbcode-quote { border-left: 3px solid #aaa; padding: 4px 8px; background: #f9f9f9; margin: 4px 0; color: #555; }
    </style>
    @stack('head')
</head>
<body class="bg-gray-100 text-gray-800">

{{-- Top Navigation Bar --}}
<div class="bg-forum-header text-white shadow-md">
    <div class="max-w-6xl mx-auto px-4">
        {{-- Board name row --}}
        <div class="flex items-center justify-between py-2 border-b border-blue-400 border-opacity-40">
            <a href="{{ url('/') }}" class="text-xl font-bold text-white hover:text-blue-100 tracking-wide">
                {{ config('app.name', 'FluxBB') }}
            </a>
            {{-- User status bar --}}
            <div class="text-xs text-blue-100">
                @auth
                    Logged in as <a href="{{ route('profile.show', auth()->id()) }}" class="font-semibold text-white hover:underline">{{ auth()->user()->username }}</a>
                    &nbsp;|&nbsp;
                    @if(auth()->user()->group_id == 1)
                        <a href="{{ url('/admin') }}" class="text-yellow-300 hover:underline font-semibold">Admin</a>
                        &nbsp;|&nbsp;
                    @endif
                    <a href="{{ route('profile.edit', auth()->user()->id) }}" class="text-blue-100 hover:underline">Settings</a>
                    &nbsp;|&nbsp;
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-blue-100 hover:underline bg-transparent border-0 p-0 cursor-pointer">Logout</button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="text-blue-100 hover:underline">Register</a>
                    &nbsp;|&nbsp;
                    <a href="{{ route('login') }}" class="text-blue-100 hover:underline">Login</a>
                @endauth
            </div>
        </div>
        {{-- Nav links row --}}
        <div class="flex items-center gap-4 py-2 text-sm">
            <a href="{{ url('/') }}" class="text-blue-100 hover:text-white hover:underline">Index</a>
            <span class="text-blue-400">|</span>
            <a href="{{ route('search.index') }}" class="text-blue-100 hover:text-white hover:underline">Search</a>
            <span class="text-blue-400">|</span>
            <a href="{{ route('user.index') }}" class="text-blue-100 hover:text-white hover:underline">User list</a>
            <span class="text-blue-400">|</span>
            <a href="{{ route('help.index') }}" class="text-blue-100 hover:text-white hover:underline">Rules</a>
            @auth
                @if(auth()->user()->group_id == 1)
                    <span class="text-blue-400">|</span>
                    <a href="{{ url('/admin') }}" class="text-yellow-300 hover:text-yellow-100 hover:underline font-semibold">Administration</a>
                @endif
            @endauth
        </div>
    </div>
</div>

{{-- Flash messages --}}
<div class="max-w-6xl mx-auto px-4 mt-2">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-2 rounded mb-2 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-2 rounded mb-2 text-sm">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-2 rounded mb-2 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

{{-- Main content --}}
<div class="max-w-6xl mx-auto px-4 py-4">
    @yield('content')
</div>

{{-- Footer --}}
<div class="max-w-6xl mx-auto px-4 mt-4 pb-6">
    <div class="border-t border-gray-300 pt-3 text-xs text-gray-500 flex flex-col sm:flex-row justify-between gap-1">
        <div>
            Powered by <a href="https://fluxbb.org" class="text-forum-link hover:underline" target="_blank" rel="noopener">FluxBB</a>
        </div>
        <div class="text-right">
            @if(isset($pageGenerationTime))
                Page generated in {{ $pageGenerationTime }}s
                &nbsp;&bull;&nbsp;
            @endif
            @if(isset($onlineCount))
                {{ $onlineCount['registered'] }} registered user(s) and {{ $onlineCount['guests'] }} guest(s) online
            @endif
        </div>
    </div>
</div>

@stack('scripts')
</body>
</html>
