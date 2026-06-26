@extends('layouts.app')

@section('title', $topic->subject)

@section('content')

{{-- Breadcrumb --}}
<div class="text-xs text-gray-500 mb-3 flex items-center flex-wrap gap-1">
    <a href="{{ url('/') }}" class="text-blue-600 hover:underline">Index</a>
    <span class="text-gray-400">&raquo;</span>
    <a href="{{ route('forum.show', $topic->forum->id) }}" class="text-blue-600 hover:underline">{{ $topic->forum->name }}</a>
    <span class="text-gray-400">&raquo;</span>
    <span class="text-gray-700">{{ $topic->subject }}</span>
</div>

{{-- Topic status notice --}}
@if($topic->closed)
    <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-2 rounded mb-3 text-sm">
        This topic has been closed and is no longer accepting replies.
    </div>
@endif

{{-- Top action bar --}}
<div class="flex flex-wrap items-center justify-between mb-3 gap-2">
    <div class="flex items-center gap-2">
        @auth
            @if(!$topic->closed)
                <a href="{{ route('post.reply', $topic->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-1.5 rounded shadow-sm transition">
                    Post Reply
                </a>
            @endif
            <a href="{{ route('topic.subscribe', $topic->id) }}"
               class="text-sm text-blue-600 hover:underline border border-blue-200 px-3 py-1.5 rounded hover:bg-blue-50 transition">
                @if(isset($isSubscribed) && $isSubscribed)
                    Unsubscribe
                @else
                    Subscribe
                @endif
            </a>
        @endauth
    </div>

    {{-- Top pagination --}}
    @if($posts->hasPages())
        <div class="text-sm">
            {{ $posts->links() }}
        </div>
    @endif
</div>

{{-- Posts list --}}
@foreach($posts as $index => $post)
    @php
        $postNumber = ($posts->currentPage() - 1) * $posts->perPage() + $loop->iteration;
        $canEdit = auth()->check() && (auth()->id() === $post->poster_id || auth()->user()->group_id == 1);
        $canDelete = auth()->check() && (auth()->id() === $post->poster_id || auth()->user()->group_id == 1);
    @endphp
    <div id="p{{ $post->num ?? $postNumber }}" class="bg-white border border-gray-200 rounded mb-4 overflow-hidden">
        <div class="flex">
            {{-- Left column: user info --}}
            <div class="w-36 md:w-44 flex-shrink-0 bg-gray-50 border-r border-gray-200 p-3 flex flex-col items-center text-center">
                {{-- Avatar placeholder --}}
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-2 text-blue-500 text-2xl font-bold border border-blue-200">
                    {{ strtoupper(substr($post->poster ?? '?', 0, 1)) }}
                </div>

                {{-- Username --}}
                <div class="font-semibold text-sm">
                    @if($post->poster_id)
                        <a href="{{ route('profile.show', $post->poster_id) }}" class="text-blue-700 hover:underline">{{ $post->poster }}</a>
                    @else
                        <span class="text-gray-600">{{ $post->poster }}</span>
                    @endif
                </div>

                {{-- User group/title --}}
                @if($post->user)
                    <div class="text-xs text-gray-500 mt-0.5">{{ $post->user->title ?? $post->user->group->name ?? 'Member' }}</div>

                    <div class="text-xs text-gray-400 mt-2 space-y-0.5">
                        <div>Posts: {{ number_format($post->user->num_posts ?? 0) }}</div>
                        <div>Joined: {{ \Carbon\Carbon::parse($post->user->registered)->format('M d, Y') }}</div>
                    </div>
                @endif
            </div>

            {{-- Right column: post content --}}
            <div class="flex-1 min-w-0 flex flex-col">
                {{-- Post header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-2 bg-gray-50">
                    <div class="text-xs text-gray-500">
                        <a href="#p{{ $post->num ?? $postNumber }}" class="text-gray-400 hover:text-blue-600 font-mono">#{{ $post->num ?? $postNumber }}</a>
                        &nbsp;&bull;&nbsp;
                        {{ \Carbon\Carbon::parse($post->posted)->format('D, d M Y H:i') }}
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        @auth
                            @if(!$topic->closed || auth()->user()->group_id == 1)
                                <a href="{{ route('post.reply', $topic->id) }}?quote={{ $post->id }}"
                                   class="text-blue-600 hover:underline">Quote</a>
                            @endif
                            @if($canEdit)
                                <a href="{{ route('post.edit', $post->id) }}"
                                   class="text-blue-600 hover:underline">Edit</a>
                            @endif
                            @if($canDelete)
                                <form method="POST" action="{{ route('post.destroy', $post->id) }}"
                                      onsubmit="return confirm('Are you sure you want to delete this post?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>

                {{-- Post body --}}
                <div class="px-4 py-3 text-sm leading-relaxed flex-1">
                    {!! \App\Helpers\BbCode::parse($post->message) !!}
                </div>

                {{-- Edit info --}}
                @if($post->edited)
                    <div class="px-4 py-1 text-xs text-gray-400 border-t border-gray-100 italic">
                        Edited {{ \Carbon\Carbon::parse($post->edited)->diffForHumans() }}
                        @if($post->edited_by)
                            by <a href="{{ route('profile.show', $post->edited_by_id ?? 0) }}" class="hover:underline">{{ $post->edited_by }}</a>
                        @endif
                    </div>
                @endif

                {{-- Signature --}}
                @if($post->user && $post->user->signature && (auth()->check() ? auth()->user()->show_sig : true))
                    <div class="px-4 py-2 border-t border-gray-100 text-xs text-gray-500">
                        <hr class="border-gray-200 mb-2">
                        {!! \App\Helpers\BbCode::parse($post->user->signature) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach

{{-- Bottom pagination --}}
<div class="flex flex-wrap items-center justify-between mt-3 gap-2">
    @auth
        <div class="flex items-center gap-2">
            @if(!$topic->closed)
                <a href="{{ route('post.reply', $topic->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-1.5 rounded shadow-sm transition">
                    Post Reply
                </a>
            @endif
        </div>
    @endauth

    @if($posts->hasPages())
        <div class="text-sm">
            {{ $posts->links() }}
        </div>
    @endif
</div>

{{-- Quick reply box --}}
@auth
    @if(!$topic->closed)
        <div class="mt-6 bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
                Quick Reply
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route('post.store.reply', $topic->id) }}">
                    @csrf
                    {{-- BBCode toolbar --}}
                    <div class="flex flex-wrap gap-1 mb-2">
                        @foreach([['B','bold','**B**'],['I','italic','_I_'],['U','underline','U'],['Code','code','</>'],['Quote','quote','"'],['URL','url','URL'],['Img','img','IMG']] as [$label, $tag, $display])
                            <button type="button"
                                    onclick="insertBBCode('quick_message', '{{ $tag }}')"
                                    class="px-2 py-1 text-xs border border-gray-300 rounded bg-gray-50 hover:bg-gray-100 font-mono">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    <textarea id="quick_message" name="message" rows="8"
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 resize-y"
                              placeholder="Write your reply here...">{{ old('message') }}</textarea>
                    @if($errors->has('message'))
                        <div class="text-red-600 text-xs mt-1">{{ $errors->first('message') }}</div>
                    @endif
                    <div class="mt-3 flex items-center gap-3">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-1.5 rounded shadow-sm transition">
                            Post Reply
                        </button>
                        <a href="{{ route('post.reply', $topic->id) }}" class="text-sm text-blue-600 hover:underline">
                            Full reply form &rarr;
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endauth

@endsection

@push('scripts')
<script>
function insertBBCode(textareaId, tag) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) return;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selected = textarea.value.substring(start, end);

    let open = '[' + tag + ']';
    let close = '[/' + tag + ']';

    if (tag === 'url') {
        const url = prompt('Enter URL:', 'https://');
        if (!url) return;
        open = '[url=' + url + ']';
        close = '[/url]';
    } else if (tag === 'img') {
        const url = prompt('Enter image URL:', 'https://');
        if (!url) return;
        open = '[img]' + url;
        close = '[/img]';
        textarea.value = textarea.value.substring(0, start) + open + close + textarea.value.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + open.length + close.length;
        textarea.focus();
        return;
    }

    const replacement = open + selected + close;
    textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
    textarea.selectionStart = start + open.length;
    textarea.selectionEnd = start + open.length + selected.length;
    textarea.focus();
}
</script>
@endpush
