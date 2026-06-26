@extends('layouts.app')

@section('title', 'Register')

@section('content')

<div class="max-w-2xl mx-auto mt-6">

    {{-- Forum rules --}}
    <div class="bg-yellow-50 border border-yellow-200 rounded mb-5 overflow-hidden">
        <div class="bg-yellow-100 border-b border-yellow-200 px-4 py-2 font-semibold text-sm text-yellow-900">
            Forum Rules
        </div>
        <div class="p-4 text-sm text-gray-700 leading-relaxed">
            @if(config('forum.rules'))
                {!! nl2br(e(config('forum.rules'))) !!}
            @else
                <ol class="list-decimal list-inside space-y-1">
                    <li>Be respectful to all members.</li>
                    <li>No spam, advertising, or self-promotion.</li>
                    <li>No hateful, offensive, or inappropriate content.</li>
                    <li>Keep discussions on topic.</li>
                    <li>Use the search function before posting a new topic.</li>
                    <li>The administrators and moderators reserve the right to edit or remove any post.</li>
                </ol>
            @endif
            <div class="mt-3 text-gray-500 text-xs">
                By registering on this forum, you agree to abide by these rules.
            </div>
        </div>
    </div>

    {{-- Registration form --}}
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
            Create Your Account
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Username --}}
                    <div class="md:col-span-2">
                        <label for="username" class="block text-sm font-semibold text-gray-700 mb-1">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="username" name="username"
                               value="{{ old('username') }}"
                               autofocus
                               maxlength="25"
                               autocomplete="username"
                               placeholder="Choose a unique username (max 25 chars)"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('username') ? 'border-red-400' : '' }}">
                        @if($errors->has('username'))
                            <div class="text-red-600 text-xs mt-1">{{ $errors->first('username') }}</div>
                        @endif
                        <div class="text-xs text-gray-400 mt-1">
                            Letters, numbers, underscores, and hyphens only.
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                            Email address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}"
                               autocomplete="email"
                               placeholder="your@email.com"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('email') ? 'border-red-400' : '' }}">
                        @if($errors->has('email'))
                            <div class="text-red-600 text-xs mt-1">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    {{-- Email confirm --}}
                    <div>
                        <label for="email_confirm" class="block text-sm font-semibold text-gray-700 mb-1">
                            Confirm email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email_confirm" name="email_confirm"
                               placeholder="Repeat your email address"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('email_confirm') ? 'border-red-400' : '' }}">
                        @if($errors->has('email_confirm'))
                            <div class="text-red-600 text-xs mt-1">{{ $errors->first('email_confirm') }}</div>
                        @endif
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password" name="password"
                               autocomplete="new-password"
                               placeholder="At least 8 characters"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('password') ? 'border-red-400' : '' }}">
                        @if($errors->has('password'))
                            <div class="text-red-600 text-xs mt-1">{{ $errors->first('password') }}</div>
                        @endif
                    </div>

                    {{-- Password confirm --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">
                            Confirm password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               autocomplete="new-password"
                               placeholder="Repeat your password"
                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
                    </div>
                </div>

                {{-- Agreement --}}
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <label class="flex items-start gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="agree_rules" value="1"
                               {{ old('agree_rules') ? 'checked' : '' }}
                               class="mt-0.5 rounded border-gray-300 text-blue-600 {{ $errors->has('agree_rules') ? 'border-red-400' : '' }}">
                        <span>
                            I have read the forum rules and agree to abide by them.
                        </span>
                    </label>
                    @if($errors->has('agree_rules'))
                        <div class="text-red-600 text-xs mt-1">{{ $errors->first('agree_rules') }}</div>
                    @endif
                </div>

                {{-- Submit --}}
                <div class="mt-5 flex items-center gap-4">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded shadow-sm transition text-sm">
                        Register
                    </button>
                    <span class="text-sm text-gray-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login</a>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
