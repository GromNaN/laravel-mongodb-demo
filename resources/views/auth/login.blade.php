@extends('layouts.app')

@section('title', 'Login')

@section('content')

<div class="max-w-md mx-auto mt-8">
    <div class="bg-white border border-gray-200 rounded overflow-hidden">
        <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
            Login to {{ config('app.name', 'FluxBB') }}
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Username or Email --}}
                <div class="mb-4">
                    <label for="login" class="block text-sm font-semibold text-gray-700 mb-1">
                        Username or Email <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="login" name="login"
                           value="{{ old('login') }}"
                           autofocus
                           autocomplete="username"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('login') ? 'border-red-400' : '' }}">
                    @if($errors->has('login'))
                        <div class="text-red-600 text-xs mt-1">{{ $errors->first('login') }}</div>
                    @endif
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password"
                           autocomplete="current-password"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('password') ? 'border-red-400' : '' }}">
                    @if($errors->has('password'))
                        <div class="text-red-600 text-xs mt-1">{{ $errors->first('password') }}</div>
                    @endif
                </div>

                {{-- Remember me --}}
                <div class="mb-5">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" value="1"
                               {{ old('remember') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        Remember me for 2 weeks
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow-sm transition text-sm">
                    Login
                </button>
            </form>

            {{-- Links --}}
            <div class="mt-4 pt-4 border-t border-gray-100 text-sm text-center space-y-2">
                <div>
                    <span class="text-gray-400">
                        Forgot your password? Contact an administrator.
                    </span>
                </div>
                <div class="text-gray-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-semibold">
                        Register now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
