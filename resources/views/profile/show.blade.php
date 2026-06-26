@extends('layouts.app')

@section('title', $user->username . "'s Profile")

@section('content')

{{-- Breadcrumb --}}
<div class="text-xs text-gray-500 mb-3 flex items-center flex-wrap gap-1">
    <a href="{{ url('/') }}" class="text-blue-600 hover:underline">Index</a>
    <span class="text-gray-400">&raquo;</span>
    <a href="{{ route('user.index') }}" class="text-blue-600 hover:underline">User list</a>
    <span class="text-gray-400">&raquo;</span>
    <span class="text-gray-700">{{ $user->username }}</span>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    {{-- Left: user card --}}
    <div class="md:col-span-1 space-y-4">

        {{-- Avatar & basic info --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
                {{ $user->username }}
            </div>
            <div class="p-4 flex flex-col items-center text-center">
                {{-- Avatar --}}
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" alt="{{ $user->username }}"
                         class="w-24 h-24 rounded-full border-2 border-gray-200 object-cover mb-3">
                @else
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mb-3 text-blue-500 text-4xl font-bold border-2 border-blue-200">
                        {{ strtoupper(substr($user->username, 0, 1)) }}
                    </div>
                @endif

                {{-- Title --}}
                @if($user->title)
                    <div class="text-sm font-semibold text-gray-700">{{ $user->title }}</div>
                @endif

                {{-- Group --}}
                @if($user->group)
                    <div class="text-xs text-gray-500 mt-0.5">{{ $user->group->name }}</div>
                @endif

                {{-- Post count --}}
                <div class="mt-3 text-sm text-gray-600">
                    <span class="font-semibold text-gray-800">{{ number_format($user->num_posts ?? 0) }}</span> posts
                </div>
            </div>
        </div>

        {{-- Contact info --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
                Information
            </div>
            <div class="p-4 space-y-2 text-sm">
                <div class="flex gap-2">
                    <span class="text-gray-500 w-24 flex-shrink-0">Registered:</span>
                    <span class="text-gray-700">{{ \Carbon\Carbon::parse($user->registered)->format('M d, Y') }}</span>
                </div>
                <div class="flex gap-2">
                    <span class="text-gray-500 w-24 flex-shrink-0">Last active:</span>
                    <span class="text-gray-700">{{ $user->last_visit ? \Carbon\Carbon::parse($user->last_visit)->diffForHumans() : 'Never' }}</span>
                </div>
                @if($user->location)
                    <div class="flex gap-2">
                        <span class="text-gray-500 w-24 flex-shrink-0">Location:</span>
                        <span class="text-gray-700">{{ $user->location }}</span>
                    </div>
                @endif
                @if($user->realname)
                    <div class="flex gap-2">
                        <span class="text-gray-500 w-24 flex-shrink-0">Real name:</span>
                        <span class="text-gray-700">{{ $user->realname }}</span>
                    </div>
                @endif
                @if($user->url)
                    <div class="flex gap-2">
                        <span class="text-gray-500 w-24 flex-shrink-0">Website:</span>
                        <a href="{{ $user->url }}" target="_blank" rel="noopener noreferrer"
                           class="text-blue-600 hover:underline break-all">{{ Str::limit($user->url, 40) }}</a>
                    </div>
                @endif
                @if($user->show_email && ($user->email_setting == 0 || (auth()->check() && auth()->user()->group_id == 1)))
                    <div class="flex gap-2">
                        <span class="text-gray-500 w-24 flex-shrink-0">Email:</span>
                        <a href="mailto:{{ $user->email }}" class="text-blue-600 hover:underline">{{ $user->email }}</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col gap-2">
            @auth
                @if(auth()->id() === $user->id)
                    <a href="{{ route('profile.edit', auth()->user()->id) }}"
                       class="text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded shadow-sm transition">
                        Edit Profile
                    </a>
                @endif
                @if(auth()->user()->group_id == 1)
                    <a href="{{ route('admin.users.edit', $user->id) }}"
                       class="text-center bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold px-4 py-2 rounded shadow-sm transition">
                        Admin: Edit User
                    </a>
                @endif
            @endauth
            <a href="{{ route('search.index', ['author' => $user->username]) }}"
               class="text-center border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm px-4 py-2 rounded transition">
                Find all posts by {{ $user->username }}
            </a>
        </div>
    </div>

    {{-- Right: signature & recent posts --}}
    <div class="md:col-span-2 space-y-4">

        {{-- Signature --}}
        @if($user->signature)
            <div class="bg-white border border-gray-200 rounded overflow-hidden">
                <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
                    Signature
                </div>
                <div class="p-4 text-sm leading-relaxed text-gray-700">
                    {!! \App\Helpers\BbCode::parse($user->signature) !!}
                </div>
            </div>
        @endif

        {{-- Recent posts --}}
        @if(isset($recentPosts) && $recentPosts->count())
            <div class="bg-white border border-gray-200 rounded overflow-hidden">
                <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
                    Recent Posts by {{ $user->username }}
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($recentPosts as $post)
                        <div class="p-4 hover:bg-gray-50 transition">
                            <div class="font-semibold text-sm">
                                <a href="{{ route('topic.show', $post->topic_id) }}#p{{ $post->id }}"
                                   class="text-blue-700 hover:underline">
                                    {{ $post->topic->subject ?? 'Unknown Topic' }}
                                </a>
                            </div>
                            <div class="text-xs text-gray-500 mt-0.5">
                                in <a href="{{ route('forum.show', $post->topic->forum_id ?? 0) }}" class="text-blue-500 hover:underline">{{ $post->topic->forum->name ?? 'Unknown Forum' }}</a>
                                &bull; {{ \Carbon\Carbon::parse($post->posted)->diffForHumans() }}
                            </div>
                            <div class="mt-1.5 text-sm text-gray-600 leading-relaxed">
                                {{ Str::limit(strip_tags($post->message), 180) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($recentPosts->count() >= 10)
                    <div class="px-4 py-3 border-t border-gray-100 text-center">
                        <a href="{{ route('search.index', ['author' => $user->username]) }}"
                           class="text-sm text-blue-600 hover:underline">
                            View all posts by {{ $user->username }} &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

@endsection
