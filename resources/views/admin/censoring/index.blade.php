@extends('admin.layout')

@section('title', 'Admin – Censoring')

@section('admin-content')

<h1 class="text-xl font-bold text-gray-800 mb-4">Word Censoring</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    {{-- Add rule form --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
            Add Censoring Rule
        </div>
        <div class="p-4">
            <form method="POST" action="{{ route('admin.censoring.store') }}" class="space-y-3">
                @csrf

                <div>
                    <label for="search_for" class="block text-xs font-semibold text-gray-600 mb-1">
                        Search for <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="search_for" name="search_for"
                           value="{{ old('search_for') }}"
                           placeholder="Word or phrase to censor"
                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400 {{ $errors->has('search_for') ? 'border-red-400' : '' }}">
                    @if($errors->has('search_for'))
                        <div class="text-red-600 text-xs mt-0.5">{{ $errors->first('search_for') }}</div>
                    @endif
                    <div class="text-xs text-gray-400 mt-1">
                        Use * as wildcard (e.g. bad* matches badword, badly, etc.)
                    </div>
                </div>

                <div>
                    <label for="replace_with" class="block text-xs font-semibold text-gray-600 mb-1">
                        Replace with <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input type="text" id="replace_with" name="replace_with"
                           value="{{ old('replace_with') }}"
                           placeholder="Replacement text (blank = remove)"
                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400">
                    <div class="text-xs text-gray-400 mt-1">
                        Leave blank to replace with *** or remove entirely.
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-1.5 rounded shadow-sm transition">
                    Add Rule
                </button>
            </form>
        </div>
    </div>

    {{-- Censoring rules list --}}
    <div class="md:col-span-2">
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 flex items-center justify-between">
                <span class="font-semibold text-sm text-gray-700">Censoring Rules</span>
                <span class="text-xs text-gray-500">{{ $censorings->total() }} rule(s)</span>
            </div>

            @if($censorings->isEmpty())
                <div class="p-6 text-center text-gray-500 italic text-sm">
                    No censoring rules defined.
                </div>
            @else
                {{-- Table header --}}
                <div class="grid grid-cols-12 px-4 py-2 bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wide">
                    <div class="col-span-5">Search for</div>
                    <div class="col-span-5">Replace with</div>
                    <div class="col-span-2 text-right">Action</div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($censorings as $censoring)
                        <form method="POST" action="{{ route('admin.censoring.update', $censoring->id) }}"
                              class="grid grid-cols-12 gap-2 px-4 py-2 hover:bg-gray-50 items-center">
                            @csrf
                            @method('PUT')
                            <div class="col-span-5">
                                <input type="text" name="search_for"
                                       value="{{ $censoring->search_for }}"
                                       class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:border-blue-400 font-mono text-xs">
                            </div>
                            <div class="col-span-5">
                                <input type="text" name="replace_with"
                                       value="{{ $censoring->replace_with }}"
                                       placeholder="(remove)"
                                       class="w-full border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:border-blue-400 font-mono text-xs">
                            </div>
                            <div class="col-span-2 flex items-center justify-end gap-1.5">
                                <button type="submit"
                                        class="text-xs text-blue-600 hover:underline border border-blue-200 px-1.5 py-0.5 rounded hover:bg-blue-50 transition">
                                    Save
                                </button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.censoring.destroy', $censoring->id) }}"
                              onsubmit="return confirm('Delete this censoring rule?')"
                              class="hidden" id="del-censor-{{ $censoring->id }}">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                </div>

                {{-- Delete buttons row (outside PUT form) --}}
                <div class="divide-y divide-gray-100">
                    @foreach($censorings as $censoring)
                        <div class="flex justify-end px-4 py-1">
                            <button type="button"
                                    onclick="document.getElementById('del-censor-{{ $censoring->id }}').submit()"
                                    class="text-xs text-red-500 hover:underline">
                                Delete
                            </button>
                        </div>
                    @endforeach
                </div>

                @if($censorings->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $censorings->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Help box --}}
        <div class="mt-3 bg-blue-50 border border-blue-200 rounded p-4 text-sm text-blue-800">
            <div class="font-semibold mb-1">Censoring Notes</div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                <li>Censoring is applied to all new posts and topic subjects.</li>
                <li>Existing posts are not retroactively censored until edited.</li>
                <li>Use <code class="bg-blue-100 px-1 rounded font-mono">*</code> as a wildcard to match partial words.</li>
                <li>Censoring is case-insensitive by default.</li>
                <li>The replacement text is displayed to users; the original is still stored.</li>
            </ul>
        </div>
    </div>
</div>

@endsection
