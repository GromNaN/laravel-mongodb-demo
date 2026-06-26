@extends('admin.layout')

@section('title', 'Admin – Categories')

@section('admin-content')

<h1 class="text-xl font-bold text-gray-800 mb-4">Manage Categories</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    {{-- Add category form --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
            Add New Category
        </div>
        <div class="p-4">
            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label for="cat_name" class="block text-xs font-semibold text-gray-600 mb-1">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="cat_name" name="name"
                           value="{{ old('name') }}"
                           maxlength="80"
                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400 {{ $errors->has('name') ? 'border-red-400' : '' }}">
                    @if($errors->has('name'))
                        <div class="text-red-600 text-xs mt-0.5">{{ $errors->first('name') }}</div>
                    @endif
                </div>
                <div>
                    <label for="cat_position" class="block text-xs font-semibold text-gray-600 mb-1">Position</label>
                    <input type="number" id="cat_position" name="disp_position"
                           value="{{ old('disp_position', ($categories->count() + 1)) }}"
                           min="1"
                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400">
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-1.5 rounded shadow-sm transition">
                    Add Category
                </button>
            </form>
        </div>
    </div>

    {{-- Categories list --}}
    <div class="md:col-span-2 space-y-3">
        @forelse($categories as $category)
            <div id="cat-{{ $category->id }}" class="bg-white border border-gray-200 rounded overflow-hidden">
                <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 flex items-center justify-between">
                    <span class="font-semibold text-sm text-gray-700">
                        #{{ $loop->iteration }} &mdash; {{ $category->name }}
                    </span>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('admin.categories.moveup', $category->id) }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-gray-700 text-xs border border-gray-300 px-1.5 py-0.5 rounded hover:bg-gray-200"
                                    title="Move up" {{ $loop->first ? 'disabled' : '' }}>&#9650;</button>
                        </form>
                        <form method="POST" action="{{ route('admin.categories.movedown', $category->id) }}">
                            @csrf
                            <button type="submit" class="text-gray-400 hover:text-gray-700 text-xs border border-gray-300 px-1.5 py-0.5 rounded hover:bg-gray-200"
                                    title="Move down" {{ $loop->last ? 'disabled' : '' }}>&#9660;</button>
                        </form>
                    </div>
                </div>

                <div class="p-3">
                    <form method="POST" action="{{ route('admin.categories.update', $category->id) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PUT')
                        <input type="text" name="name"
                               value="{{ $category->name }}"
                               maxlength="80"
                               class="flex-1 border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400">
                        <input type="number" name="disp_position"
                               value="{{ $category->disp_position }}"
                               min="1"
                               class="w-20 border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400"
                               title="Position">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition whitespace-nowrap">
                            Save
                        </button>
                    </form>

                    <div class="mt-2 flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            {{ $category->forums->count() }} forum(s)
                        </div>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}"
                              onsubmit="return confirm('Delete category &quot;{{ $category->name }}&quot;? All forums inside must be moved or deleted first.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-500 hover:underline"
                                    {{ $category->forums->count() > 0 ? 'disabled title=\'Move all forums out first\'' : '' }}>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded p-6 text-center text-gray-500 italic text-sm">
                No categories yet. Create one using the form.
            </div>
        @endforelse
    </div>
</div>

@endsection
