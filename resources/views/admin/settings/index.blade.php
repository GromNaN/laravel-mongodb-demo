@extends('admin.layout')

@section('title', 'Admin – Settings')

@section('admin-content')

<h1 class="text-xl font-bold text-gray-800 mb-4">Board Settings</h1>

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    <div class="space-y-5">

        {{-- ========== Essential Settings ========== --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
                Essential Settings
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label for="board_title" class="block text-sm font-semibold text-gray-700 mb-1">
                        Board Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="board_title" name="board_title"
                           value="{{ old('board_title', $settings['board_title'] ?? config('app.name')) }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label for="board_desc" class="block text-sm font-semibold text-gray-700 mb-1">Board Description</label>
                    <input type="text" id="board_desc" name="board_desc"
                           value="{{ old('board_desc', $settings['board_desc'] ?? '') }}"
                           placeholder="Short description of this forum"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label for="admin_email" class="block text-sm font-semibold text-gray-700 mb-1">
                        Admin Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="admin_email" name="admin_email"
                           value="{{ old('admin_email', $settings['admin_email'] ?? '') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label for="webmaster_email" class="block text-sm font-semibold text-gray-700 mb-1">Webmaster Email</label>
                    <input type="email" id="webmaster_email" name="webmaster_email"
                           value="{{ old('webmaster_email', $settings['webmaster_email'] ?? '') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label for="base_url" class="block text-sm font-semibold text-gray-700 mb-1">Base URL</label>
                    <input type="url" id="base_url" name="base_url"
                           value="{{ old('base_url', $settings['base_url'] ?? config('app.url')) }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label for="default_lang" class="block text-sm font-semibold text-gray-700 mb-1">Default Language</label>
                    <select id="default_lang" name="default_lang"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        @foreach(['English', 'French', 'German', 'Spanish', 'Portuguese', 'Dutch', 'Russian', 'Chinese'] as $lang)
                            <option value="{{ $lang }}" {{ old('default_lang', $settings['default_lang'] ?? 'English') === $lang ? 'selected' : '' }}>
                                {{ $lang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="default_timezone" class="block text-sm font-semibold text-gray-700 mb-1">Default Timezone</label>
                    <select id="default_timezone" name="default_timezone"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        @foreach(timezone_identifiers_list() as $tz)
                            <option value="{{ $tz }}" {{ old('default_timezone', $settings['default_timezone'] ?? 'UTC') === $tz ? 'selected' : '' }}>
                                {{ $tz }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="announcement" class="block text-sm font-semibold text-gray-700 mb-1">
                        Announcement <span class="text-gray-400 font-normal">(shown at top of forum index)</span>
                    </label>
                    <textarea id="announcement" name="announcement" rows="3"
                              placeholder="Leave blank to disable announcement"
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 resize-y">{{ old('announcement', $settings['announcement'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ========== Features ========== --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
                Features
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-3">
                    @foreach([
                        ['allow_registration', 'Allow new user registrations'],
                        ['allow_posting', 'Allow posting (all forums)'],
                        ['allow_search', 'Enable search function'],
                        ['allow_email_notify', 'Allow email subscription notifications'],
                        ['show_online', 'Show online users list'],
                        ['maintenance', 'Maintenance mode (board offline for guests)'],
                        ['require_email_confirm', 'Require email confirmation on registration'],
                        ['allow_guest_view', 'Allow guests to view the board'],
                        ['allow_pm', 'Enable private messages'],
                        ['censoring_enabled', 'Enable word censoring'],
                    ] as [$key, $label])
                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="{{ $key }}" value="1"
                                   {{ old($key, $settings[$key] ?? true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ========== Display ========== --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
                Display Settings
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="topics_per_page" class="block text-sm font-semibold text-gray-700 mb-1">Topics per page</label>
                    <select id="topics_per_page" name="topics_per_page"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        @foreach([10, 15, 25, 50, 75] as $n)
                            <option value="{{ $n }}" {{ old('topics_per_page', $settings['topics_per_page'] ?? 25) == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="posts_per_page" class="block text-sm font-semibold text-gray-700 mb-1">Posts per page</label>
                    <select id="posts_per_page" name="posts_per_page"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        @foreach([10, 15, 25, 50, 75] as $n)
                            <option value="{{ $n }}" {{ old('posts_per_page', $settings['posts_per_page'] ?? 25) == $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="user_online_timeout" class="block text-sm font-semibold text-gray-700 mb-1">
                        Online timeout (minutes)
                    </label>
                    <input type="number" id="user_online_timeout" name="user_online_timeout"
                           value="{{ old('user_online_timeout', $settings['user_online_timeout'] ?? 30) }}"
                           min="5" max="120"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                    <div class="text-xs text-gray-400 mt-1">How long before a user is considered offline.</div>
                </div>

                <div>
                    <label for="max_post_size" class="block text-sm font-semibold text-gray-700 mb-1">
                        Max post size (bytes)
                    </label>
                    <input type="number" id="max_post_size" name="max_post_size"
                           value="{{ old('max_post_size', $settings['max_post_size'] ?? 32768) }}"
                           min="1024"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                    <div class="text-xs text-gray-400 mt-1">32768 = 32 KB (recommended default)</div>
                </div>

                <div>
                    <label for="avatar_max_size" class="block text-sm font-semibold text-gray-700 mb-1">
                        Avatar max size (bytes)
                    </label>
                    <input type="number" id="avatar_max_size" name="avatar_max_size"
                           value="{{ old('avatar_max_size', $settings['avatar_max_size'] ?? 10240) }}"
                           min="1024"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label for="avatar_max_width" class="block text-sm font-semibold text-gray-700 mb-1">Avatar max width (px)</label>
                    <input type="number" id="avatar_max_width" name="avatar_max_width"
                           value="{{ old('avatar_max_width', $settings['avatar_max_width'] ?? 60) }}"
                           min="16" max="200"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="show_post_count" value="1"
                               {{ old('show_post_count', $settings['show_post_count'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        Show post count on forum index
                    </label>
                </div>
            </div>
        </div>

        {{-- ========== Email / SMTP ========== --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
                Email Settings
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="mail_function" class="block text-sm font-semibold text-gray-700 mb-1">Mail transport</label>
                    <select id="mail_function" name="mail_function"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                        <option value="mail" {{ old('mail_function', $settings['mail_function'] ?? 'mail') === 'mail' ? 'selected' : '' }}>PHP mail()</option>
                        <option value="smtp" {{ old('mail_function', $settings['mail_function'] ?? '') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                        <option value="sendmail" {{ old('mail_function', $settings['mail_function'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                        <option value="log" {{ old('mail_function', $settings['mail_function'] ?? '') === 'log' ? 'selected' : '' }}>Log to file (debug)</option>
                    </select>
                </div>

                <div>
                    <label for="smtp_host" class="block text-sm font-semibold text-gray-700 mb-1">SMTP Host</label>
                    <input type="text" id="smtp_host" name="smtp_host"
                           value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}"
                           placeholder="smtp.example.com"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label for="smtp_user" class="block text-sm font-semibold text-gray-700 mb-1">SMTP Username</label>
                    <input type="text" id="smtp_user" name="smtp_user"
                           value="{{ old('smtp_user', $settings['smtp_user'] ?? '') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label for="smtp_pass" class="block text-sm font-semibold text-gray-700 mb-1">SMTP Password</label>
                    <input type="password" id="smtp_pass" name="smtp_pass"
                           placeholder="Leave blank to keep current"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label for="smtp_port" class="block text-sm font-semibold text-gray-700 mb-1">SMTP Port</label>
                    <input type="number" id="smtp_port" name="smtp_port"
                           value="{{ old('smtp_port', $settings['smtp_port'] ?? 587) }}"
                           min="1" max="65535"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer mt-6">
                        <input type="checkbox" name="smtp_ssl" value="1"
                               {{ old('smtp_ssl', $settings['smtp_ssl'] ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600">
                        Use SSL/TLS for SMTP
                    </label>
                </div>
            </div>
        </div>

        {{-- ========== Registration ========== --}}
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <div class="bg-forum-header text-white px-4 py-2 font-semibold text-sm">
                Registration Settings
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="min_pass_len" class="block text-sm font-semibold text-gray-700 mb-1">Minimum password length</label>
                    <input type="number" id="min_pass_len" name="min_pass_len"
                           value="{{ old('min_pass_len', $settings['min_pass_len'] ?? 8) }}"
                           min="4" max="32"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>

                <div>
                    <label for="post_wait_time" class="block text-sm font-semibold text-gray-700 mb-1">
                        Post flood time (seconds)
                    </label>
                    <input type="number" id="post_wait_time" name="post_wait_time"
                           value="{{ old('post_wait_time', $settings['post_wait_time'] ?? 30) }}"
                           min="0"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                    <div class="text-xs text-gray-400 mt-1">Minimum seconds between posts (0 = no limit).</div>
                </div>

                <div class="md:col-span-2">
                    <label for="forum_rules" class="block text-sm font-semibold text-gray-700 mb-1">Forum Rules</label>
                    <textarea id="forum_rules" name="forum_rules" rows="6"
                              placeholder="Enter forum rules shown during registration..."
                              class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 resize-y">{{ old('forum_rules', $settings['forum_rules'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

    </div>

    {{-- Submit --}}
    <div class="mt-5 flex items-center gap-3">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-2 rounded shadow-sm transition text-sm">
            Save All Settings
        </button>
    </div>
</form>

@endsection
