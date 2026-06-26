@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')

{{-- Breadcrumb --}}
<div class="text-xs text-gray-500 mb-3 flex items-center flex-wrap gap-1">
    <a href="{{ url('/') }}" class="text-blue-600 hover:underline">Index</a>
    <span class="text-gray-400">&raquo;</span>
    <a href="{{ route('profile.show', auth()->id()) }}" class="text-blue-600 hover:underline">{{ auth()->user()->username }}</a>
    <span class="text-gray-400">&raquo;</span>
    <span class="text-gray-700">Edit Profile</span>
</div>

@php $user = auth()->user(); @endphp

{{-- Tab navigation --}}
<div class="flex flex-wrap border-b border-gray-300 mb-0 gap-0" x-data="{ tab: '{{ old('_tab', 'personal') }}' }">

    @foreach([
        ['personal', 'Personal Info'],
        ['messaging', 'Messaging'],
        ['personality', 'Personality'],
        ['display', 'Display'],
        ['password', 'Password'],
    ] as [$tabId, $tabLabel])
        <button type="button"
                onclick="switchTab('{{ $tabId }}')"
                id="tab-btn-{{ $tabId }}"
                class="tab-btn px-4 py-2 text-sm font-semibold border border-b-0 border-gray-300 rounded-t -mb-px mr-1 mt-1 transition
                       {{ old('_tab', 'personal') === $tabId ? 'bg-white text-blue-700 border-blue-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-50' }}">
            {{ $tabLabel }}
        </button>
    @endforeach
</div>

<form method="POST" action="{{ route('profile.update', $user->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="_tab" id="active-tab" value="{{ old('_tab', 'personal') }}">

    {{-- === Personal Info Tab === --}}
    <div id="tab-personal" class="tab-content bg-white border border-gray-200 rounded-b rounded-tr p-6 space-y-4
                                  {{ old('_tab', 'personal') === 'personal' ? '' : 'hidden' }}">
        <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">Personal Information</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="realname" class="block text-sm font-semibold text-gray-700 mb-1">Real name</label>
                <input type="text" id="realname" name="realname"
                       value="{{ old('realname', $user->realname) }}"
                       maxlength="40"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
            </div>
            <div>
                <label for="location" class="block text-sm font-semibold text-gray-700 mb-1">Location</label>
                <input type="text" id="location" name="location"
                       value="{{ old('location', $user->location) }}"
                       maxlength="30"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
            </div>
            <div class="md:col-span-2">
                <label for="url" class="block text-sm font-semibold text-gray-700 mb-1">Website URL</label>
                <input type="url" id="url" name="url"
                       value="{{ old('url', $user->url) }}"
                       maxlength="100"
                       placeholder="https://your-website.com"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
            </div>
        </div>
    </div>

    {{-- === Messaging Tab === --}}
    <div id="tab-messaging" class="tab-content bg-white border border-gray-200 rounded-b rounded-tr p-6 space-y-4
                                   {{ old('_tab') === 'messaging' ? '' : 'hidden' }}">
        <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">Messaging &amp; Email Settings</h3>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                Email address <span class="text-red-500">*</span>
            </label>
            <input type="email" id="email" name="email"
                   value="{{ old('email', $user->email) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('email') ? 'border-red-400' : '' }}">
            @if($errors->has('email'))
                <div class="text-red-600 text-xs mt-1">{{ $errors->first('email') }}</div>
            @endif
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email visibility</label>
            <div class="space-y-1.5">
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input type="radio" name="email_setting" value="0"
                           {{ old('email_setting', $user->email_setting) == '0' ? 'checked' : '' }}
                           class="text-blue-600">
                    Display email to everyone
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input type="radio" name="email_setting" value="1"
                           {{ old('email_setting', $user->email_setting) == '1' ? 'checked' : '' }}
                           class="text-blue-600">
                    Hide email but allow contact form
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                    <input type="radio" name="email_setting" value="2"
                           {{ old('email_setting', $user->email_setting) == '2' ? 'checked' : '' }}
                           class="text-blue-600">
                    Hide email completely (no contact form)
                </label>
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="notify_with_post" value="1"
                       {{ old('notify_with_post', $user->notify_with_post) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600">
                Include post content in subscription email notifications
            </label>
        </div>

        <div>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="auto_subscribe" value="1"
                       {{ old('auto_subscribe', $user->auto_subscribe) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600">
                Automatically subscribe to topics I post in
            </label>
        </div>
    </div>

    {{-- === Personality Tab === --}}
    <div id="tab-personality" class="tab-content bg-white border border-gray-200 rounded-b rounded-tr p-6 space-y-5
                                     {{ old('_tab') === 'personality' ? '' : 'hidden' }}">
        <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">Personality Settings</h3>

        {{-- Avatar --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Avatar</label>
            @if($user->avatar)
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ $user->avatar }}" alt="Current avatar"
                         class="w-16 h-16 rounded border border-gray-200 object-cover">
                    <div>
                        <div class="text-xs text-gray-500">Current avatar</div>
                        <label class="flex items-center gap-1.5 text-sm text-red-600 cursor-pointer mt-1">
                            <input type="checkbox" name="delete_avatar" value="1"
                                   class="rounded border-gray-300">
                            Delete current avatar
                        </label>
                    </div>
                </div>
            @endif
            <div class="space-y-2">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Upload new avatar (max 60×60 px, 10 KB):</label>
                    <input type="file" name="avatar_file" accept="image/jpeg,image/gif,image/png"
                           class="text-sm text-gray-600 file:mr-3 file:py-1 file:px-3 file:rounded file:border file:border-gray-300 file:text-sm file:bg-gray-50 file:cursor-pointer hover:file:bg-gray-100">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Or enter avatar URL:</label>
                    <input type="url" name="avatar_url"
                           value="{{ old('avatar_url') }}"
                           placeholder="https://example.com/avatar.png"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                </div>
            </div>
        </div>

        {{-- Signature --}}
        <div>
            <label for="signature" class="block text-sm font-semibold text-gray-700 mb-1">
                Signature
                <span class="text-gray-400 font-normal">(max 512 chars, BBCode allowed)</span>
            </label>
            <div class="flex flex-wrap gap-1 mb-2">
                @foreach([['B','bold'],['I','italic'],['U','underline'],['URL','url']] as [$label,$tag])
                    <button type="button" onclick="insertBBCode('signature','{{ $tag }}')"
                            class="px-2 py-0.5 text-xs border border-gray-300 rounded bg-gray-50 hover:bg-gray-100 font-semibold">{{ $label }}</button>
                @endforeach
            </div>
            <textarea id="signature" name="signature" rows="5" maxlength="512"
                      class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono focus:outline-none focus:border-blue-400 resize-y">{{ old('signature', $user->signature) }}</textarea>
            @if($errors->has('signature'))
                <div class="text-red-600 text-xs mt-1">{{ $errors->first('signature') }}</div>
            @endif
        </div>
    </div>

    {{-- === Display Tab === --}}
    <div id="tab-display" class="tab-content bg-white border border-gray-200 rounded-b rounded-tr p-6 space-y-4
                                 {{ old('_tab') === 'display' ? '' : 'hidden' }}">
        <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">Display Preferences</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="timezone" class="block text-sm font-semibold text-gray-700 mb-1">Timezone</label>
                <select id="timezone" name="timezone"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                    @foreach(timezone_identifiers_list() as $tz)
                        <option value="{{ $tz }}" {{ old('timezone', $user->timezone) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="language" class="block text-sm font-semibold text-gray-700 mb-1">Language</label>
                <select id="language" name="language"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                    <option value="English" {{ old('language', $user->language) === 'English' ? 'selected' : '' }}>English</option>
                    <option value="French" {{ old('language', $user->language) === 'French' ? 'selected' : '' }}>French</option>
                    <option value="German" {{ old('language', $user->language) === 'German' ? 'selected' : '' }}>German</option>
                    <option value="Spanish" {{ old('language', $user->language) === 'Spanish' ? 'selected' : '' }}>Spanish</option>
                </select>
            </div>

            <div>
                <label for="posts_per_page" class="block text-sm font-semibold text-gray-700 mb-1">Posts per page</label>
                <select id="posts_per_page" name="posts_per_page"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                    @foreach([10, 15, 25, 50] as $n)
                        <option value="{{ $n }}" {{ old('posts_per_page', $user->posts_per_page ?? 25) == $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="topics_per_page" class="block text-sm font-semibold text-gray-700 mb-1">Topics per page</label>
                <select id="topics_per_page" name="topics_per_page"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                    @foreach([10, 15, 25, 50] as $n)
                        <option value="{{ $n }}" {{ old('topics_per_page', $user->topics_per_page ?? 25) == $n ? 'selected' : '' }}>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="space-y-2 pt-2">
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="show_avatars" value="1"
                       {{ old('show_avatars', $user->show_avatars ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600">
                Show avatars
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="show_sig" value="1"
                       {{ old('show_sig', $user->show_sig ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600">
                Show signatures
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="show_smilies" value="1"
                       {{ old('show_smilies', $user->show_smilies ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600">
                Show smilies
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input type="checkbox" name="show_img" value="1"
                       {{ old('show_img', $user->show_img ?? true) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600">
                Show images in posts
            </label>
        </div>
    </div>

    {{-- === Password Tab === --}}
    <div id="tab-password" class="tab-content bg-white border border-gray-200 rounded-b rounded-tr p-6 space-y-4
                                  {{ old('_tab') === 'password' ? '' : 'hidden' }}">
        <h3 class="font-semibold text-gray-700 text-sm border-b border-gray-100 pb-2">Change Password</h3>
        <div class="text-sm text-gray-600 bg-blue-50 border border-blue-200 rounded px-3 py-2">
            Leave these fields blank if you do not wish to change your password.
        </div>

        <div class="max-w-sm space-y-4">
            <div>
                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1">Current password</label>
                <input type="password" id="current_password" name="current_password"
                       autocomplete="current-password"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('current_password') ? 'border-red-400' : '' }}">
                @if($errors->has('current_password'))
                    <div class="text-red-600 text-xs mt-1">{{ $errors->first('current_password') }}</div>
                @endif
            </div>

            <div>
                <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-1">New password</label>
                <input type="password" id="new_password" name="new_password"
                       autocomplete="new-password"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400 {{ $errors->has('new_password') ? 'border-red-400' : '' }}">
                @if($errors->has('new_password'))
                    <div class="text-red-600 text-xs mt-1">{{ $errors->first('new_password') }}</div>
                @endif
            </div>

            <div>
                <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirm new password</label>
                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                       autocomplete="new-password"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
            </div>
        </div>
    </div>

    {{-- Save button (always visible) --}}
    <div class="mt-4 flex items-center gap-3">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded shadow-sm transition">
            Save Changes
        </button>
        <a href="{{ route('profile.show', auth()->id()) }}"
           class="text-sm text-gray-600 hover:underline">
            Cancel
        </a>
    </div>
</form>

@endsection

@push('scripts')
<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-white', 'text-blue-700', 'border-blue-300');
        btn.classList.add('bg-gray-100', 'text-gray-600');
    });

    document.getElementById('tab-' + tabId).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-btn-' + tabId);
    activeBtn.classList.add('bg-white', 'text-blue-700', 'border-blue-300');
    activeBtn.classList.remove('bg-gray-100', 'text-gray-600');
    document.getElementById('active-tab').value = tabId;
}

function insertBBCode(textareaId, tag) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) return;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selected = textarea.value.substring(start, end);
    const open = '[' + tag + ']', close = '[/' + tag + ']';
    const replacement = open + selected + close;
    textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
    textarea.selectionStart = start + open.length;
    textarea.selectionEnd = start + open.length + selected.length;
    textarea.focus();
}
</script>
@endpush
