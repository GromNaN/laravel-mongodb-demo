@extends('admin.layout')

@section('title', 'Admin – Edit Forum')

@section('admin-content')

<div class="flex items-center gap-3 mb-4">
    <a href="{{ route('admin.forums.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to Forums</a>
    <span class="text-gray-400">/</span>
    <h1 class="text-xl font-bold text-gray-800">Edit Forum: {{ $forum->name }}</h1>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden max-w-2xl">
    <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
        Forum Settings
    </div>
    <div class="p-5">
        <form method="POST" action="{{ route('admin.forums.update', $forum->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                    Forum Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $forum->name) }}"
                       maxlength="80"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 {{ $errors->has('name') ? 'border-red-400' : '' }}">
                @if($errors->has('name'))
                    <div class="text-red-600 text-xs mt-1">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 resize-y">{{ old('description', $forum->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        @if(isset($categories))
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $forum->cat_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label for="disp_position" class="block text-sm font-semibold text-gray-700 mb-1">Position</label>
                    <input type="number" id="disp_position" name="disp_position"
                           value="{{ old('disp_position', $forum->disp_position) }}"
                           min="1"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>
            </div>

            <div>
                <label for="sort_by" class="block text-sm font-semibold text-gray-700 mb-1">Default sort order</label>
                <select id="sort_by" name="sort_by"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                    <option value="last_post" {{ old('sort_by', $forum->sort_by) === 'last_post' ? 'selected' : '' }}>By last post</option>
                    <option value="posted" {{ old('sort_by', $forum->sort_by) === 'posted' ? 'selected' : '' }}>By topic start date</option>
                    <option value="subject" {{ old('sort_by', $forum->sort_by) === 'subject' ? 'selected' : '' }}>By subject</option>
                </select>
            </div>

            <div>
                <label for="redirect_url" class="block text-sm font-semibold text-gray-700 mb-1">
                    Redirect URL <span class="text-gray-400 font-normal">(leave blank for normal forum)</span>
                </label>
                <input type="url" id="redirect_url" name="redirect_url"
                       value="{{ old('redirect_url', $forum->redirect_url) }}"
                       placeholder="https://example.com"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
            </div>

            <div>
                <label for="moderators" class="block text-sm font-semibold text-gray-700 mb-1">
                    Moderators <span class="text-gray-400 font-normal">(comma-separated usernames)</span>
                </label>
                <input type="text" id="moderators" name="moderators"
                       value="{{ old('moderators', is_array($forum->moderators) ? implode(', ', $forum->moderators) : $forum->moderators) }}"
                       placeholder="username1, username2"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
            </div>

            <div class="space-y-2 pt-2 border-t border-gray-100">
                <div class="text-xs font-semibold text-gray-600 mb-1">Permissions</div>
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input type="checkbox" name="post_topics" value="1"
                           {{ old('post_topics', $forum->post_topics ?? true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600">
                    Allow registered users to post new topics
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input type="checkbox" name="post_replies" value="1"
                           {{ old('post_replies', $forum->post_replies ?? true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600">
                    Allow registered users to post replies
                </label>
            </div>

            {{-- Danger zone --}}
            <div class="border border-red-200 rounded p-3 bg-red-50 space-y-2">
                <div class="text-xs font-semibold text-red-700">Danger Zone</div>
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input type="checkbox" name="purge_posts" value="1"
                           class="rounded border-gray-300 text-red-600">
                    <span class="text-red-700">Mark all posts in this forum as read (cannot be undone)</span>
                </label>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm transition">
                    Save Changes
                </button>
                <a href="{{ route('admin.forums.index') }}"
                   class="text-sm text-gray-600 hover:underline">Cancel</a>
                <div class="flex-1"></div>
                <form method="POST" action="{{ route('admin.forums.destroy', $forum->id) }}"
                      onsubmit="return confirm('Delete forum &quot;{{ $forum->name }}&quot;? All topics and posts inside will be permanently deleted!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-sm text-red-600 hover:underline border border-red-200 px-4 py-2 rounded hover:bg-red-50 transition">
                        Delete Forum
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>

{{-- Forum stats --}}
<div class="mt-4 bg-white border border-gray-200 rounded overflow-hidden max-w-2xl">
    <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
        Forum Statistics
    </div>
    <div class="p-4 grid grid-cols-3 gap-4 text-sm text-center">
        <div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($forum->num_topics ?? 0) }}</div>
            <div class="text-xs text-gray-500">Topics</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-gray-800">{{ number_format($forum->num_posts ?? 0) }}</div>
            <div class="text-xs text-gray-500">Posts</div>
        </div>
        <div>
            <div class="text-sm font-semibold text-gray-800">
                {{ $forum->last_post ? \Carbon\Carbon::parse($forum->last_post)->diffForHumans() : 'Never' }}
            </div>
            <div class="text-xs text-gray-500">Last post</div>
        </div>
    </div>
</div>

@endsection
