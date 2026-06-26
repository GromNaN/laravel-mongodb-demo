@extends('admin.layout')

@section('title', 'Admin – Bans')

@section('admin-content')

<h1 class="text-xl font-bold text-gray-800 mb-4">Manage Bans</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    {{-- Add ban form --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 font-semibold text-sm text-gray-700">
            Add New Ban
        </div>
        <div class="p-4">
            <form method="POST" action="{{ route('admin.bans.store') }}" class="space-y-3">
                @csrf

                <div>
                    <label for="ban_username" class="block text-xs font-semibold text-gray-600 mb-1">Username</label>
                    <input type="text" id="ban_username" name="username"
                           value="{{ old('username') }}"
                           placeholder="Exact username (optional)"
                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400 {{ $errors->has('username') ? 'border-red-400' : '' }}">
                    @if($errors->has('username'))
                        <div class="text-red-600 text-xs mt-0.5">{{ $errors->first('username') }}</div>
                    @endif
                </div>

                <div>
                    <label for="ban_ip" class="block text-xs font-semibold text-gray-600 mb-1">IP Address</label>
                    <input type="text" id="ban_ip" name="ip"
                           value="{{ old('ip') }}"
                           placeholder="e.g. 192.168.1.1 or 192.168.*"
                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400 {{ $errors->has('ip') ? 'border-red-400' : '' }}">
                    @if($errors->has('ip'))
                        <div class="text-red-600 text-xs mt-0.5">{{ $errors->first('ip') }}</div>
                    @endif
                </div>

                <div>
                    <label for="ban_email" class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                    <input type="text" id="ban_email" name="email"
                           value="{{ old('email') }}"
                           placeholder="email@example.com or *@domain.com"
                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400 {{ $errors->has('email') ? 'border-red-400' : '' }}">
                    @if($errors->has('email'))
                        <div class="text-red-600 text-xs mt-0.5">{{ $errors->first('email') }}</div>
                    @endif
                </div>

                <div>
                    <label for="ban_message" class="block text-xs font-semibold text-gray-600 mb-1">Ban Message</label>
                    <textarea id="ban_message" name="message" rows="3"
                              placeholder="Reason shown to the banned user..."
                              class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400 resize-y">{{ old('message') }}</textarea>
                </div>

                <div>
                    <label for="ban_expire" class="block text-xs font-semibold text-gray-600 mb-1">
                        Expiration date <span class="text-gray-400">(blank = permanent)</span>
                    </label>
                    <input type="date" id="ban_expire" name="expire_date"
                           value="{{ old('expire_date') }}"
                           min="{{ now()->addDay()->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded px-2.5 py-1.5 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div class="text-xs text-gray-400 bg-gray-50 rounded p-2">
                    At least one of username, IP, or email must be specified.
                </div>

                <button type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2 rounded shadow-sm transition">
                    Add Ban
                </button>
            </form>
        </div>
    </div>

    {{-- Bans list --}}
    <div class="md:col-span-2">
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 flex items-center justify-between">
                <span class="font-semibold text-sm text-gray-700">Active Bans</span>
                <span class="text-xs text-gray-500">{{ $bans->total() }} total</span>
            </div>

            @if($bans->isEmpty())
                <div class="p-6 text-center text-gray-500 italic text-sm">
                    No bans currently active.
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($bans as $ban)
                        <div class="px-4 py-3 hover:bg-gray-50">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap gap-2 text-xs mb-1">
                                        @if($ban->username)
                                            <span class="bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-semibold">
                                                User: {{ $ban->username }}
                                            </span>
                                        @endif
                                        @if($ban->ip)
                                            <span class="bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded font-mono">
                                                IP: {{ $ban->ip }}
                                            </span>
                                        @endif
                                        @if($ban->email)
                                            <span class="bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded">
                                                Email: {{ $ban->email }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($ban->message)
                                        <div class="text-xs text-gray-600 italic mb-1">
                                            &ldquo;{{ $ban->message }}&rdquo;
                                        </div>
                                    @endif

                                    <div class="text-xs text-gray-400">
                                        Banned {{ \Carbon\Carbon::parse($ban->created_at)->diffForHumans() }}
                                        @if($ban->expire_date)
                                            &bull; Expires {{ \Carbon\Carbon::parse($ban->expire_date)->format('M d, Y') }}
                                            @if(\Carbon\Carbon::parse($ban->expire_date)->isPast())
                                                <span class="text-orange-500 font-semibold">(expired)</span>
                                            @endif
                                        @else
                                            &bull; <span class="text-red-600 font-semibold">Permanent</span>
                                        @endif
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('admin.bans.destroy', $ban->id) }}"
                                      onsubmit="return confirm('Remove this ban?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-500 hover:underline px-2 py-1 border border-red-200 rounded hover:bg-red-50 transition whitespace-nowrap">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($bans->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $bans->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@endsection
