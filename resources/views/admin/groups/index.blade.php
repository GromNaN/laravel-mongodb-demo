@extends('admin.layout')

@section('title', 'Admin – Groups')

@section('admin-content')

<h1 class="text-xl font-bold text-gray-800 mb-4">Manage User Groups</h1>

@foreach($groups as $group)
    <div class="bg-white border border-gray-200 rounded overflow-hidden mb-5">
        <div class="bg-gray-800 text-white px-4 py-2 flex items-center justify-between">
            <span class="font-semibold text-sm">{{ $group->name }}</span>
            @if($group->id <= 4)
                <span class="text-xs text-gray-400">Built-in group</span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.groups.update', $group->id) }}">
            @csrf
            @method('PUT')

            <div class="p-4">
                {{-- Group basic info --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 pb-4 border-b border-gray-100">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Group Name</label>
                        <input type="text" name="name" value="{{ $group->name }}"
                               class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400"
                               {{ in_array($group->id, [1, 2, 3, 4]) ? 'readonly' : '' }}>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">User Title</label>
                        <input type="text" name="g_user_title" value="{{ $group->g_user_title }}"
                               placeholder="Default title for group members"
                               class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Post count requirement</label>
                        <input type="number" name="g_post_count" value="{{ $group->g_post_count ?? 0 }}"
                               min="0"
                               class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400">
                        <div class="text-xs text-gray-400 mt-0.5">Min. posts to reach this group (0 = manual only)</div>
                    </div>
                </div>

                {{-- Permission matrix --}}
                <div class="text-xs font-semibold text-gray-600 mb-3 uppercase tracking-wide">Permissions</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-2">
                    @php
                        $permissions = [
                            ['g_read_board', 'Read board'],
                            ['g_view_users', 'View user list'],
                            ['g_post_replies', 'Post replies'],
                            ['g_post_topics', 'Start new topics'],
                            ['g_edit_posts', 'Edit own posts'],
                            ['g_delete_posts', 'Delete own posts'],
                            ['g_delete_topics', 'Delete own topics'],
                            ['g_set_title', 'Set own title'],
                            ['g_search', 'Use search'],
                            ['g_search_users', 'Search users'],
                            ['g_send_email', 'Send email to users'],
                            ['g_post_flood', 'Subject to post flood check'],
                            ['g_search_flood', 'Subject to search flood check'],
                            ['g_email_flood', 'Subject to email flood check'],
                            ['g_report_flood', 'Subject to report flood check'],
                            ['g_upload_avatar', 'Upload avatar'],
                            ['g_use_signature', 'Use signature'],
                        ];
                    @endphp

                    @foreach($permissions as [$perm, $label])
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="{{ $perm }}" value="1"
                                   {{ old($perm . '_' . $group->id, $group->$perm ?? false) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>

                {{-- Moderator-level permissions (for mod groups) --}}
                @if($group->g_moderator ?? false)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="text-xs font-semibold text-orange-700 mb-3 uppercase tracking-wide">Moderator Permissions</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-2">
                            @foreach([
                                ['g_mod_edit_users', 'Edit users'],
                                ['g_mod_rename_users', 'Rename users'],
                                ['g_mod_change_passwords', 'Change user passwords'],
                                ['g_mod_ban_users', 'Ban users'],
                                ['g_mod_edit_posts', 'Edit any post'],
                                ['g_mod_delete_posts', 'Delete any post'],
                                ['g_mod_delete_topics', 'Delete any topic'],
                                ['g_mod_sticky', 'Sticky topics'],
                                ['g_mod_close_topic', 'Close topics'],
                                ['g_mod_move_topic', 'Move topics'],
                            ] as [$perm, $label])
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                    <input type="checkbox" name="{{ $perm }}" value="1"
                                           {{ old($perm . '_' . $group->id, $group->$perm ?? false) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-orange-600">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="border-t border-gray-100 px-4 py-3 bg-gray-50 flex items-center gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-1.5 rounded shadow-sm transition">
                    Save {{ $group->name }} Permissions
                </button>
                @if($group->id > 4)
                    <form method="POST" action="{{ route('admin.groups.destroy', $group->id) }}"
                          onsubmit="return confirm('Delete group &quot;{{ $group->name }}&quot;? Users in this group will be moved to the Members group.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-500 hover:underline">Delete group</button>
                    </form>
                @endif
            </div>
        </form>
    </div>
@endforeach

{{-- Add new group --}}
<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
        Create New Group
    </div>
    <div class="p-4">
        <form method="POST" action="{{ route('admin.groups.store') }}" class="flex flex-wrap items-end gap-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Group Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="e.g. VIP Members"
                       class="border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Based on</label>
                <select name="based_on"
                        class="border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400">
                    @foreach($groups as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-1.5 rounded shadow-sm transition">
                Create Group
            </button>
        </form>
    </div>
</div>

@endsection
