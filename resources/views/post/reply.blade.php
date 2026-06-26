@extends('layouts.app')

@section('title', 'Post Reply')

@section('content')

{{-- Breadcrumb --}}
<div class="text-xs text-gray-500 mb-3 flex items-center flex-wrap gap-1">
    <a href="{{ url('/') }}" class="text-blue-600 hover:underline">Index</a>
    <span class="text-gray-400">&raquo;</span>
    @if(isset($topic->forum->category))
        <a href="{{ url('/') }}#c{{ $topic->forum->category->id }}" class="text-blue-600 hover:underline">{{ $topic->forum->category->name }}</a>
        <span class="text-gray-400">&raquo;</span>
    @endif
    <a href="{{ route('forum.show', $topic->forum->id) }}" class="text-blue-600 hover:underline">{{ $topic->forum->name }}</a>
    <span class="text-gray-400">&raquo;</span>
    <a href="{{ route('topic.show', $topic->id) }}" class="text-blue-600 hover:underline">{{ $topic->subject }}</a>
    <span class="text-gray-400">&raquo;</span>
    <span class="text-gray-700">Post Reply</span>
</div>

{{-- Recent posts context (collapsible) --}}
@if(isset($recentPosts) && $recentPosts->count())
    <div class="mb-4 bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-gray-100 border-b border-gray-200 flex items-center justify-between px-4 py-2">
            <span class="text-sm font-semibold text-gray-700">Recent Posts in This Topic</span>
            <button onclick="toggleContext()" id="ctx-toggle"
                    class="text-xs text-blue-600 hover:underline">Hide</button>
        </div>
        <div id="ctx-posts" class="divide-y divide-gray-100">
            @foreach($recentPosts as $recentPost)
                <div class="flex gap-3 px-4 py-3">
                    <div class="flex-shrink-0 w-28 text-xs text-gray-500">
                        <div class="font-semibold text-gray-700">{{ $recentPost->poster }}</div>
                        <div class="text-gray-400">{{ \Carbon\Carbon::parse($recentPost->posted)->format('M d, H:i') }}</div>
                    </div>
                    <div class="flex-1 text-sm text-gray-700 leading-relaxed">
                        {!! \App\Helpers\BbCode::toHtml(Str::limit($recentPost->message, 300)) !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Reply form --}}
<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
        Post Reply to: {{ $topic->subject }}
    </div>

    <div class="p-4">
        <form method="POST" action="{{ route('post.storeReply', $topic->id) }}">
            @csrf

            {{-- Quoted content (if replying to a specific post) --}}
            @if(isset($quotedPost))
                <div class="mb-4 bg-gray-50 border border-gray-200 rounded p-3 text-sm">
                    <div class="text-xs text-gray-500 mb-1 font-semibold">Quoting {{ $quotedPost->poster }}:</div>
                    <div class="text-gray-700 italic">{{ Str::limit(strip_tags($quotedPost->message), 200) }}</div>
                </div>
            @endif

            {{-- BBCode toolbar --}}
            <div class="mb-2">
                <div class="text-xs font-semibold text-gray-600 mb-1">Formatting:</div>
                <div class="flex flex-wrap gap-1">
                    @foreach([
                        ['Bold','bold'],
                        ['Italic','italic'],
                        ['Underline','underline'],
                        ['Code','code'],
                        ['Quote','quote'],
                        ['URL','url'],
                        ['Image','img'],
                        ['List','list'],
                    ] as [$label, $tag])
                        <button type="button"
                                onclick="insertBBCode('message', '{{ $tag }}')"
                                class="px-2.5 py-1 text-xs border border-gray-300 rounded bg-gray-50 hover:bg-gray-100 font-semibold text-gray-700 transition">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="text-xs text-gray-400 mt-1">
                    BBCode: [b]bold[/b] [i]italic[/i] [code]code[/code] [quote]text[/quote] [url=link]text[/url] [img]url[/img]
                </div>
            </div>

            {{-- Message textarea --}}
            <div class="mb-4">
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-1">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea id="message" name="message" rows="15"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 resize-y {{ $errors->has('message') ? 'border-red-400' : '' }}"
                          placeholder="Write your reply here...">{{ old('message', isset($quotedPost) ? '[quote=' . $quotedPost->poster . ']' . $quotedPost->message . '[/quote]' . "\n\n" : '') }}</textarea>
                @if($errors->has('message'))
                    <div class="text-red-600 text-xs mt-1">{{ $errors->first('message') }}</div>
                @endif
            </div>

            {{-- Options & submit --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-100 pt-4">
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="subscribe" value="1"
                               {{ old('subscribe', auth()->user()->auto_subscribe ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        Subscribe to topic
                    </label>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('topic.show', $topic->id) }}"
                       class="text-sm text-gray-600 hover:underline px-4 py-1.5 border border-gray-300 rounded hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-1.5 rounded shadow-sm transition">
                        Post Reply
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleContext() {
    const el = document.getElementById('ctx-posts');
    const btn = document.getElementById('ctx-toggle');
    if (el.style.display === 'none') {
        el.style.display = '';
        btn.textContent = 'Hide';
    } else {
        el.style.display = 'none';
        btn.textContent = 'Show';
    }
}

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
