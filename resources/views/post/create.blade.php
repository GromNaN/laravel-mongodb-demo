@extends('layouts.app')

@section('title', 'New Topic')

@section('content')

{{-- Breadcrumb --}}
<div class="text-xs text-gray-500 mb-3 flex items-center flex-wrap gap-1">
    <a href="{{ url('/') }}" class="text-blue-600 hover:underline">Index</a>
    <span class="text-gray-400">&raquo;</span>
    @if(isset($forum->category))
        <a href="{{ url('/') }}#c{{ $forum->category->id }}" class="text-blue-600 hover:underline">{{ $forum->category->name }}</a>
        <span class="text-gray-400">&raquo;</span>
    @endif
    <a href="{{ route('forum.show', $forum->id) }}" class="text-blue-600 hover:underline">{{ $forum->name }}</a>
    <span class="text-gray-400">&raquo;</span>
    <span class="text-gray-700">New Topic</span>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
        Post New Topic
    </div>

    <div class="p-4">
        <form method="POST" action="{{ route('post.store', $forum->id) }}">
            @csrf

            {{-- Subject --}}
            <div class="mb-4">
                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-1">
                    Subject <span class="text-red-500">*</span>
                </label>
                <input type="text" id="subject" name="subject"
                       value="{{ old('subject') }}"
                       maxlength="70"
                       placeholder="Topic subject (max 70 characters)"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('subject') ? 'border-red-400' : '' }}">
                @if($errors->has('subject'))
                    <div class="text-red-600 text-xs mt-1">{{ $errors->first('subject') }}</div>
                @endif
            </div>

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
                        ['List item','*'],
                    ] as [$label, $tag])
                        <button type="button"
                                onclick="insertBBCode('message', '{{ $tag }}')"
                                class="px-2.5 py-1 text-xs border border-gray-300 rounded bg-gray-50 hover:bg-gray-100 font-semibold text-gray-700 transition">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="text-xs text-gray-400 mt-1">
                    BBCode help: [b]bold[/b], [i]italic[/i], [u]underline[/u], [code]code[/code], [quote]quote[/quote], [url=http://]link[/url], [img]url[/img]
                </div>
            </div>

            {{-- Message --}}
            <div class="mb-4">
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-1">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea id="message" name="message" rows="20"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 resize-y {{ $errors->has('message') ? 'border-red-400' : '' }}"
                          placeholder="Write your post here using BBCode...">{{ old('message') }}</textarea>
                @if($errors->has('message'))
                    <div class="text-red-600 text-xs mt-1">{{ $errors->first('message') }}</div>
                @endif
            </div>

            {{-- Options row --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-100 pt-4">
                <div class="flex items-center gap-4">
                    {{-- Subscribe checkbox --}}
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="subscribe" value="1"
                               {{ old('subscribe', auth()->user()->auto_subscribe ?? false) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        Subscribe to topic
                    </label>
                    {{-- Preview checkbox (triggers preview section) --}}
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="preview" value="1"
                               {{ old('preview') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        Preview before posting
                    </label>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('forum.show', $forum->id) }}"
                       class="text-sm text-gray-600 hover:underline px-4 py-1.5 border border-gray-300 rounded hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-1.5 rounded shadow-sm transition">
                        Submit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Preview section --}}
@if(old('preview') && old('message'))
    <div class="mt-6 bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
            Preview
        </div>
        <div class="p-4 text-sm leading-relaxed">
            @if(old('subject'))
                <h3 class="text-lg font-bold text-gray-800 mb-3">{{ old('subject') }}</h3>
            @endif
            {!! \App\Helpers\BbCode::toHtml(old('message')) !!}
        </div>
    </div>
@endif

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
    } else if (tag === '*') {
        open = '[*]';
        close = '';
    }

    const replacement = open + selected + close;
    textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
    if (close) {
        textarea.selectionStart = start + open.length;
        textarea.selectionEnd = start + open.length + selected.length;
    } else {
        textarea.selectionStart = textarea.selectionEnd = start + open.length + selected.length;
    }
    textarea.focus();
}
</script>
@endpush
