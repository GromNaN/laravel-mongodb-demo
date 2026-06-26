@extends('admin.layout')

@section('title', 'Admin – Edit User')

@section('admin-content')

<div class="flex items-center gap-3 mb-4">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to Users</a>
    <span class="text-gray-400">/</span>
    <h1 class="text-xl font-bold text-gray-800">Edit User: {{ $user->username }}</h1>
</div>

<form method="POST" action="{{ route('admin.users.update', $user->id) }}">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Account info --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden md:col-span-2">
            <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
                Account Information
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" name="username"
                           value="{{ old('username', $user->username) }}"
                           maxlength="25"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 {{ $errors->has('username') ? 'border-red-400' : '' }}">
                    @if($errors->has('username'))
                        <div class="text-red-600 text-xs mt-1">{{ $errors->first('username') }}</div>
                    @endif
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 {{ $errors->has('email') ? 'border-red-400' : '' }}">
                    @if($errors->has('email'))
                        <div class="text-red-600 text-xs mt-1">{{ $errors->first('email') }}</div>
                    @endif
                </div>

                <div>
                    <label for="group_id" class="block text-sm font-semibold text-gray-700 mb-1">User Group</label>
                    <select id="group_id" name="group_id"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        @if(isset($groups))
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" {{ old('group_id', $user->group_id) == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">
                        Custom Title <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input type="text" id="title" name="title"
                           value="{{ old('title', $user->title) }}"
                           maxlength="50"
                           placeholder="Override group title"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>
            </div>
        </div>

        {{-- Moderation --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
                Moderation
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <label for="admin_note" class="block text-sm font-semibold text-gray-700 mb-1">
                        Admin Note <span class="text-gray-400 font-normal">(internal)</span>
                    </label>
                    <textarea id="admin_note" name="admin_note" rows="3"
                              placeholder="Notes visible only to administrators..."
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 resize-y">{{ old('admin_note', $user->admin_note) }}</textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="is_banned" value="1"
                               {{ old('is_banned', $user->banned) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-red-600">
                        <span class="font-semibold text-red-700">Ban this user</span>
                    </label>
                    <div class="text-xs text-gray-500 mt-1 ml-6">
                        Banned users cannot log in or post.
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats (read-only) --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
                User Statistics
            </div>
            <div class="p-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">User ID:</span>
                    <span class="text-gray-700 font-mono">{{ $user->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Registered:</span>
                    <span class="text-gray-700">{{ \Carbon\Carbon::parse($user->registered)->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Last visit:</span>
                    <span class="text-gray-700">{{ $user->last_visit ? \Carbon\Carbon::parse($user->last_visit)->diffForHumans() : 'Never' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total posts:</span>
                    <span class="text-gray-700">{{ number_format($user->num_posts ?? 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Registration IP:</span>
                    <span class="text-gray-700 font-mono text-xs">{{ $user->registration_ip ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Last IP:</span>
                    <span class="text-gray-700 font-mono text-xs">{{ $user->last_ip ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Password reset --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden md:col-span-2">
            <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
                Reset Password <span class="text-gray-400 font-normal">(leave blank to keep current)</span>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-1">New Password</label>
                    <input type="password" id="new_password" name="new_password"
                           autocomplete="new-password"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                           autocomplete="new-password"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="mt-4 flex items-center gap-3">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm transition">
            Save Changes
        </button>
        <a href="{{ route('admin.users.index') }}"
           class="text-sm text-gray-600 hover:underline px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
            Cancel
        </a>
        <div class="flex-1"></div>
        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
              onsubmit="return confirm('Permanently delete user {{ $user->username }} and all their posts? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="text-sm text-red-600 hover:underline px-4 py-2 border border-red-200 rounded hover:bg-red-50 transition">
                Delete User
            </button>
        </form>
    </div>
</form>

@endsection
