@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')

{{-- Breadcrumb --}}
<div class="text-xs text-gray-500 mb-3 flex items-center flex-wrap gap-1">
    <a href="{{ url('/') }}" class="text-blue-600 hover:underline">Index</a>
    <span class="text-gray-400">&raquo;</span>
    @if(isset($post->topic->forum->category))
        <a href="{{ url('/') }}#c{{ $post->topic->forum->category->id }}" class="text-blue-600 hover:underline">{{ $post->topic->forum->category->name }}</a>
        <span class="text-gray-400">&raquo;</span>
    @endif
    <a href="{{ route('forum.show', $post->topic->forum->id) }}" class="text-blue-600 hover:underline">{{ $post->topic->forum->name }}</a>
    <span class="text-gray-400">&raquo;</span>
    <a href="{{ route('topic.show', $post->topic->id) }}" class="text-blue-600 hover:underline">{{ $post->topic->subject }}</a>
    <span class="text-gray-400">&raquo;</span>
    <span class="text-gray-700">Edit Post</span>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
        Edit Post
    </div>

    <div class="p-4">
        <form method="POST" action="{{ route('post.update', $post->id) }}">
            @csrf
            @method('PUT')

            {{-- First post in topic: allow editing subject --}}
            @if($isFirstPost ?? false)
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-semibold text-gray-700 mb-1">
                        Topic Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="subject" name="subject"
                           value="{{ old('subject', $post->topic->subject) }}"
                           maxlength="70"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('subject') ? 'border-red-400' : '' }}">
                    @if($errors->has('subject'))
                        <div class="text-red-600 text-xs mt-1">{{ $errors->first('subject') }}</div>
                    @endif
                </div>
            @endif

            {{-- Post info --}}
            <div class="mb-3 text-xs text-gray-500 bg-gray-50 border border-gray-100 rounded px-3 py-2">
                Post by <strong class="text-gray-700">{{ $post->poster }}</strong>
                on {{ \Carbon\Carbon::parse($post->posted)->format('D, d M Y H:i') }}
                @if($post->edited)
                    &bull; Last edited {{ \Carbon\Carbon::parse($post->edited)->diffForHumans() }}
                    @if($post->edited_by)
                        by {{ $post->edited_by }}
                    @endif
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
                    ] as [$label, $tag])
                        <button type="button"
                                onclick="insertBBCode('message', '{{ $tag }}')"
                                class="px-2.5 py-1 text-xs border border-gray-300 rounded bg-gray-50 hover:bg-gray-100 font-semibold text-gray-700 transition">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Message --}}
            <div class="mb-4">
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-1">
                    Message <span class="text-red-500">*</span>
                </label>
                <textarea id="message" name="message" rows="20"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 resize-y {{ $errors->has('message') ? 'border-red-400' : '' }}">{{ old('message', $post->message) }}</textarea>
                @if($errors->has('message'))
                    <div class="text-red-600 text-xs mt-1">{{ $errors->first('message') }}</div>
                @endif
            </div>

            {{-- Edit reason (optional) --}}
            <div class="mb-4">
                <label for="edit_reason" class="block text-sm font-semibold text-gray-700 mb-1">
                    Reason for edit <span class="text-gray-400 font-normal">(optional)</span>
                </label>
                <input type="text" id="edit_reason" name="edit_reason"
                       value="{{ old('edit_reason') }}"
                       maxlength="100"
                       placeholder="Briefly describe your changes"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
            </div>

            {{-- Admin options --}}
            @if(auth()->check() && auth()->user()->group_id == 1)
                <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded p-3">
                    <div class="text-xs font-semibold text-yellow-800 mb-2">Moderator Options</div>
                    <div class="flex flex-wrap gap-4">
                        @if($isFirstPost ?? false)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="sticky" value="1"
                                       {{ old('sticky', $post->topic->sticky) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600">
                                Sticky topic
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="closed" value="1"
                                       {{ old('closed', $post->topic->closed) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600">
                                Close topic
                            </label>
                        @endif
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="hide_smilies" value="1"
                                   {{ old('hide_smilies', $post->hide_smilies) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600">
                            Disable smilies in this post
                        </label>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-100 pt-4">
                <div>
                    {{-- Preview button --}}
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="preview" value="1"
                               {{ old('preview') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        Preview changes
                    </label>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('topic.show', $post->topic->id) }}#p{{ $post->id }}"
                       class="text-sm text-gray-600 hover:underline px-4 py-1.5 border border-gray-300 rounded hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    @if(auth()->check() && auth()->user()->group_id == 1)
                        <form method="POST" action="{{ route('post.destroy', $post->id) }}"
                              onsubmit="return confirm('Permanently delete this post? This cannot be undone.');"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-sm text-red-600 hover:underline px-4 py-1.5 border border-red-200 rounded hover:bg-red-50 transition">
                                Delete Post
                            </button>
                        </form>
                    @endif
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-1.5 rounded shadow-sm transition">
                        Save Changes
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
            {!! \App\Helpers\BbCode::parse(old('message', '')) !!}
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
    }

    const replacement = open + selected + close;
    textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
    textarea.selectionStart = start + open.length;
    textarea.selectionEnd = start + open.length + selected.length;
    textarea.focus();
}
</script>
@endpush
