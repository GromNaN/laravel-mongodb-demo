@extends('admin.layout')

@section('title', 'Admin – Forums')

@section('admin-content')

<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold text-gray-800">Manage Forums</h1>
    <a href="#create-forum"
       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded shadow-sm transition">
        + Add Forum
    </a>
</div>

@forelse($categories as $category)
    <div class="mb-5">
        {{-- Category header --}}
        <div class="bg-gray-800 text-white px-4 py-2 rounded-t flex items-center justify-between">
            <span class="font-semibold text-sm">{{ $category->name }}</span>
            <div class="flex items-center gap-3 text-xs">
                <form method="POST" action="{{ route('admin.categories.moveup', $category->id) }}">
                    @csrf
                    <button type="submit" class="text-gray-300 hover:text-white" title="Move up">&#9650;</button>
                </form>
                <form method="POST" action="{{ route('admin.categories.movedown', $category->id) }}">
                    @csrf
                    <button type="submit" class="text-gray-300 hover:text-white" title="Move down">&#9660;</button>
                </form>
                <a href="{{ route('admin.categories.index') }}#cat-{{ $category->id }}"
                   class="text-blue-300 hover:underline">Edit category</a>
            </div>
        </div>

        {{-- Forums in category --}}
        <div class="bg-white border border-l border-r border-gray-200">
            @forelse($category->forums as $forum)
                <div class="border-b border-gray-100 flex items-center gap-3 px-4 py-3 hover:bg-gray-50
                            {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                    {{-- Reorder --}}
                    <div class="flex flex-col items-center gap-0.5">
                        <form method="POST" action="{{ route('admin.forums.moveup', $forum->id) }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-gray-700 text-xs leading-none" title="Move up">&#9650;</button>
                        </form>
                        <form method="POST" action="{{ route('admin.forums.movedown', $forum->id) }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-gray-700 text-xs leading-none" title="Move down">&#9660;</button>
                        </form>
                    </div>

                    {{-- Forum info --}}
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm">
                            <a href="{{ route('forum.show', $forum->id) }}" class="text-blue-700 hover:underline" target="_blank">
                                {{ $forum->name }}
                            </a>
                            @if($forum->redirect_url)
                                <span class="text-xs text-orange-500 ml-1">[Redirect]</span>
                            @endif
                        </div>
                        @if($forum->description)
                            <div class="text-xs text-gray-500">{{ $forum->description }}</div>
                        @endif
                        <div class="text-xs text-gray-400 mt-0.5">
                            Posts: {{ number_format($forum->num_posts ?? 0) }} &bull;
                            Topics: {{ number_format($forum->num_topics ?? 0) }} &bull;
                            Position: {{ $forum->disp_position ?? $loop->iteration }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('admin.forums.edit', $forum->id) }}"
                           class="text-xs text-blue-600 hover:underline border border-blue-200 px-2 py-1 rounded hover:bg-blue-50 transition">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.forums.destroy', $forum->id) }}"
                              onsubmit="return confirm('Delete forum &quot;{{ $forum->name }}&quot;? All topics and posts will be permanently deleted!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-500 hover:underline border border-red-200 px-2 py-1 rounded hover:bg-red-50 transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-4 py-3 text-sm text-gray-500 italic">No forums in this category.</div>
            @endforelse
        </div>

        {{-- Add forum to this category --}}
        <div class="border border-t-0 border-gray-200 rounded-b bg-gray-50 px-4 py-2">
            <a href="#create-forum"
               class="text-xs text-blue-600 hover:underline">
                + Add forum to {{ $category->name }}
            </a>
        </div>
    </div>
@empty
    <div class="bg-white border border-gray-200 rounded p-6 text-center text-gray-500 italic text-sm">
        No categories exist yet.
        <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:underline ml-1">Create a category first.</a>
    </div>
@endforelse

@endsection
